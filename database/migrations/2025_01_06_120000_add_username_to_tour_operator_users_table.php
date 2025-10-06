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
            $table->string('username')->unique()->after('name');

            // Index pour performance de connexion
            $table->index(['username']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tour_operator_users', function (Blueprint $table) {
            $table->dropIndex(['username']);
            $table->dropColumn('username');
        });
    }
};