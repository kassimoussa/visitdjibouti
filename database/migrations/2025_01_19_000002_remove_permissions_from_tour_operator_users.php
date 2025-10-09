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
        Schema::table('tour_operator_users', function (Blueprint $table) {
            $table->dropColumn('permissions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_operator_users', function (Blueprint $table) {
            $table->json('permissions')->nullable()->comment('manage_events, manage_tours, view_reservations, manage_profile, all');
        });
    }
};
