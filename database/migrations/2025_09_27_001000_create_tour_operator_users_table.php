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
        Schema::create('tour_operator_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tour_operator_id')->constrained('tour_operators')->onDelete('cascade');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('username')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('role')->default('editor'); // ex: admin, editor
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            // Index
            $table->index(['tour_operator_id', 'is_active']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tour_operator_users');
    }
};