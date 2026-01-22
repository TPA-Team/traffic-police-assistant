import os
os.environ["CUDA_VISIBLE_DEVICES"] = ""  # 🔒 CPU فقط


import pika, requests, tempfile, json, time
import whisper

# ================= RabbitMQ Config =================
RABBIT_HOST = "172.20.10.5"
RABBIT_PORT = 5672
RABBIT_USER = "ai_user"
RABBIT_PASS = "ai_1221"
RABBIT_VHOST = "/"

EXCHANGE = "ai.exchange"
QUEUE = "ai.stt.jobs"
ROUTING_KEY = "job.stt.create"
RESULT_KEY = "job.result"

# ================= Load Model =================
print("[STT] Loading Whisper model (CPU)...")
model = whisper.load_model("small")  # تحميل نموذج Whisper العادي
print("[STT] Model loaded ✅")


# ================= Helpers =================
def download_audio(url: str) -> str:
    r = requests.get(url, timeout=60)
    r.raise_for_status()

    fd, path = tempfile.mkstemp(suffix=".ogg")
    with os.fdopen(fd, "wb") as f:
        f.write(r.content)

    return path


def process_job(msg: dict) -> dict:
    audio_url = msg["payload"]["audio_url"]
    job_id = msg["job_id"]

    audio_path = download_audio(audio_url)

    result = model.transcribe(audio_path, language="ar")  # استخدام transcribe من نموذج Whisper العادي
    text = result['text'].strip()
    os.remove(audio_path)

    return {
        "job_id": job_id,
        "text": text,
        "language": result.get("language", None),
    }


# ================= Main =================
def main():
    credentials = pika.PlainCredentials(RABBIT_USER, RABBIT_PASS)
    params = pika.ConnectionParameters(
        host=RABBIT_HOST,
        port=RABBIT_PORT,
        virtual_host=RABBIT_VHOST,
        credentials=credentials,
    )

    conn = pika.BlockingConnection(params)
    ch = conn.channel()

    ch.exchange_declare(exchange=EXCHANGE, exchange_type="direct", durable=True)
    ch.queue_declare(queue=QUEUE, durable=True)
    ch.queue_bind(queue=QUEUE, exchange=EXCHANGE, routing_key=ROUTING_KEY)

    def on_message(ch, method, props, body):
        print("[STT] RAW MESSAGE:", body.decode())

        msg = json.loads(body)

        # ✅ عرّفي job_id من الرسالة
        job_id = msg["job_id"]

        print(f"[STT] 🎧 Job {job_id}")

        try:
            result = process_job(msg)

            ch.basic_publish(
                exchange=EXCHANGE,
                routing_key=RESULT_KEY,
                body=json.dumps({
                    "job_id": job_id,
                    "status": "success",
                    "result": result,
                    "error": None
                }, ensure_ascii=False).encode(),
            )

            ch.basic_ack(method.delivery_tag)
            print(f"[STT] ✅ Job {job_id} done")

        except Exception as e:
            ch.basic_publish(
                exchange=EXCHANGE,
                routing_key=RESULT_KEY,
                body=json.dumps({
                    "job_id": job_id,
                    "status": "error",
                    "result": None,
                    "error": str(e)
                }, ensure_ascii=False).encode(),
            )
            ch.basic_ack(method.delivery_tag)
            print(f"[STT] ❌ Job {job_id} failed:", e)

    ch.basic_qos(prefetch_count=1)
    ch.basic_consume(queue=QUEUE, on_message_callback=on_message)

    print("[STT] 🚀 Listening... queue=ai.stt.jobs rk=job.stt.create")
    ch.start_consuming()

if __name__ == "__main__":
    main()