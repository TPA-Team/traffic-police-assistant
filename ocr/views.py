import base64, re, requests
from rest_framework.decorators import api_view
from rest_framework.response import Response
from django.conf import settings

@api_view(['POST'])
def read_plate(request):
    if 'image' not in request.FILES:
        return Response({"error": "Image required"}, status=400)

    image_bytes = request.FILES['image'].read()
    
    image_b64 = base64.b64encode(image_bytes).decode('utf-8')

    payload = {
        "model": "qwen3-vl:2b",
        "prompt": "Give  the license plate number in the image.",
        "images": [image_b64],
        "stream": False
    }

    URL = f"http://172.20.10.2:11434/api/generate"  

    try:
        resp = requests.post(URL, json=payload, timeout=120)

        print(">>> status:", resp.status_code)
        print(">>> headers:", resp.headers)
        print(">>> text:", repr(resp.text)[:500])

        # إذا كان JSON صالح
        if resp.status_code == 200 and "application/json" in resp.headers.get("Content-Type", ""):
            data = resp.json()
        else:
            return Response({
                "error": "Model response not JSON",
                "status": resp.status_code,
                "raw": resp.text
            }, status=500)

    except Exception as e:
        return Response({"error": "Model request failed", "details": str(e)}, status=500)

    raw_text = data.get("response", "")
    match = re.search(r"\d[\d\s\-]*\d", raw_text)
    plate_num = match.group(0).replace(" ", "") if match else ""

    return Response({"ocr_plate": plate_num, "raw_text": raw_text})
