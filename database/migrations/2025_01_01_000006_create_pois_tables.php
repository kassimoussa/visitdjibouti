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
        // POIs
        Schema::create('pois', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('region')->nullable();
            $table->json('contacts')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_reservations')->default(false);
            $table->string('status')->default('draft');
            $table->foreignId('creator_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
        });

        // POI Translations
        Schema::create('poi_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poi_id')->constrained('pois')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->string('address')->nullable();
            $table->text('opening_hours')->nullable();
            $table->string('entry_fee')->nullable();
            $table->text('tips')->nullable();
            $table->timestamps();

            $table->unique(['poi_id', 'locale']);
        });

        // Category-POI Pivot
        Schema::create('category_poi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('poi_id')->constrained('pois')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['category_id', 'poi_id']);
        });

        // Media-POI Pivot
        Schema::create('media_poi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
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
