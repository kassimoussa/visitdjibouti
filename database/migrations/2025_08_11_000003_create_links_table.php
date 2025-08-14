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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_info_id');
            $table->string('url');
            $table->string('platform'); // website, facebook, instagram, twitter, linkedin, youtube, etc.
            $table->integer('order')->default(0);
            $table->timestamps();

            $table->foreign('organization_info_id')->references('id')->on('organization_info')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};