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
        Schema::table('pois', function (Blueprint $table) {
            // Changer le champ contact de string à text pour permettre de très longs textes
            $table->text('contact')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pois', function (Blueprint $table) {
            // Revenir au type string original (attention: peut tronquer les données existantes)
            $table->string('contact')->nullable()->change();
        });
    }
};