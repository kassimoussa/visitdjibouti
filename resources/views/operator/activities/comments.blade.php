@extends('operator.layouts.app')

@section('title', 'Commentaires - ' . $activity->title)

@section('page-title', 'Commentaires de l\'Activité')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.activities.index') }}">
                            <i class="fas fa-running me-1"></i>
                            Activités
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.activities.show', $activity) }}">
                            {{ Str::limit($activity->title, 40) }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Commentaires</li>
                </ol>
            </nav>
            <h2 class="mb-1">Commentaires de l'Activité</h2>
            <p class="text-muted mb-0">{{ $activity->title }}</p>
        </div>
        <div>
            <a href="{{ route('operator.activities.show', $activity) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à l'activité
            </a>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $comments->total() }}</h3>
                    <small>Total commentaires</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $activity->approvedComments()->where('app_user_id', '!=', null)->count() }}</h3>
                    <small>Utilisateurs vérifiés</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $activity->approvedComments()->whereNotNull('parent_id')->count() }}</h3>
                    <small>Réponses</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste complète des commentaires avec pagination -->
    <x-operator.comments-section
        :comments="$comments"
        title="Tous les commentaires" />
</div>
@endsection
