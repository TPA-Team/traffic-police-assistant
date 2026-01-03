from faster_whisper import WhisperModel
import pika, requests, tempfile, os, json, time, threading

RABBIT_HOST = "127.0.0.1"
EXCHANGE = "ai.exchange"
QUEUE = "ai.stt.jobs"
ROUTING_KEY = "job.stt.create"
RESULT_KEY = "job.result"

print("[STT] Loading Whisper model...")
model = WhisperModel("small", device="cpu", compute_type="int8")
print("[STT] Model loaded ✅")


def download_audio(url: str) -> str:
    r = requests.get(url, timeout=60)
    r.raise_for_status()

    fd, path = tempfile.mkstemp(suffix=".audio")
    with os.fdopen(fd, "wb") as f:
        f.write(r.content)

    return path


def process_job(msg: dict) -> dict:
    audio_url = msg["payload"]["audio_url"]
    job_id = msg["job_id"]

    audio_path = download_audio(audio_url)

    segments, info = model.transcribe(
        audio_path,
        language="ar",
        task="transcribe",
        vad_filter=True,
        vad_parameters={"min_silence_duration_ms": 300},
    )

    text = " ".join(seg.text for seg in segments).strip()
    os.remove(audio_path)

    return {
        "job_id": job_id,
        "text": text,
        "language": getattr(info, "language", None),
    }


def main():
    conn = pika.BlockingConnection(pika.ConnectionParameters(RABBIT_HOST))
    ch = conn.channel()

    ch.exchange_declare(exchange=EXCHANGE, exchange_type="direct", durable=True)
    ch.queue_declare(queue=QUEUE, durable=True)
    ch.queue_bind(queue=QUEUE, exchange=EXCHANGE, routing_key=ROUTING_KEY)

    def on_message(ch, method, props, body):
        msg = json.loads(body)
        try:
            result = process_job(msg)

            ch.basic_publish(
                exchange=EXCHANGE,
                routing_key=RESULT_KEY,
                body=json.dumps({
                    "job_id": result["job_id"],
                    "status": "success",
                    "result": result,
                    "error": None
                }, ensure_ascii=False).encode(),
            )

            ch.basic_ack(method.delivery_tag)

        except Exception as e:
            ch.basic_publish(
                exchange=EXCHANGE,
                routing_key=RESULT_KEY,
                body=json.dumps({
                    "job_id": msg.get("job_id"),
                    "status": "error",
                    "result": None,
                    "error": str(e)
                }).encode(),
            )
            ch.basic_ack(method.delivery_tag)

    ch.basic_consume(queue=QUEUE, on_message_callback=on_message)
    print("[STT] Listening... queue=ai.stt.jobs rk=job.stt.create")
    ch.start_consuming()


if __name__ == "__main__":
    main()