<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->boolean('price_on_request')->default(false);
            $table->timestamp('technical_verified_at')->nullable();
            $table->timestamp('commercial_verified_at')->nullable();
            $table->timestamp('media_rights_verified_at')->nullable();
            $table->timestamp('published_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table): void {
            $table->dropColumn([
                'price_on_request', 'technical_verified_at', 'commercial_verified_at',
                'media_rights_verified_at', 'published_at',
            ]);
        });
    }
};
