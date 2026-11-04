<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->unique();
            $table->string('customer_name');
            $table->string('customer_phone', 40);
            $table->string('customer_email')->nullable();
            $table->string('city');
            $table->string('district')->nullable();
            $table->text('address');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('subtotal_gnf');
            $table->unsignedBigInteger('shipping_gnf')->nullable();
            $table->unsignedBigInteger('total_gnf')->nullable();
            $table->string('currency', 3)->default('GNF');
            $table->string('status')->default('pending_review');
            $table->string('payment_status')->default('unpaid');
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at']);
        });

        Schema::create('order_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku_snapshot');
            $table->string('name_snapshot');
            $table->unsignedBigInteger('unit_price_gnf');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('subtotal_gnf');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
