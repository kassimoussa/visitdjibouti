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
        // Tours
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');
            $table->string('type');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('target_type')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->integer('duration_hours')->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('min_participants')->default(1);
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 3)->default('DJF');
            $table->enum('difficulty_level', ['easy', 'moderate', 'difficult', 'expert'])->default('easy');
            $table->json('includes')->nullable();
            $table->json('requirements')->nullable();
            $table->decimal('meeting_point_latitude', 10, 8)->nullable();
            $table->decimal('meeting_point_longitude', 11, 8)->nullable();
            $table->string('meeting_point_address')->nullable();
            $table->enum('status', ['active', 'suspended', 'archived'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->json('recurring_pattern')->nullable();
            $table->boolean('weather_dependent')->default(false);
            $table->integer('age_restriction_min')->nullable();
            $table->integer('age_restriction_max')->nullable();
            $table->text('cancellation_policy')->nullable();
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->integer('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['target_type', 'target_id']);
            $table->index('tour_operator_id');
            $table->index('type');
            $table->index('status');
            $table->index('is_featured');
            $table->index('difficulty_level');
            $table->index('price');
            $table->index(['meeting_point_latitude', 'meeting_point_longitude']);
        });

        // Tour Translations
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

        // Media-Tour Pivot
        Schema::create('media_tour', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['media_id', 'tour_id']);
            $table->index(['tour_id', 'order']);
        });

        // Tour Schedules
        Schema::create('tour_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_id')->constrained('tours')->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('available_spots');
            $table->integer('booked_spots')->default(0);
            $table->enum('status', ['available', 'full', 'cancelled', 'completed'])->default('available');
            $table->string('guide_name')->nullable();
            $table->string('guide_contact')->nullable();
            $table->json('guide_languages')->nullable();
            $table->text('special_notes')->nullable();
            $table->enum('weather_status', ['unknown', 'favorable', 'unfavorable', 'cancelled_weather'])->default('unknown');
            $table->text('meeting_point_override')->nullable();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->timestamp('cancellation_deadline')->nullable();
            $table->foreignId('created_by_admin_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index('tour_id');
            $table->index('start_date');
            $table->index('status');
            $table->index(['start_date', 'status']);
            $table->index('guide_name');
            $table->index(['available_spots', 'booked_spots']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_schedules');
        Schema::dropIfExists('media_tour');
        Schema::dropIfExists('tour_translations');
        Schema::dropIfExists('tours');
    }
};
