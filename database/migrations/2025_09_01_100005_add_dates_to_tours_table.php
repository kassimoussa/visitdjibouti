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
            // Ajouter les champs de date s'ils n'existent pas
            if (!Schema::hasColumn('tours', 'start_date')) {
                $table->date('start_date')->nullable()->after('target_type');
            }
            if (!Schema::hasColumn('tours', 'end_date')) {
                $table->date('end_date')->nullable()->after('start_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            if (Schema::hasColumn('tours', 'start_date')) {
                $table->dropColumn('start_date');
            }
            if (Schema::hasColumn('tours', 'end_date')) {
                $table->dropColumn('end_date');
            }
        });
    }
};
