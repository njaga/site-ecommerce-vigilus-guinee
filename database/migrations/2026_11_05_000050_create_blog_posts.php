<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title', 220);
            $table->string('slug', 180)->unique();
            $table->string('category', 70)->default('Conseils');
            $table->string('excerpt', 360);
            $table->longText('body');
            $table->string('cover_path')->nullable();
            $table->string('cover_alt', 180)->nullable();
            $table->string('seo_title', 75)->nullable();
            $table->string('seo_description', 165)->nullable();
            $table->string('status', 20)->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'published_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
