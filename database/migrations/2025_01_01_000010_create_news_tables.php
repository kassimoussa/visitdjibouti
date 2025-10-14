<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // News Categories
        Schema::create('news_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color', 7)->default('#3498db');
            $table->foreignId('parent_id')->nullable()->constrained('news_categories')->onDelete('cascade');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['parent_id', 'sort_order']);
        });

        // News Category Translations
        Schema::create('news_category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_category_id')->constrained('news_categories')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->timestamps();

            $table->unique(['news_category_id', 'locale']);
        });

        // News Tags
        Schema::create('news_tags', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // News Tag Translations
        Schema::create('news_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_tag_id')->constrained('news_tags')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();

            $table->unique(['news_tag_id', 'locale']);
        });

        // News
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->longText('content_blocks')->nullable();
            $table->dateTime('published_at')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('reading_time')->nullable();
            $table->foreignId('creator_id')->constrained('admin_users')->onDelete('cascade');
            $table->foreignId('news_category_id')->nullable()->constrained('news_categories')->nullOnDelete();
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'status']);
            $table->index('news_category_id');
        });

        // News Translations
        Schema::create('news_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('seo_keywords')->nullable();
            $table->timestamps();

            $table->unique(['news_id', 'locale']);
        });

        // News-NewsCategory Pivot
        Schema::create('news_news_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->foreignId('news_category_id')->constrained('news_categories')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['news_id', 'news_category_id']);
        });

        // News-NewsTag Pivot
        Schema::create('news_news_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->foreignId('news_tag_id')->constrained('news_tags')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['news_id', 'news_tag_id']);
        });

        // Media-News Pivot
        Schema::create('media_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained('news')->onDelete('cascade');
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            $table->unsignedInteger('order')->default(0);
            $table->string('type')->default('gallery');
            $table->timestamps();

            $table->unique(['news_id', 'media_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_news');
        Schema::dropIfExists('news_news_tag');
        Schema::dropIfExists('news_news_category');
        Schema::dropIfExists('news_translations');
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_tag_translations');
        Schema::dropIfExists('news_tags');
        Schema::dropIfExists('news_category_translations');
        Schema::dropIfExists('news_categories');
    }
};
