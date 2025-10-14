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
        // Events
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website_url')->nullable();
            $table->string('ticket_url')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->string('organizer')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('allow_reservations')->default(false);
            $table->string('status')->default('draft');
            $table->foreignId('creator_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->foreignId('tour_operator_id')->nullable()->constrained('tour_operators')->nullOnDelete();
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->bigInteger('views_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'start_date']);
            $table->index(['is_featured', 'start_date']);
            $table->index(['start_date', 'end_date']);
            $table->index(['tour_operator_id', 'status']);
        });

        // Event Translations
        Schema::create('event_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('title');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->text('location_details')->nullable();
            $table->text('requirements')->nullable();
            $table->longText('program')->nullable();
            $table->text('additional_info')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'locale']);
        });

        // Category-Event Pivot
        Schema::create('category_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['category_id', 'event_id']);
        });

        // Media-Event Pivot
        Schema::create('media_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->unique(['media_id', 'event_id']);
        });

        // Event Registrations
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone')->nullable();
            $table->integer('participants_count')->default(1);
            $table->string('status')->default('pending');
            $table->string('registration_number')->unique();
            $table->string('payment_status')->default('pending');
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('special_requirements')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_id', 'status']);
            $table->index('user_email');
            $table->index('registration_number');
        });

        // Event Reviews
        Schema::create('event_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name');
            $table->string('user_email');
            $table->integer('rating');
            $table->string('title')->nullable();
            $table->text('comment');
            $table->string('status')->default('pending');
            $table->text('admin_reply')->nullable();
            $table->foreignId('admin_reply_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamp('admin_reply_at')->nullable();
            $table->boolean('is_verified_attendee')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->integer('report_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['event_id', 'status']);
            $table->index('rating');
            $table->index('user_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_reviews');
        Schema::dropIfExists('event_registrations');
        Schema::dropIfExists('media_event');
        Schema::dropIfExists('category_event');
        Schema::dropIfExists('event_translations');
        Schema::dropIfExists('events');
    }
};
