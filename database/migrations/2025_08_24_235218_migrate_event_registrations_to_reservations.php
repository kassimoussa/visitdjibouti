<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\Event;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrer toutes les données d'event_registrations vers reservations
        if (Schema::hasTable('event_registrations')) {
            
            // Récupérer toutes les inscriptions d'événements
            $eventRegistrations = DB::table('event_registrations')->get();
            
            foreach ($eventRegistrations as $registration) {
                // Créer une nouvelle réservation correspondante
                DB::table('reservations')->insert([
                    'reservable_id' => $registration->event_id,
                    'reservable_type' => Event::class,
                    'app_user_id' => $registration->user_id, // Transfert de user_id vers app_user_id
                    'guest_name' => $registration->user_name, // Si pas d'user_id, utiliser comme guest
                    'guest_email' => $registration->user_email,
                    'guest_phone' => $registration->user_phone,
                    'reservation_date' => $registration->created_at, // Utiliser la date de création comme date de réservation
                    'number_of_people' => $registration->participants_count,
                    'status' => $registration->status,
                    'confirmation_number' => $registration->registration_number,
                    'special_requirements' => $registration->special_requirements,
                    'notes' => $registration->notes,
                    'payment_status' => $registration->payment_status,
                    'payment_amount' => $registration->payment_amount,
                    'payment_reference' => $registration->payment_reference,
                    'cancelled_at' => $registration->cancelled_at,
                    'cancellation_reason' => $registration->cancellation_reason,
                    'created_at' => $registration->created_at,
                    'updated_at' => $registration->updated_at,
                    'deleted_at' => $registration->deleted_at,
                ]);
            }
            
            // Message de confirmation
            $count = $eventRegistrations->count();
            echo "Migration completed: {$count} event registrations migrated to reservations.\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Supprimer toutes les réservations d'événements créées par cette migration
        DB::table('reservations')
            ->where('reservable_type', Event::class)
            ->delete();
            
        echo "Rollback completed: Event reservations removed from reservations table.\n";
    }
};
