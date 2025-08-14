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
        Schema::create('link_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_id');
            $table->string('locale', 2);
            $table->string('name'); // "Site web officiel", "Official website", "الموقع الرسمي"
            $table->timestamps();

            $table->foreign('link_id')->references('id')->on('links')->onDelete('cascade');
            $table->unique(['link_id', 'locale'], 'link_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_translations');
    }
};