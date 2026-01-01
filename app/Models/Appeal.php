<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appeal extends Model
{
    protected $fillable = ['violation_id', 'status', 'reason', 'decision_note'];

    public function violation()
    {
        return $this->belongsTo(Violation::class);
    }
}
