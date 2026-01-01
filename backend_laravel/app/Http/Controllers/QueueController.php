<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SendToAI;

class QueueController extends Controller
{
    public function sendMessage()
    {
        $payload = [
            'text' => 'test from controller',
            'source' => 'laravel'
        ];

        SendToAI::dispatch($payload)->onQueue('ai_service');

        return response()->json([
            'status' => 'success',
            'message' => 'Message dispatched to queue'
        ]);
    }
}
