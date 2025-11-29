@extends('operator.layouts.app')

@section('title', 'Commentaires - ' . $tour->title)

@section('page-title', 'Commentaires du Tour')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.tours.index') }}">
                            <i class="fas fa-route me-1"></i>
                            Tours
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.tours.show', $tour) }}">
                            {{ Str::limit($tour->title, 40) }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Commentaires</li>
                </ol>
            </nav>
            <h2 class="mb-1">Commentaires du Tour</h2>
            <p class="text-muted mb-0">{{ $tour->title }}</p>
        </div>
        <div>
            <a href="{{ route('operator.tours.show', $tour) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour au tour
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
                    <h3 class="mb-1">{{ $tour->approvedComments()->where('app_user_id', '!=', null)->count() }}</h3>
                    <small>Utilisateurs vérifiés</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <h3 class="mb-1">{{ $tour->approvedComments()->whereNotNull('parent_id')->count() }}</h3>
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
