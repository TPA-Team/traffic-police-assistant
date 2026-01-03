import os, json, time, base64, re, threading
import pika, requests
from pymongo import MongoClient
from bson import ObjectId
from pika.exceptions import AMQPConnectionError, StreamLostError
import cv2

# ================== Encode image EXACT size (no resize) ==================
def b64_jpeg_no_resize(img_bgr, quality: int = 95) -> str:
    ok, buf = cv2.imencode(".jpg", img_bgr, [int(cv2.IMWRITE_JPEG_QUALITY), quality])
    if not ok:
        raise RuntimeError("Failed to encode JPEG")
    return base64.b64encode(buf.tobytes()).decode("utf-8")

def load_full_image_data_uri(image_path: str) -> str:
    img = cv2.imread(image_path)
    if img is None:
        raise RuntimeError(f"Cannot read image: {image_path}")
    b64 = b64_jpeg_no_resize(img, quality=95)
    return f"data:image/jpeg;base64,{b64}"

# ================== RabbitMQ (from env like Laravel) ==================
RABBIT_HOST = os.getenv("RABBITMQ_HOST", "127.0.0.1")
RABBIT_PORT = int(os.getenv("RABBITMQ_PORT", "5672"))
RABBIT_USER = os.getenv("RABBITMQ_USER", "admin")
RABBIT_PASS = os.getenv("RABBITMQ_PASSWORD", "admin123")
RABBIT_VHOST = os.getenv("RABBITMQ_VHOST", "/")

EXCHANGE = os.getenv("AI_RMQ_EXCHANGE", "ai.exchange")

# ✅ OCR queue + routing key
JOBS_QUEUE = os.getenv("AI_RMQ_OCR_QUEUE", "ai.ocr.jobs")
JOBS_ROUTING_KEY = os.getenv("AI_RMQ_OCR_ROUTING_KEY", "job.ocr.create")

# ✅ results
RESULTS_ROUTING_KEY = os.getenv("AI_RMQ_RESULTS_ROUTING_KEY", "job.result")

# ================== MongoDB ==================
MONGO_URL = os.getenv("MONGO_URL", "mongodb://127.0.0.1:27017")
mongo = MongoClient(MONGO_URL)
db = mongo["ai_service"]
results_col = db["plate_ocr_results"]

# ================== Ollama ==================
OLLAMA_URL = os.getenv("OLLAMA_URL", "http://127.0.0.1:11434/v1/chat/completions")
OLLAMA_MODEL = os.getenv("OLLAMA_MODEL", "qwen2.5vl:3b")

print("OLLAMA_URL =", OLLAMA_URL)
print("OLLAMA_MODEL =", OLLAMA_MODEL)
print("EXCHANGE =", EXCHANGE)
print("JOBS_QUEUE =", JOBS_QUEUE)
print("JOBS_ROUTING_KEY =", JOBS_ROUTING_KEY)

# ================== JSON Helpers ==================
def to_jsonable(obj):
    if isinstance(obj, ObjectId):
        return str(obj)
    if isinstance(obj, dict):
        return {k: to_jsonable(v) for k, v in obj.items()}
    if isinstance(obj, (list, tuple)):
        return [to_jsonable(x) for x in obj]
    return obj

def extract_model_text(data: dict) -> str:
    try:
        choices = data.get("choices")
        if isinstance(choices, list) and choices:
            msg = choices[0].get("message") or {}
            txt = msg.get("content", "")
            if isinstance(txt, str):
                return txt.strip()
    except Exception:
        pass
    try:
        msg = data.get("message") or {}
        txt = msg.get("content", "")
        if isinstance(txt, str):
            return txt.strip()
    except Exception:
        pass
    resp = data.get("response")
    return resp.strip() if isinstance(resp, str) else ""

def parse_json_from_text(text: str) -> dict:
    text = (text or "").strip()
    if not text:
        raise ValueError("Empty model response")
    text = re.sub(r"^```(?:json)?\s*", "", text, flags=re.IGNORECASE)
    text = re.sub(r"\s*```$", "", text)
    if text.startswith("{") and text.endswith("}"):
        return json.loads(text)
    m = re.search(r"\{.*\}", text, flags=re.DOTALL)
    if not m:
        raise ValueError(f"No JSON found in model response: {text!r}")
    return json.loads(m.group(0))

def normalize_out(d: dict) -> dict:
    if not isinstance(d, dict):
        d = {}
    plate = (d.get("plate_number") or "").strip()
    model = (d.get("model") or "").strip()   # storing MAKE here
    color = (d.get("color") or "").strip()

    plate = re.sub(r"\s+", " ", plate).strip()
    model = re.sub(r"\s+", " ", model).strip()
    color = re.sub(r"\s+", " ", color).strip()

    # keep Arabic/Latin/nums/spaces/-  (للنمر السورية + اسم المحافظة)
    plate = re.sub(r"[^\u0600-\u06FF0-9A-Za-z\s\-]", "", plate).strip()
    return {"plate_number": plate, "model": model, "color": color}

def _call_ollama_json(prompt_text: str, data_uri: str, num_predict: int = 220, num_ctx: int = 3072) -> dict:
    payload = {
        "model": OLLAMA_MODEL,
        "messages": [{
            "role": "user",
            "content": [
                {"type": "text", "text": prompt_text},
                {"type": "image_url", "image_url": {"url": data_uri}},
            ],
        }],
        "stream": False,
        "options": {"temperature": 0, "num_predict": num_predict, "num_ctx": num_ctx},
        "response_format": {"type": "json_object"},
        "keep_alive": "10m",
    }
    r = requests.post(OLLAMA_URL, json=payload, timeout=(10, 300))
    if r.status_code >= 400:
        raise RuntimeError(f"Ollama HTTP {r.status_code}: {r.text}")
    data = r.json()
    text = extract_model_text(data)
    return parse_json_from_text(text)

# ================== OCR ==================
def ocr_vehicle(image_path: str) -> dict:
    data_uri = load_full_image_data_uri(image_path)

    prompt = (
        'Return ONLY JSON with EXACT keys: {"plate_number":"","model":"","color":""}. '
        'Rules:\n'
        '1) plate_number: read the plate EXACTLY as shown, including Arabic governorate name if present (e.g., "دمشق 12345" or "حلب ٧٨٩١"). '
        'Return a single string.\n'
        '2) model: store the CAR MAKE/BRAND only (e.g., "Hyundai", "Toyota", "Kia"). If not sure return "".\n'
        '3) color: dominant body color as one simple word (white/black/silver/gray/red/blue/green/yellow/brown/beige/gold/orange). If unsure return "".\n'
        '4) Do NOT add extra keys. Do NOT add any extra text.'
    )

    obj = _call_ollama_json(prompt, data_uri)
    return normalize_out(obj)

# ================== Rabbit Helpers ==================
def rabbit_connection():
    creds = pika.PlainCredentials(RABBIT_USER, RABBIT_PASS)
    params = pika.ConnectionParameters(
        host=RABBIT_HOST,
        port=RABBIT_PORT,
        virtual_host=RABBIT_VHOST,
        credentials=creds,
        heartbeat=300,
        blocked_connection_timeout=600,
        socket_timeout=30,
        connection_attempts=10,
        retry_delay=5,
    )
    return pika.BlockingConnection(params)

# ================== Worker job handler ==================
def handle_job(msg: dict) -> dict:
    job_id = msg["job_id"]
    image_path = msg["payload"]["local_image_path"]

    ocr_result = ocr_vehicle(image_path)

    doc = {
        "job_id": job_id,
        "plate_number": ocr_result.get("plate_number", ""),
        "model": ocr_result.get("model", ""),   # MAKE stored here
        "color": ocr_result.get("color", ""),
        "image_path": image_path,
        "created_at": time.time(),
    }

    ins = results_col.insert_one(doc)

    return {
        "job_id": job_id,
        "plate_number": doc["plate_number"],
        "model": doc["model"],
        "color": doc["color"],
        "image_path": doc["image_path"],
        "created_at": doc["created_at"],
        "mongo_id": str(ins.inserted_id),
    }

# ================== main consume loop ==================
def consume_forever():
    conn = rabbit_connection()
    ch = conn.channel()

    ch.exchange_declare(exchange=EXCHANGE, exchange_type="direct", durable=True)
    ch.queue_declare(queue=JOBS_QUEUE, durable=True)
    ch.queue_bind(queue=JOBS_QUEUE, exchange=EXCHANGE, routing_key=JOBS_ROUTING_KEY)

    ch.basic_qos(prefetch_count=1)

    def publish_result(payload: dict, correlation_id: str | None):
        if not ch.is_open:
            return
        ch.basic_publish(
            exchange=EXCHANGE,
            routing_key=RESULTS_ROUTING_KEY,
            body=json.dumps(to_jsonable(payload), ensure_ascii=False).encode("utf-8"),
            properties=pika.BasicProperties(
                content_type="application/json",
                delivery_mode=2,
                correlation_id=correlation_id,
            ),
        )

    def ack(delivery_tag: int):
        if ch.is_open:
            ch.basic_ack(delivery_tag)

    def nack(delivery_tag: int, requeue: bool = False):
        if ch.is_open:
            ch.basic_nack(delivery_tag, requeue=requeue)

    def worker_thread(delivery_tag: int, props, body_bytes: bytes):
        correlation_id = getattr(props, "correlation_id", None)
        job_id = None
        try:
            msg = json.loads(body_bytes)
            job_id = msg.get("job_id")
            print("JOB:", job_id)

            result_payload = handle_job(msg)

            success_payload = {
                "job_id": result_payload["job_id"],
                "status": "success",
                "result": result_payload,
                "error": None,
            }

            conn.add_callback_threadsafe(lambda: publish_result(success_payload, correlation_id))
            conn.add_callback_threadsafe(lambda: ack(delivery_tag))

        except Exception as e:
            err_payload = {
                "job_id": job_id,
                "status": "error",
                "result": None,
                "error": str(e),
            }
            try:
                conn.add_callback_threadsafe(lambda: publish_result(err_payload, correlation_id))
            except Exception:
                pass
            try:
                conn.add_callback_threadsafe(lambda: nack(delivery_tag, requeue=False))
            except Exception:
                pass

    def on_message(ch, method, props, body):
        t = threading.Thread(target=worker_thread, args=(method.delivery_tag, props, body), daemon=True)
        t.start()

    ch.basic_consume(queue=JOBS_QUEUE, on_message_callback=on_message, auto_ack=False)
    print("AI OCR worker listening... (ONE IMAGE -> ONE OLLAMA CALL | NO PROCESSING)")
    ch.start_consuming()

def main():
    while True:
        try:
            consume_forever()
        except KeyboardInterrupt:
            print("Stopping worker...")
            break
        except (AMQPConnectionError, StreamLostError, ConnectionResetError, OSError) as e:
            print("RabbitMQ connection lost. Reconnecting...", repr(e))
            time.sleep(3)
        except Exception as e:
            print("Unexpected error. Restarting consumer...", repr(e))
            time.sleep(3)

if __name__ == "__main__":
    main()
