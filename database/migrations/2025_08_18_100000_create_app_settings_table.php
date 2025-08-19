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
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique identifier for the setting');
            $table->enum('type', ['image', 'text', 'config', 'mixed'])->comment('Type of setting content');
            $table->unsignedBigInteger('media_id')->nullable()->comment('Optional main media file');
            $table->json('content')->comment('Flexible JSON content with multilingual support');
            $table->boolean('is_active')->default(true)->comment('Whether this setting is active');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('media_id')->references('id')->on('media')->onDelete('set null');
            
            // Indexes for performance
            $table->index(['key', 'is_active']);
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_settings');
    }
};