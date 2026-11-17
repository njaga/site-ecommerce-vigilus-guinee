<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('training_sessions', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 160);
            $table->text('description');
            $table->dateTime('starts_at');
            $table->string('location', 200);
            $table->string('audience', 160);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            $table->index(['is_published', 'starts_at']);
        });
        Schema::create('training_requests', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('training_session_id')->nullable()->constrained()->nullOnDelete();
            $table->string('session_title')->nullable();
            $table->string('request_type', 20);
            $table->string('name', 150);
            $table->string('company', 150)->nullable();
            $table->string('email');
            $table->string('phone', 40);
            $table->unsignedInteger('participants');
            $table->string('preferred_period', 150)->nullable();
            $table->text('message')->nullable();
            $table->timestamp('consented_at');
            $table->string('status', 20)->default('new');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_requests');
        Schema::dropIfExists('training_sessions');
    }
};
