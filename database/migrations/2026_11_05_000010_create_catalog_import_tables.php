<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalog_import_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('total_rows')->default(0);
            $table->string('status', 24)->default('review');
            $table->timestamps();
        });

        Schema::create('catalog_import_rows', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('batch_id')->constrained('catalog_import_batches')->cascadeOnDelete();
            $table->unsignedInteger('source_row');
            $table->string('source_category', 160)->nullable();
            $table->foreignId('suggested_category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('external_model', 100)->nullable();
            $table->string('internal_model', 150)->nullable();
            $table->string('part_number', 100)->nullable();
            $table->string('description', 255)->nullable();
            $table->longText('features')->nullable();
            $table->unsignedSmallInteger('warranty_months')->nullable();
            $table->string('status', 24)->default('pending');
            $table->string('issue', 255)->nullable();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->timestamps();
            $table->unique(['batch_id', 'source_row']);
            $table->index(['batch_id', 'status']);
        });

        // Intentionnel : aucun prix d'achat fournisseur, volume, quantité BOQ
        // ou montant total confidentiel n'est enregistré en base.
    }

    public function down(): void
    {
        Schema::dropIfExists('catalog_import_rows');
        Schema::dropIfExists('catalog_import_batches');
    }
};
