@extends('layouts.admin')

@section('title', 'Inscription #' . $registration->id)
@section('page-title', 'Détails de l\'Inscription')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.activity-registrations.index') }}">Inscriptions</a></li>
            <li class="breadcrumb-item active">Inscription #{{ $registration->id }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2">Inscription #{{ $registration->id }}</h2>
            <div class="d-flex gap-2">
                @if($registration->status === 'pending')
                    <span class="badge bg-warning">En attente</span>
                @elseif($registration->status === 'confirmed')
                    <span class="badge bg-success">Confirmée</span>
                @elseif($registration->status === 'completed')
                    <span class="badge bg-primary">Terminée</span>
                @elseif($registration->status === 'cancelled_by_user')
                    <span class="badge bg-danger">Annulée par l'utilisateur</span>
                @else
                    <span class="badge bg-danger">Annulée par l'opérateur</span>
                @endif

                @if($registration->payment_status === 'pending')
                    <span class="badge bg-warning">Paiement en attente</span>
                @elseif($registration->payment_status === 'paid')
                    <span class="badge bg-success">Payé</span>
                @else
                    <span class="badge bg-info">Remboursé</span>
                @endif
            </div>
        </div>
        <a href="{{ route('admin.activity-registrations.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-md-8">
            <!-- Informations de l'activité -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-running me-2"></i>Activité
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex">
                        @if($registration->activity->featuredImage)
                            <img src="{{ asset($registration->activity->featuredImage->thumbnail_path ?? $registration->activity->featuredImage->path) }}"
                                 alt="{{ $registration->activity->title }}"
                                 class="rounded me-3"
                                 style="width: 120px; height: 120px; object-fit: cover;">
                        @endif
                        <div>
                            <h5>{{ $registration->activity->title }}</h5>
                            <p class="text-muted mb-2">{{ Str::limit($registration->activity->description, 150) }}</p>
                            <p class="mb-1">
                                <strong>Prix :</strong> {{ number_format($registration->activity->price, 0, ',', ' ') }} {{ $registration->activity->currency }}
                            </p>
                            <p class="mb-1">
                                <strong>Durée :</strong> {{ $registration->activity->duration }} minutes
                            </p>
                            <p class="mb-0">
                                <strong>Difficulté :</strong>
                                <span class="badge bg-{{ $registration->activity->difficulty_level === 'easy' ? 'success' : ($registration->activity->difficulty_level === 'moderate' ? 'warning' : 'danger') }}">
                                    {{ $registration->activity->difficulty_label }}
                                </span>
                            </p>
                            <a href="{{ route('admin.activities.show', $registration->activity) }}" class="btn btn-sm btn-outline-primary mt-2">
                                Voir l'activité
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations du participant -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>Participant
                    </h5>
                </div>
                <div class="card-body">
                    @if($registration->appUser)
                        <div class="d-flex align-items-center mb-3">
                            @if($registration->appUser->avatar_url)
                                <img src="{{ $registration->appUser->avatar_url }}"
                                     alt="{{ $registration->appUser->name }}"
                                     class="rounded-circle me-3"
                                     style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                     style="width: 50px; height: 50px;">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            @endif
                            <div>
                                <h6 class="mb-0">{{ $registration->appUser->name }}</h6>
                                <small class="text-muted">Utilisateur enregistré</small>
                            </div>
                        </div>
                        <p class="mb-1">
                            <i class="fas fa-envelope me-2"></i>{{ $registration->appUser->email }}
                        </p>
                        @if($registration->appUser->phone)
                            <p class="mb-0">
                                <i class="fas fa-phone me-2"></i>{{ $registration->appUser->phone }}
                            </p>
                        @endif
                        <a href="{{ route('admin.app-users.show', $registration->appUser->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                            Voir le profil
                        </a>
                    @else
                        <div class="mb-3">
                            <h6>{{ $registration->guest_name }}</h6>
                            <small class="text-muted">Utilisateur invité</small>
                        </div>
                        <p class="mb-1">
                            <i class="fas fa-envelope me-2"></i>{{ $registration->guest_email }}
                        </p>
                        @if($registration->guest_phone)
                            <p class="mb-0">
                                <i class="fas fa-phone me-2"></i>{{ $registration->guest_phone }}
                            </p>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Détails de l'inscription -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Détails de l'inscription
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Nombre de participants :</strong>
                            <p class="mb-0">{{ $registration->number_of_participants }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Prix total :</strong>
                            <p class="mb-0">{{ number_format($registration->total_price, 0, ',', ' ') }} {{ $registration->activity->currency }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date préférée :</strong>
                            <p class="mb-0">{{ $registration->preferred_date ? \Carbon\Carbon::parse($registration->preferred_date)->format('d/m/Y') : 'Non spécifiée' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date d'inscription :</strong>
                            <p class="mb-0">{{ $registration->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @if($registration->confirmed_at)
                        <div class="col-md-6 mb-3">
                            <strong>Date de confirmation :</strong>
                            <p class="mb-0">{{ $registration->confirmed_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        @if($registration->completed_at)
                        <div class="col-md-6 mb-3">
                            <strong>Date de complétion :</strong>
                            <p class="mb-0">{{ $registration->completed_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                        @if($registration->cancelled_at)
                        <div class="col-md-6 mb-3">
                            <strong>Date d'annulation :</strong>
                            <p class="mb-0">{{ $registration->cancelled_at->format('d/m/Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                    @if($registration->special_requests)
                        <div class="mt-3">
                            <strong>Demandes spéciales :</strong>
                            <p class="mb-0 mt-2">{{ $registration->special_requests }}</p>
                        </div>
                    @endif
                    @if($registration->cancellation_reason)
                        <div class="mt-3">
                            <strong>Raison de l'annulation :</strong>
                            <div class="alert alert-warning mb-0 mt-2">
                                {{ $registration->cancellation_reason }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Opérateur -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-route me-2"></i>Opérateur
                    </h5>
                </div>
                <div class="card-body">
                    <h6>{{ $registration->activity->tourOperator->name }}</h6>
                    @if($registration->activity->tourOperator->email)
                        <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $registration->activity->tourOperator->email }}</p>
                    @endif
                    @if($registration->activity->tourOperator->phone)
                        <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $registration->activity->tourOperator->phone }}</p>
                    @endif
                    @if($registration->activity->tourOperator->website)
                        <p class="mb-0"><i class="fas fa-globe me-2"></i><a href="{{ $registration->activity->tourOperator->website }}" target="_blank">{{ $registration->activity->tourOperator->website }}</a></p>
                    @endif
                    <a href="{{ route('tour-operators.show', $registration->activity->tour_operator_id) }}" class="btn btn-sm btn-outline-primary mt-2">
                        Voir l'opérateur
                    </a>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Statut -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Statut
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>État :</strong>
                        <div class="mt-2">
                            @if($registration->status === 'pending')
                                <span class="badge bg-warning fs-6">En attente</span>
                            @elseif($registration->status === 'confirmed')
                                <span class="badge bg-success fs-6">Confirmée</span>
                            @elseif($registration->status === 'completed')
                                <span class="badge bg-primary fs-6">Terminée</span>
                            @elseif($registration->status === 'cancelled_by_user')
                                <span class="badge bg-danger fs-6">Annulée (utilisateur)</span>
                            @else
                                <span class="badge bg-danger fs-6">Annulée (opérateur)</span>
                            @endif
                        </div>
                    </div>
                    <div>
                        <strong>Paiement :</strong>
                        <div class="mt-2">
                            @if($registration->payment_status === 'pending')
                                <span class="badge bg-warning fs-6">En attente</span>
                            @elseif($registration->payment_status === 'paid')
                                <span class="badge bg-success fs-6">Payé</span>
                            @else
                                <span class="badge bg-info fs-6">Remboursé</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>Chronologie
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <strong>Inscription créée</strong>
                                <p class="text-muted mb-0">{{ $registration->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @if($registration->confirmed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <strong>Confirmée</strong>
                                <p class="text-muted mb-0">{{ $registration->confirmed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($registration->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <strong>Complétée</strong>
                                <p class="text-muted mb-0">{{ $registration->completed_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                        @if($registration->cancelled_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <strong>Annulée</strong>
                                <p class="text-muted mb-0">{{ $registration->cancelled_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    padding-bottom: 20px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-item:not(:last-child)::before {
    content: '';
    position: absolute;
    left: -21px;
    top: 20px;
    width: 2px;
    height: calc(100% - 10px);
    background-color: #dee2e6;
}

.timeline-marker {
    position: absolute;
    left: -26px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 0 0 2px currentColor;
}

.timeline-content strong {
    display: block;
    margin-bottom: 5px;
}
</style>
@endpush
@endsection
