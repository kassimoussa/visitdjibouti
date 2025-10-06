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
        Schema::table('events', function (Blueprint $table) {
            // Ajout de la relation avec les tour operators
            $table->foreignId('tour_operator_id')->nullable()
                  ->after('creator_id')
                  ->constrained('tour_operators')
                  ->nullOnDelete();

            // Index pour optimiser les requêtes
            $table->index(['tour_operator_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['tour_operator_id']);
            $table->dropIndex(['tour_operator_id', 'status']);
            $table->dropColumn('tour_operator_id');
        });
    }
};