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
        Schema::table('categories', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable()->after('id');
            $table->integer('sort_order')->default(0)->after('parent_id');
            $table->integer('level')->default(0)->after('sort_order');
            
            // Contrainte de clé étrangère pour la hiérarchie
            $table->foreign('parent_id')->references('id')->on('categories')->onDelete('cascade');
            
            // Index pour optimiser les requêtes hiérarchiques
            $table->index(['parent_id', 'sort_order']);
            $table->index(['level', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropIndex(['parent_id', 'sort_order']);
            $table->dropIndex(['level', 'sort_order']);
            $table->dropColumn(['parent_id', 'sort_order', 'level']);
        });
    }
};