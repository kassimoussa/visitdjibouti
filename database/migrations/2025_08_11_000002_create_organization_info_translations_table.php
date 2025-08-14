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
        Schema::create('organization_info_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_info_id');
            $table->string('locale', 2);
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('opening_hours_translated')->nullable();
            $table->timestamps();

            $table->foreign('organization_info_id')->references('id')->on('organization_info')->onDelete('cascade');
            $table->unique(['organization_info_id', 'locale'], 'org_info_trans_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_info_translations');
    }
};