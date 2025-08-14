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
        // Table principale des ambassades
        Schema::create('embassies', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['foreign_in_djibouti', 'djiboutian_abroad'])->comment('Ambassades étrangères à Djibouti ou djiboutiennes à l\'étranger');
            $table->string('country_code', 3)->nullable()->comment('Code pays ISO (ex: PAL pour Palestine)');
            $table->string('phones')->nullable()->comment('Numéros de téléphone séparés par |');
            $table->string('emails')->nullable()->comment('Emails séparés par |');
            $table->string('fax')->nullable();
            $table->string('website')->nullable();
            $table->string('ld')->nullable()->comment('Numéros LD séparés par |');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['type', 'is_active']);
        });

        // Table de traductions des ambassades
        Schema::create('embassy_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('embassy_id')->constrained()->onDelete('cascade');
            $table->string('locale', 5); // fr, en, ar
            
            // Champs traduisibles
            $table->string('name'); // Nom de l'ambassade
            $table->string('ambassador_name')->nullable(); // Nom de l'ambassadeur
            $table->text('address')->nullable(); // Adresse
            $table->string('postal_box')->nullable(); // Boîte postale
            
            $table->timestamps();
            
            // Contrainte d'unicité
            $table->unique(['embassy_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('embassy_translations');
        Schema::dropIfExists('embassies');
    }
};