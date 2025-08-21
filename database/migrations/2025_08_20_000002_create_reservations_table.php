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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            
            // Relations polymorphes (POI ou Event)
            $table->morphs('reservable'); // reservable_id et reservable_type
            
            // Utilisateur (peut être null pour les invités)
            $table->foreignId('app_user_id')->nullable()->constrained('app_users')->nullOnDelete();
            
            // Informations utilisateur/invité
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('guest_phone')->nullable();
            
            // Détails de la réservation
            $table->date('reservation_date');
            $table->time('reservation_time')->nullable();
            $table->integer('number_of_people')->default(1);
            $table->string('status')->default('pending'); // pending, confirmed, cancelled, completed, no_show
            
            // Numéro de confirmation unique
            $table->string('confirmation_number')->unique();
            
            // Informations de contact et notes
            $table->json('contact_info')->nullable(); // Informations flexibles selon le type de réservation
            $table->text('special_requirements')->nullable();
            $table->text('notes')->nullable();
            
            // Paiement (optionnel selon le type de réservation)
            $table->string('payment_status')->default('not_required'); // not_required, pending, paid, failed, refunded
            $table->decimal('payment_amount', 10, 2)->nullable();
            $table->string('payment_reference')->nullable();
            
            // Annulation
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            // Rappels et notifications
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('confirmation_sent_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index pour performance (morphs() crée déjà l'index pour reservable_type, reservable_id)
            $table->index(['app_user_id']);
            $table->index(['guest_email']);
            $table->index(['reservation_date', 'status']);
            $table->index(['confirmation_number']);
            $table->index(['status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};