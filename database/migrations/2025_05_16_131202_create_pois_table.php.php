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
        // 1. Table principale - nom simplifié
        Schema::create('pois', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('region')->nullable();
            $table->json('contact')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_reservations')->default(false);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->foreignId('creator_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
        });
        
        // 2. Table de traductions
        Schema::create('poi_translations', function (Blueprint $table) {
            $table->id();
            // Nom simplifié de la table référencée
            $table->foreignId('poi_id')->constrained('pois')->onDelete('cascade');
            $table->string('locale', 5); // 'fr', 'en', 'ar', etc.
            
            // Champs traduisibles
            $table->string('name');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('address')->nullable();
            $table->text('opening_hours')->nullable();
            $table->string('entry_fee')->nullable();
            $table->text('tips')->nullable();
            
            $table->timestamps();
            
            // Contrainte d'unicité simplifiée
            $table->unique(['poi_id', 'locale']);
        });
        
        // 3. Table pivot pour la relation many-to-many avec categories
        Schema::create('category_poi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('poi_id')->constrained('pois')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['category_id', 'poi_id']);
        });
        
        // 4. Table pivot pour la relation many-to-many avec media
        Schema::create('media_poi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->foreignId('poi_id')->constrained('pois')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique(['media_id', 'poi_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_poi');
        Schema::dropIfExists('category_poi');
        Schema::dropIfExists('poi_translations');
        Schema::dropIfExists('pois');
    }
};