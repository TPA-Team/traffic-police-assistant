<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ai_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('job_id')->unique();
            $table->uuid('correlation_id')->unique();
            $table->string('type', 50);
            $table->enum('status', ['queued','processing','success','failed'])->default('queued');

            $table->unsignedBigInteger('requested_by')->nullable();
            $table->unsignedBigInteger('violation_draft_id')->nullable();

            $table->json('payload');
            $table->json('result')->nullable();
            $table->json('error')->nullable();

            $table->unsignedInteger('attempts')->default(0);
            $table->timestamp('finished_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_jobs');
    }
};
