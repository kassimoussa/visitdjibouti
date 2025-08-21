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
        Schema::table('app_users', function (Blueprint $table) {
            // Support pour les utilisateurs anonymes
            $table->boolean('is_anonymous')->default(false)->after('id');
            $table->string('anonymous_id')->nullable()->unique()->after('is_anonymous');
            $table->string('device_id')->nullable()->after('anonymous_id'); // Pour identifier l'appareil
            
            // Rendre les champs optionnels pour les utilisateurs anonymes
            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->timestamp('email_verified_at')->nullable()->change();
            $table->string('password')->nullable()->change();
            
            // Ajouter des métadonnées utiles
            $table->timestamp('converted_at')->nullable()->after('updated_at'); // Quand l'utilisateur anonyme devient complet
            $table->json('conversion_source')->nullable()->after('converted_at'); // Source de la conversion (favorites, reservation, etc.)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('app_users', function (Blueprint $table) {
            $table->dropColumn([
                'is_anonymous',
                'anonymous_id', 
                'device_id',
                'converted_at',
                'conversion_source'
            ]);
            
            // Remettre les contraintes NOT NULL (attention: peut échouer si des données anonymes existent)
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
