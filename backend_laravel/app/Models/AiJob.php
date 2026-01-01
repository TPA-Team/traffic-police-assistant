<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiJob extends Model
{
    protected $table = 'ai_jobs';

    protected $fillable = [
        'job_id','correlation_id','type','status',
        'requested_by','violation_draft_id',
        'payload','result','error','attempts','finished_at'
    ];

    protected $casts = [
        'payload' => 'array',
        'result' => 'array',
        'error' => 'array',
        'finished_at' => 'datetime',
    ];
}
