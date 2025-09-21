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
        Schema::table('tours', function (Blueprint $table) {
            // Ajouter les champs de dates
            $table->date('start_date')->nullable()->after('type');
            $table->date('end_date')->nullable()->after('start_date');

            // Supprimer le champ duration_days
            $table->dropColumn('duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            // Remettre le champ duration_days
            $table->integer('duration_days')->nullable()->after('duration_hours');

            // Supprimer les champs de dates
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};