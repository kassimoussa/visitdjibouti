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
        Schema::dropIfExists('tour_operator_media');
        Schema::dropIfExists('tour_operator_services');
        Schema::dropIfExists('tour_operator_translations');
        Schema::dropIfExists('tour_operators');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne pas recréer les anciennes tables
    }
};