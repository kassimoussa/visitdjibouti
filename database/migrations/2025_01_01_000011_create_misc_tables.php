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
        // Organization Info
        Schema::create('organization_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('logo_id')->nullable()->constrained('media')->nullOnDelete();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->text('opening_hours')->nullable();
            $table->timestamps();
        });

        // Organization Info Translations
        Schema::create('organization_info_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_info_id')->constrained('organization_info')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('opening_hours_translated')->nullable();
            $table->timestamps();

            $table->unique(['organization_info_id', 'locale'], 'org_info_trans_unique');
        });

        // Links (Social media, etc.)
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_info_id')->constrained('organization_info')->onDelete('cascade');
            $table->string('url');
            $table->string('platform');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Link Translations
        Schema::create('link_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('link_id')->constrained('links')->onDelete('cascade');
            $table->string('locale', 2);
            $table->string('name');
            $table->timestamps();

            $table->unique(['link_id', 'locale'], 'link_trans_unique');
        });

        // Embassies
        Schema::create('embassies', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['foreign_in_djibouti', 'djiboutian_abroad'])->comment('Ambassades étrangères à Djibouti ou djiboutiennes à l\'étranger');
            $table->string('country_code', 3)->nullable()->comment('Code pays ISO');
            $table->string('phones')->nullable()->comment('Numéros séparés par |');
            $table->string('emails')->nullable()->comment('Emails séparés par |');
            $table->string('fax')->nullable();
            $table->string('website')->nullable();
            $table->string('ld')->nullable()->comment('Numéros LD séparés par |');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['type', 'is_active']);
        });

        // Embassy Translations
        Schema::create('embassy_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('embassy_id')->constrained('embassies')->onDelete('cascade');
            $table->string('locale', 5);
            $table->string('name');
            $table->string('ambassador_name')->nullable();
            $table->text('address')->nullable();
            $table->string('postal_box')->nullable();
            $table->timestamps();

            $table->unique(['embassy_id', 'locale']);
        });

        // External Links
        Schema::create('external_links', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // App Settings
        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique()->comment('Unique identifier');
            $table->enum('type', ['image', 'text', 'config', 'mixed'])->comment('Type of setting');
            $table->foreignId('media_id')->nullable()->constrained('media')->nullOnDelete()->comment('Optional main media');
            $table->json('content')->comment('Flexible JSON content with multilingual support');
            $table->boolean('is_active')->default(true)->comment('Whether setting is active');
            $table->timestamps();

            $table->index(['key', 'is_active']);
            $table->index('type');
        });

        // Reservations (Polymorphic)
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservable_type');
            $table->unsignedBigInteger('reservable_id');
            $table->foreignId('app_user_id')->nullable()->constrained('app_users')->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            $table->date('reservation_date');
            $table->time('reservation_time')->nullable();
            $table->integer('number_of_people')->default(1);
            $table->string('status')->default('pending');
            $table->string('confirmation_number')->unique();
            $table->json('contact_info')->nullable();
            $table->text('special_requirements')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_status')->default('not_required');
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('confirmation_sent_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reservable_type', 'reservable_id']);
            $table->index('app_user_id');
            $table->index('guest_email');
            $table->index(['reservation_date', 'status']);
            $table->index('confirmation_number');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('app_settings');
        Schema::dropIfExists('external_links');
        Schema::dropIfExists('embassy_translations');
        Schema::dropIfExists('embassies');
        Schema::dropIfExists('link_translations');
        Schema::dropIfExists('links');
        Schema::dropIfExists('organization_info_translations');
        Schema::dropIfExists('organization_info');
    }
};
