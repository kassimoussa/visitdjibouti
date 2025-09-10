<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Étape 1: Ajouter le nouveau champ JSON
        Schema::table('pois', function (Blueprint $table) {
            $table->json('contacts')->nullable()->after('contact');
        });

        // Étape 2: Migrer les données existantes du champ contact vers contacts JSON
        $pois = DB::table('pois')->whereNotNull('contact')->where('contact', '!=', '')->get();
        
        foreach ($pois as $poi) {
            // Convertir l'ancien contact en format JSON
            $contactsData = [
                [
                    'name' => 'Contact principal',
                    'type' => 'general',
                    'phone' => $poi->contact,
                    'email' => null,
                    'website' => null,
                    'address' => null,
                    'description' => null,
                    'is_primary' => true
                ]
            ];
            
            DB::table('pois')
                ->where('id', $poi->id)
                ->update(['contacts' => json_encode($contactsData)]);
        }

        // Étape 3: Supprimer l'ancien champ contact
        Schema::table('pois', function (Blueprint $table) {
            $table->dropColumn('contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurer le champ contact
        Schema::table('pois', function (Blueprint $table) {
            $table->text('contact')->nullable()->after('region');
        });

        // Récupérer le premier contact du JSON et le mettre dans le champ contact
        $pois = DB::table('pois')->whereNotNull('contacts')->get();
        
        foreach ($pois as $poi) {
            $contacts = json_decode($poi->contacts, true);
            if (!empty($contacts) && isset($contacts[0]['phone'])) {
                DB::table('pois')
                    ->where('id', $poi->id)
                    ->update(['contact' => $contacts[0]['phone']]);
            }
        }

        // Supprimer le champ JSON
        Schema::table('pois', function (Blueprint $table) {
            $table->dropColumn('contacts');
        });
    }
};