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
            // Rendre les champs target nullable car tous les tours n'ont pas forcément une cible POI/Event
            $table->unsignedBigInteger('target_id')->nullable()->change();
            $table->string('target_type')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->unsignedBigInteger('target_id')->nullable(false)->change();
            $table->string('target_type')->nullable(false)->change();
        });
    }
};
