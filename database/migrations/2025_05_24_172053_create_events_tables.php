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
        // 1. Table principale des événements
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            
            // Dates et heures
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            // Localisation
            $table->string('location')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            
            // Contact et liens
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->string('website_url')->nullable();
            $table->string('ticket_url')->nullable();
            
            // Participants et prix
            $table->decimal('price', 10, 2)->nullable();
            $table->integer('max_participants')->nullable();
            $table->integer('current_participants')->default(0);
            $table->string('organizer')->nullable();
            
            // Statuts et options
            $table->boolean('is_featured')->default(false);
            $table->string('status')->default('draft'); // draft, published, scheduled, cancelled, completed
            
            // Relations
            $table->foreignId('creator_id')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->foreignId('featured_image_id')->nullable()->constrained('media')->nullOnDelete();
            
            // Stats
            $table->bigInteger('views_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index
            $table->index(['status', 'start_date']);
            $table->index(['is_featured', 'start_date']);
            $table->index(['start_date', 'end_date']);
        });
        
        // 2. Table de traductions des événements
        Schema::create('event_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('locale', 5); // 'fr', 'en', 'ar', etc.
            
            // Champs traduisibles
            $table->string('title');
            $table->text('description');
            $table->text('short_description')->nullable();
            $table->text('location_details')->nullable();
            $table->text('requirements')->nullable();
            $table->longText('program')->nullable();
            $table->text('additional_info')->nullable();
            
            $table->timestamps();
            
            // Contrainte d'unicité
            $table->unique(['event_id', 'locale']);
        });
        
        // 3. Table pivot pour la relation many-to-many avec categories
        Schema::create('category_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['category_id', 'event_id']);
        });
        
        // 4. Table pivot pour la relation many-to-many avec media
        Schema::create('media_event', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->onDelete('cascade');
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->unique(['media_id', 'event_id']);
        });
        
        // 5. Table des inscriptions aux événements
        Schema::create('event_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Informations de l'utilisateur (pour les non-connectés aussi)
            $table->string('user_name');
            $table->string('user_email');
            $table->string('user_phone')->nullable();
            
            // Détails de l'inscription
            $table->integer('participants_count')->default(1);
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, attended, no_show
            $table->string('registration_number')->unique();
            
            // Paiement
            $table->string('payment_status')->default('pending'); // pending, paid, failed, refunded
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_reference')->nullable();
            
            // Informations supplémentaires
            $table->text('special_requirements')->nullable();
            $table->text('notes')->nullable();
            
            // Annulation
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['event_id', 'status']);
            $table->index(['user_email']);
            $table->index(['registration_number']);
        });
        
        // 6. Table des avis sur les événements
        Schema::create('event_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            
            // Informations de l'utilisateur
            $table->string('user_name');
            $table->string('user_email');
            
            // Contenu de l'avis
            $table->integer('rating'); // 1 à 5
            $table->string('title')->nullable();
            $table->text('comment');
            
            // Modération
            $table->string('status')->default('pending'); // pending, approved, rejected, spam
            
            // Réponse admin
            $table->text('admin_reply')->nullable();
            $table->foreignId('admin_reply_by')->nullable()->constrained('admin_users')->nullOnDelete();
            $table->timestamp('admin_reply_at')->nullable();
            
            // Métadonnées
            $table->boolean('is_verified_attendee')->default(false);
            $table->integer('helpful_count')->default(0);
            $table->integer('report_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['event_id', 'status']);
            $table->index(['rating']);
            $table->index(['user_email']);
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