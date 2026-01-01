<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appeals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('violation_id');
            $table->string('status')->default('pending');
            $table->text('reason');
            $table->text('decision_note')->nullable();
            $table->timestamps();

            // foreign key constraint
            $table->foreign('violation_id')
                  ->references('id')
                  ->on('violations')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appeals');
    }
};
