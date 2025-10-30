<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour le statut par défaut de 'draft' à 'active'
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('status', ['draft', 'active', 'inactive'])->default('active')->change();
        });

        // Mettre à jour toutes les activités existantes qui sont en 'draft' vers 'active'
        // sauf si elles ont été explicitement mises en draft par un opérateur
        DB::table('activities')
            ->where('status', 'draft')
            ->where('created_at', '>', now()->subDay()) // Seulement celles créées récemment
            ->update(['status' => 'active']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->enum('status', ['draft', 'active', 'inactive'])->default('draft')->change();
        });
    }
};
