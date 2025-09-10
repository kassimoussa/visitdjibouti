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
        Schema::create('poi_tour_operator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poi_id')->constrained()->onDelete('cascade');
            $table->foreignId('tour_operator_id')->constrained()->onDelete('cascade');
            $table->enum('service_type', [
                'guide', 
                'transport', 
                'full_package', 
                'accommodation',
                'activity',
                'other'
            ])->default('guide');
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Index unique pour éviter les doublons
            $table->unique(['poi_id', 'tour_operator_id'], 'unique_poi_tour_operator');
            
            // Index pour les requêtes fréquentes
            $table->index(['poi_id', 'is_active']);
            $table->index(['tour_operator_id', 'is_active']);
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poi_tour_operator');
    }
};