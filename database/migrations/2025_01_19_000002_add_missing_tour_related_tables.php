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
        // Tour Translations
        if (!Schema::hasTable('tour_translations')) {
            Schema::create('tour_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
                $table->string('locale', 2);
                $table->string('title');
                $table->text('description');
                $table->text('short_description')->nullable();
                $table->text('itinerary')->nullable();
                $table->text('meeting_point_description')->nullable();
                $table->json('highlights')->nullable();
                $table->json('what_to_bring')->nullable();
                $table->text('cancellation_policy_text')->nullable();
                $table->timestamps();

                $table->unique(['tour_id', 'locale']);
                $table->index('tour_id');
                $table->index('locale');
            });
        }

        // Media-Tour Pivot
        if (!Schema::hasTable('media_tour')) {
            Schema::create('media_tour', function (Blueprint $table) {
                $table->id();
                $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
                $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
                $table->integer('order')->default(0);
                $table->timestamps();

                $table->unique(['media_id', 'tour_id']);
                $table->index(['tour_id', 'order']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media_tour');
        Schema::dropIfExists('tour_translations');
    }
};
