@extends('layouts.admin')

@section('title', $activity->title)
@section('page-title', 'Détails de l\'Activité')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('activities.index') }}">Activités</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($activity->title, 50) }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2">{{ $activity->title }}</h2>
            <div class="d-flex gap-2 align-items-center">
                @if($activity->status === 'draft')
                    <span class="badge bg-secondary">Brouillon</span>
                @elseif($activity->status === 'active')
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-danger">Inactive</span>
                @endif

                @if($activity->is_featured)
                    <span class="badge bg-warning">
                        <i class="fas fa-star"></i> Mise en avant
                    </span>
                @endif

                <span class="badge bg-{{ $activity->difficulty_level === 'easy' ? 'success' : ($activity->difficulty_level === 'moderate' ? 'warning' : 'danger') }}">
                    {{ $activity->difficulty_label }}
                </span>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            @if($activity->status !== 'draft')
                <form action="{{ route('activities.toggle-status', $activity) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-{{ $activity->status === 'active' ? 'warning' : 'success' }}">
                        <i class="fas fa-{{ $activity->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                        {{ $activity->status === 'active' ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
            @endif
            <form action="{{ route('activities.toggle-featured', $activity) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-warning">
                    <i class="fas fa-star{{ $activity->is_featured ? '' : '-half-alt' }} me-2"></i>
                    {{ $activity->is_featured ? 'Retirer mise en avant' : 'Mettre en avant' }}
                </button>
            </form>
            <button type="button" class="btn btn-outline-danger"
                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) { document.getElementById('delete-form').submit(); }">
                <i class="fas fa-trash me-2"></i>Supprimer
            </button>
            <form id="delete-form" action="{{ route('activities.destroy', $activity) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-md-8">
            <!-- Image principale -->
            @if($activity->featuredImage)
            <div class="card mb-4">
                <div class="card-body p-0">
                    <img src="{{ asset($activity->featuredImage->path) }}"
                         alt="{{ $activity->title }}"
                         class="img-fluid w-100"
                         style="max-height: 400px; object-fit: cover;">
                </div>
            </div>
            @endif

            <!-- Description -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-align-left me-2"></i>Description
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{!! nl2br(e($activity->description)) !!}</p>
                </div>
            </div>

            <!-- Détails de l'activité -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Prix :</strong>
                            <p class="mb-0">{{ number_format($activity->price, 0, ',', ' ') }} {{ $activity->currency }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Durée :</strong>
                            <p class="mb-0">{{ $activity->duration }} minutes</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Participants :</strong>
                            <p class="mb-0">{{ $activity->current_participants }}/{{ $activity->max_participants ?? '∞' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Âge minimum :</strong>
                            <p class="mb-0">{{ $activity->min_age ?? 'Non spécifié' }} ans</p>
                        </div>
                        @if($activity->region)
                        <div class="col-md-6 mb-3">
                            <strong>Région :</strong>
                            <p class="mb-0">{{ $activity->region }}</p>
                        </div>
                        @endif
                        @if($activity->meeting_point)
                        <div class="col-12 mb-3">
                            <strong>Point de rendez-vous :</strong>
                            <p class="mb-0">{{ $activity->meeting_point }}</p>
                        </div>
                        @endif
                        @if($activity->latitude && $activity->longitude)
                        <div class="col-12">
                            <strong>Coordonnées GPS :</strong>
                            <p class="mb-0">{{ $activity->latitude }}, {{ $activity->longitude }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Équipement fourni -->
            @if($activity->equipment_provided && count($activity->equipment_provided) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>Équipement fourni
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($activity->equipment_provided as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Équipement requis -->
            @if($activity->equipment_required && count($activity->equipment_required) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>Équipement requis
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($activity->equipment_required as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Prérequis -->
            @if($activity->prerequisites && count($activity->prerequisites) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clipboard-list me-2"></i>Prérequis
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($activity->prerequisites as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Certifications -->
            @if($activity->certifications && count($activity->certifications) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-certificate me-2"></i>Certifications
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($activity->certifications as $cert)
                            <li>{{ $cert }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Médias -->
            @if($activity->media->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-images me-2"></i>Galerie
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @foreach($activity->media as $media)
                            <div class="col-md-4">
                                <img src="{{ asset($media->thumbnail_path ?? $media->path) }}"
                                     alt="{{ $media->name }}"
                                     class="img-fluid rounded"
                                     style="height: 150px; width: 100%; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Inscriptions récentes -->
            @if($activity->registrations->count() > 0)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Inscriptions récentes
                    </h5>
                    <a href="{{ route('activity-registrations.index', ['activity_id' => $activity->id]) }}" class="btn btn-sm btn-primary">
                        Voir toutes
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activity->registrations as $registration)
                                    <tr>
                                        <td>
                                            @if($registration->appUser)
                                                {{ $registration->appUser->name }}
                                            @else
                                                {{ $registration->guest_name }}
                                            @endif
                                        </td>
                                        <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @if($registration->status === 'pending')
                                                <span class="badge bg-warning">En attente</span>
                                            @elseif($registration->status === 'confirmed')
                                                <span class="badge bg-success">Confirmée</span>
                                            @elseif($registration->status === 'completed')
                                                <span class="badge bg-primary">Terminée</span>
                                            @else
                                                <span class="badge bg-danger">Annulée</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('activity-registrations.show', $registration) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Opérateur -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-route me-2"></i>Opérateur
                    </h5>
                </div>
                <div class="card-body">
                    <h6>{{ $activity->tourOperator->name }}</h6>
                    @if($activity->tourOperator->email)
                        <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $activity->tourOperator->email }}</p>
                    @endif
                    @if($activity->tourOperator->phone)
                        <p class="mb-1"><i class="fas fa-phone me-2"></i>{{ $activity->tourOperator->phone }}</p>
                    @endif
                    <a href="{{ route('tour-operators.show', $activity->tourOperator->id) }}" class="btn btn-sm btn-outline-primary mt-2">
                        Voir l'opérateur
                    </a>
                </div>
            </div>

            <!-- Statistiques inscriptions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Total</span>
                            <strong>{{ $registrationStats['total'] }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>En attente</span>
                            <span class="badge bg-warning">{{ $registrationStats['pending'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Confirmées</span>
                            <span class="badge bg-success">{{ $registrationStats['confirmed'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Terminées</span>
                            <span class="badge bg-primary">{{ $registrationStats['completed'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Annulées</span>
                            <span class="badge bg-danger">{{ $registrationStats['cancelled'] }}</span>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-eye me-2"></i>Vues</span>
                        <strong>{{ $activity->views_count }}</strong>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Dates
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>Créée le :</strong><br>
                        {{ $activity->created_at->format('d/m/Y H:i') }}
                    </p>
                    <p class="mb-0">
                        <strong>Modifiée le :</strong><br>
                        {{ $activity->updated_at->format('d/m/Y H:i') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
