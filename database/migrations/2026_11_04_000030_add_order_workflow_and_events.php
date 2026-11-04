<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->uuid('submission_key')->nullable()->unique();
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
        });

        Schema::create('order_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->text('note')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_events');
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropUnique(['submission_key']);
            $table->dropColumn(['submission_key', 'consented_at', 'confirmed_at']);
        });
    }
};
