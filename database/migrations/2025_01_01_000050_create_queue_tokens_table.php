<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token_no', 20);
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('place_id')->constrained('places')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('counter_id')->nullable()->constrained('counters')->nullOnDelete();
            $table->string('status')->default('waiting');
            // waiting, called, serving, done, skipped, no_show, cancelled
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('served_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('date')->index(); // YYYY-MM-DD for daily token sequence
            $table->timestamps();

            $table->unique(['service_id', 'token_no', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_tokens');
    }
};
