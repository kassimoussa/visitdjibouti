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
        // 1. Création de la table principale des actualités
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->longText('content_blocks')->nullable(); // Contenu HTML de TinyMCE
            $table->datetime('published_at')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->unsignedInteger('views_count')->default(0);
            $table->unsignedInteger('reading_time')->nullable(); // En minutes
            
            // Relations
            $table->foreignId('creator_id')->constrained('admin_users')->onDelete('cascade');
            $table->foreignId('news_category_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour performance
            $table->index(['status', 'published_at']);
            $table->index(['is_featured', 'status']);
            $table->index('news_category_id');
        });

        // 2. Création de la table de traductions pour les actualités
        Schema::create('news_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // fr, en, ar, etc.
            
            // Champs traduisibles
            $table->string('title');
            $table->text('excerpt')->nullable(); // Résumé court
            $table->text('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('seo_keywords')->nullable(); // Tags SEO
            
            $table->timestamps();
            
            // Contrainte d'unicité
            $table->unique(['news_id', 'locale']);
        });

        // 3. Table pivot pour les catégories multiples (optionnel)
        Schema::create('news_news_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->foreignId('news_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['news_id', 'news_category_id']);
        });

        // 4. Table pivot pour les médias
        Schema::create('media_news', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->unsignedInteger('order')->default(0);
            $table->string('type')->default('gallery'); // gallery, inline, featured
            $table->timestamps();
            
            $table->unique(['news_id', 'media_id']);
        });

        // 5. Table pour les tags
        Schema::create('news_tags', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('news_tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_tag_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->timestamps();
            
            $table->unique(['news_tag_id', 'locale']);
        });

        Schema::create('news_news_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_id')->constrained()->onDelete('cascade');
            $table->foreignId('news_tag_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['news_id', 'news_tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_news_tag');
        Schema::dropIfExists('news_tag_translations');
        Schema::dropIfExists('news_tags');
        Schema::dropIfExists('media_news');
        Schema::dropIfExists('news_news_category');
        Schema::dropIfExists('news_translations');
        Schema::dropIfExists('news');
    }
};