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
        // Table des avis (reviews) pour les POIs avec notation étoiles
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('poi_id')->constrained('pois')->onDelete('cascade');
            $table->foreignId('app_user_id')->nullable()->constrained('app_users')->onDelete('set null');

            // Informations pour les utilisateurs invités
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            // Contenu de l'avis
            $table->unsignedTinyInteger('rating'); // 1-5 étoiles
            $table->string('title')->nullable(); // Titre optionnel
            $table->text('comment')->nullable(); // Commentaire détaillé

            // Métadonnées
            $table->boolean('is_verified')->default(false)->comment('Utilisateur a visité/réservé');
            $table->boolean('is_approved')->default(true)->comment('Modération');
            $table->unsignedInteger('helpful_count')->default(0)->comment('Nombre de "utile"');

            // Réponse de l'opérateur (optionnel)
            $table->text('operator_response')->nullable();
            $table->timestamp('operator_response_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('poi_id');
            $table->index('app_user_id');
            $table->index(['poi_id', 'rating']);
            $table->index(['is_approved', 'created_at']);
        });

        // Table des commentaires (polymorphique) pour POI, Event, Tour, TourOperator, Activity
        Schema::create('comments', function (Blueprint $table) {
            $table->id();

            // Relation polymorphique
            $table->morphs('commentable'); // commentable_type, commentable_id

            // Auteur
            $table->foreignId('app_user_id')->nullable()->constrained('app_users')->onDelete('set null');

            // Informations pour les utilisateurs invités
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();

            // Commentaires imbriqués (réponses)
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');

            // Contenu
            $table->text('comment');

            // Métadonnées
            $table->boolean('is_approved')->default(true)->comment('Modération');
            $table->unsignedInteger('likes_count')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['commentable_type', 'commentable_id']);
            $table->index('app_user_id');
            $table->index('parent_id');
            $table->index(['is_approved', 'created_at']);
        });

        // Table pour les "helpful" votes sur les reviews
        Schema::create('review_helpful_votes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('review_id')->constrained('reviews')->onDelete('cascade');
            $table->foreignId('app_user_id')->nullable()->constrained('app_users')->onDelete('cascade');
            $table->string('guest_identifier')->nullable()->comment('IP ou device ID pour invités');
            $table->timestamps();

            // Un utilisateur/invité ne peut voter qu'une fois
            $table->unique(['review_id', 'app_user_id']);
            $table->index(['review_id', 'guest_identifier']);
        });

        // Table pour les "likes" sur les comments
        Schema::create('comment_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->onDelete('cascade');
            $table->foreignId('app_user_id')->nullable()->constrained('app_users')->onDelete('cascade');
            $table->string('guest_identifier')->nullable()->comment('IP ou device ID pour invités');
            $table->timestamps();

            // Un utilisateur/invité ne peut liker qu'une fois
            $table->unique(['comment_id', 'app_user_id']);
            $table->index(['comment_id', 'guest_identifier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_likes');
        Schema::dropIfExists('review_helpful_votes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('reviews');
    }
};
