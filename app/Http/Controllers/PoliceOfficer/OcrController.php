<?php

namespace App\Http\Controllers\PoliceOfficer;

use App\Http\Controllers\Controller;
use App\Http\Services\PoliceOfficer\RabbitPublisher;
use App\Models\AiJob;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OcrController extends Controller
{
    
    public function requestPlateOcr(Request $request, RabbitPublisher $publisher)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
            'violation_draft_id' => 'nullable|integer',
        ]);

        // 1) خزّن الصورة لوكلي
        $path = $request->file('image')->move(
    public_path('uploads/plates'),
    uniqid() . '.' . $request->file('image')->getClientOriginalExtension()
);

$absolutePath = $path->getPathname();


        // 2) أنشئ job في DB
        $jobId = (string) Str::uuid();
        $corrId = (string) Str::uuid();

        $payload = [
            'local_image_path' => $absolutePath,
            'violation_draft_id' => $request->input('violation_draft_id'),
        ];

        AiJob::create([
            'job_id' => $jobId,
            'correlation_id' => $corrId,
            'type' => 'plate_ocr',
            'status' => 'queued',
            'requested_by' => auth()->id(),
            'violation_draft_id' => $request->input('violation_draft_id'),
            'payload' => $payload,
        ]);

        // 3) ابعث رسالة RabbitMQ (Contract)
        $message = [
            'job_id' => $jobId,
            'correlation_id' => $corrId,
            'type' => 'plate_ocr',
            'payload' => $payload,
            'reply_to' => env('AI_RMQ_RESULTS_QUEUE', 'ai.results'),
            'schema_version' => 1,
        ];

        $publisher->publish('job.create', $message);

        return response()->json([
            'status' => 'queued',
            'job_id' => $jobId,
        ]);
    }

    public function getOcrResult(string $job_id)
    {
        $job = AiJob::where('job_id', $job_id)->firstOrFail();

        return response()->json([
            'job_id' => $job->job_id,
            'status' => $job->status,
            'result' => $job->result,
            'error' => $job->error,
        ]);
    }

}
