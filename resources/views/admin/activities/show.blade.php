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
            <div class="d-flex gap-2 align-items-center flex-wrap">
                {!! $activity->status_badge !!}

                @if($activity->is_featured)
                    <span class="badge bg-warning">
                        <i class="fas fa-star"></i> Mise en avant
                    </span>
                @endif

                <span class="badge bg-{{ $activity->difficulty_level === 'easy' ? 'success' : ($activity->difficulty_level === 'moderate' ? 'warning' : ($activity->difficulty_level === 'difficult' ? 'danger' : 'dark')) }}">
                    {{ $activity->difficulty_label }}
                </span>

                @if($activity->weather_dependent)
                    <span class="badge bg-info">
                        <i class="fas fa-cloud-sun"></i> Dépend de la météo
                    </span>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
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

    @php
        $translationFr = $activity->translations->where('locale', 'fr')->first();
        $translationEn = $activity->translations->where('locale', 'en')->first();
    @endphp

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-md-8">
            <!-- Image principale -->
            @if($activity->featuredImage)
            <div class="card mb-4">
                <div class="card-body p-0">
                    <img src="{{ asset($activity->featuredImage->path) }}"
                         alt="{{ $activity->title }}"
                         class="img-fluid w-100 cursor-pointer"
                         style="max-height: 400px; object-fit: cover;"
                         data-bs-toggle="modal"
                         data-bs-target="#imageModal{{ $activity->featuredImage->id }}">
                </div>
            </div>

            <!-- Modal pour l'image principale -->
            <div class="modal fade" id="imageModal{{ $activity->featuredImage->id }}" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <img src="{{ asset($activity->featuredImage->path) }}"
                                 alt="{{ $activity->title }}"
                                 class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Onglets de langue pour le contenu -->
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#content-fr" type="button">
                                <i class="fas fa-flag me-1"></i> Français
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#content-en" type="button">
                                <i class="fas fa-flag me-1"></i> English
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Contenu FR -->
                        <div class="tab-pane fade show active" id="content-fr" role="tabpanel">
                            @if($translationFr)
                                <h3 class="mb-3">{{ $translationFr->title }}</h3>

                                @if($translationFr->short_description)
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>{{ $translationFr->short_description }}</strong>
                                </div>
                                @endif

                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-align-left me-2 text-primary"></i>Description</h5>
                                    <div class="text-muted">{!! nl2br(e($translationFr->description)) !!}</div>
                                </div>

                                @if($translationFr->what_to_bring)
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-shopping-bag me-2 text-success"></i>Quoi apporter</h5>
                                    <div class="text-muted">{!! nl2br(e($translationFr->what_to_bring)) !!}</div>
                                </div>
                                @endif

                                @if($translationFr->meeting_point_description)
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Point de rendez-vous</h5>
                                    <div class="text-muted">{!! nl2br(e($translationFr->meeting_point_description)) !!}</div>
                                </div>
                                @endif

                                @if($translationFr->additional_info)
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-warning"></i>Informations additionnelles</h5>
                                    <div class="text-muted">{!! nl2br(e($translationFr->additional_info)) !!}</div>
                                </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Aucune traduction française disponible
                                </div>
                            @endif
                        </div>

                        <!-- Contenu EN -->
                        <div class="tab-pane fade" id="content-en" role="tabpanel">
                            @if($translationEn)
                                <h3 class="mb-3">{{ $translationEn->title }}</h3>

                                @if($translationEn->short_description)
                                <div class="alert alert-info mb-4">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>{{ $translationEn->short_description }}</strong>
                                </div>
                                @endif

                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-align-left me-2 text-primary"></i>Description</h5>
                                    <div class="text-muted">{!! nl2br(e($translationEn->description)) !!}</div>
                                </div>

                                @if($translationEn->what_to_bring)
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-shopping-bag me-2 text-success"></i>What to bring</h5>
                                    <div class="text-muted">{!! nl2br(e($translationEn->what_to_bring)) !!}</div>
                                </div>
                                @endif

                                @if($translationEn->meeting_point_description)
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Meeting point</h5>
                                    <div class="text-muted">{!! nl2br(e($translationEn->meeting_point_description)) !!}</div>
                                </div>
                                @endif

                                @if($translationEn->additional_info)
                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-info-circle me-2 text-warning"></i>Additional information</h5>
                                    <div class="text-muted">{!! nl2br(e($translationEn->additional_info)) !!}</div>
                                </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No English translation available
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails de l'activité -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations pratiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag fa-2x text-primary me-3"></i>
                                <div>
                                    <small class="text-muted">Prix</small>
                                    <h5 class="mb-0 text-primary">{{ number_format($activity->price, 0, ',', ' ') }} {{ $activity->currency }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-clock fa-2x text-success me-3"></i>
                                <div>
                                    <small class="text-muted">Durée</small>
                                    <h5 class="mb-0 text-success">{{ $activity->formatted_duration }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users fa-2x text-info me-3"></i>
                                <div>
                                    <small class="text-muted">Participants</small>
                                    <h5 class="mb-0 text-info">
                                        {{ $activity->min_participants }}
                                        @if($activity->max_participants)
                                            - {{ $activity->max_participants }}
                                        @else
                                            +
                                        @endif
                                    </h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-user-check fa-2x text-success me-3"></i>
                                <div>
                                    <small class="text-muted">Inscrits actuellement</small>
                                    <h5 class="mb-0 text-success">{{ $activity->current_participants ?? 0 }}/{{ $activity->max_participants ?? '∞' }}</h5>
                                </div>
                            </div>
                        </div>
                        @if($activity->region)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-map-marked-alt fa-2x text-warning me-3"></i>
                                <div>
                                    <small class="text-muted">Région</small>
                                    <h5 class="mb-0 text-warning">{{ $activity->region }}</h5>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($activity->has_age_restrictions)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-birthday-cake fa-2x text-danger me-3"></i>
                                <div>
                                    <small class="text-muted">Restrictions d'âge</small>
                                    <h5 class="mb-0 text-danger">{{ $activity->age_restrictions_text }}</h5>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($activity->location_address || ($activity->latitude && $activity->longitude))
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Localisation</h6>
                    <div class="row">
                        @if($activity->location_address)
                        <div class="col-md-12 mb-2">
                            <i class="fas fa-location-arrow me-2 text-muted"></i>
                            <strong>{{ $activity->location_address }}</strong>
                        </div>
                        @endif
                        @if($activity->latitude && $activity->longitude)
                        <div class="col-md-12">
                            <i class="fas fa-map me-2 text-muted"></i>
                            GPS: {{ $activity->latitude }}, {{ $activity->longitude }}
                        </div>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Équipement et prérequis -->
            <div class="row">
                <!-- Équipement fourni -->
                @if($activity->equipment_provided && count($activity->equipment_provided) > 0)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0 text-success">
                                <i class="fas fa-check-circle me-2"></i>Équipement fourni
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach($activity->equipment_provided as $item)
                                    <li class="mb-2">
                                        <i class="fas fa-check text-success me-2"></i>{{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Équipement requis -->
                @if($activity->equipment_required && count($activity->equipment_required) > 0)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning bg-opacity-10">
                            <h6 class="mb-0 text-warning">
                                <i class="fas fa-exclamation-circle me-2"></i>Équipement requis
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach($activity->equipment_required as $item)
                                    <li class="mb-2">
                                        <i class="fas fa-shopping-bag text-warning me-2"></i>{{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Prérequis physiques -->
                @if($activity->physical_requirements && count($activity->physical_requirements) > 0)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-info bg-opacity-10">
                            <h6 class="mb-0 text-info">
                                <i class="fas fa-clipboard-list me-2"></i>Prérequis physiques
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach($activity->physical_requirements as $item)
                                    <li class="mb-2">
                                        <i class="fas fa-heartbeat text-info me-2"></i>{{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Certifications -->
                @if($activity->certifications_required && count($activity->certifications_required) > 0)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary bg-opacity-10">
                            <h6 class="mb-0 text-primary">
                                <i class="fas fa-certificate me-2"></i>Certifications requises
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach($activity->certifications_required as $cert)
                                    <li class="mb-2">
                                        <i class="fas fa-award text-primary me-2"></i>{{ $cert }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Inclusions -->
                @if($activity->includes && count($activity->includes) > 0)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-success bg-opacity-10">
                            <h6 class="mb-0 text-success">
                                <i class="fas fa-star me-2"></i>Inclus dans l'activité
                            </h6>
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                @foreach($activity->includes as $item)
                                    <li class="mb-2">
                                        <i class="fas fa-plus-circle text-success me-2"></i>{{ $item }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Politique d'annulation -->
            @if($activity->cancellation_policy)
            <div class="card mb-4">
                <div class="card-header bg-danger bg-opacity-10">
                    <h6 class="mb-0 text-danger">
                        <i class="fas fa-ban me-2"></i>Politique d'annulation
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{!! nl2br(e($activity->cancellation_policy)) !!}</p>
                </div>
            </div>
            @endif

            <!-- Galerie d'images -->
            @if($activity->media->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-images me-2"></i>Galerie ({{ $activity->media->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($activity->media as $media)
                            <div class="col-md-4">
                                <img src="{{ asset($media->thumbnail_path ?? $media->path) }}"
                                     alt="{{ $media->original_name ?? 'Image' }}"
                                     class="img-fluid rounded shadow-sm cursor-pointer"
                                     style="width: 100%; height: 150px; object-fit: cover;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#imageModal{{ $media->id }}">
                            </div>

                            <!-- Modal pour chaque image -->
                            <div class="modal fade" id="imageModal{{ $media->id }}" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body p-0">
                                            <img src="{{ asset($media->path) }}"
                                                 alt="{{ $media->original_name ?? 'Image' }}"
                                                 class="img-fluid w-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Inscriptions récentes -->
            @if($activity->registrations->count() > 0)
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-users me-2"></i>Inscriptions récentes
                    </h5>
                    <a href="{{ route('activity-registrations.index', ['activity_id' => $activity->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-2"></i>Voir toutes ({{ $activity->registrations->count() }})
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Participant</th>
                                    <th>Personnes</th>
                                    <th>Date</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activity->registrations->take(5) as $registration)
                                    <tr>
                                        <td>
                                            @if($registration->appUser)
                                                <div>
                                                    <strong>{{ $registration->appUser->name }}</strong>
                                                    <br><small class="text-muted">{{ $registration->appUser->email }}</small>
                                                </div>
                                            @else
                                                <div>
                                                    <strong>{{ $registration->guest_name }}</strong>
                                                    <br><small class="text-muted">{{ $registration->guest_email }}</small>
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $registration->number_of_people }}</span>
                                        </td>
                                        <td>
                                            <div>
                                                {{ $registration->created_at->format('d/m/Y') }}
                                                <br><small class="text-muted">{{ $registration->created_at->format('H:i') }}</small>
                                            </div>
                                        </td>
                                        <td>{!! $registration->status_badge !!}</td>
                                        <td>
                                            <a href="{{ route('activity-registrations.show', $registration) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir">
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
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary bg-opacity-10">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-route me-2"></i>Opérateur de Tour
                    </h6>
                </div>
                <div class="card-body">
                    <h5>{{ $activity->tourOperator->name }}</h5>
                    @if($activity->tourOperator->email)
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            <a href="mailto:{{ $activity->tourOperator->email }}">{{ $activity->tourOperator->email }}</a>
                        </p>
                    @endif
                    @if($activity->tourOperator->phone)
                        <p class="mb-2">
                            <i class="fas fa-phone me-2 text-success"></i>
                            <a href="tel:{{ $activity->tourOperator->phone }}">{{ $activity->tourOperator->phone }}</a>
                        </p>
                    @endif
                    <a href="{{ route('tour-operators.show', $activity->tourOperator->id) }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                        <i class="fas fa-eye me-2"></i>Voir l'opérateur
                    </a>
                </div>
            </div>

            <!-- Créé par -->
            @if($activity->createdBy)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0 text-info">
                        <i class="fas fa-user-tie me-2"></i>Créé par
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $activity->createdBy->first_name }} {{ $activity->createdBy->last_name }}</strong></p>
                    <p class="mb-0 text-muted">{{ $activity->createdBy->email }}</p>
                </div>
            </div>
            @endif

            <!-- Statistiques inscriptions -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success bg-opacity-10">
                    <h6 class="mb-0 text-success">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques Inscriptions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-3">
                        <div class="col-4">
                            <div class="p-2 bg-success bg-opacity-10 rounded">
                                <h3 class="text-success mb-1">{{ $registrationStats['total'] ?? 0 }}</h3>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-warning bg-opacity-10 rounded">
                                <h3 class="text-warning mb-1">{{ $registrationStats['pending'] ?? 0 }}</h3>
                                <small class="text-muted">Attente</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-primary bg-opacity-10 rounded">
                                <h3 class="text-primary mb-1">{{ $registrationStats['confirmed'] ?? 0 }}</h3>
                                <small class="text-muted">Confirmées</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-flag-checkered me-2 text-info"></i>Terminées</span>
                        <strong class="text-info">{{ $registrationStats['completed'] ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-times-circle me-2 text-danger"></i>Annulées</span>
                        <strong class="text-danger">{{ $registrationStats['cancelled'] ?? 0 }}</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-eye me-2 text-secondary"></i>Vues</span>
                        <strong>{{ number_format($activity->views_count ?? 0) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary bg-opacity-10">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Dates importantes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Créée le</small>
                        <strong>{{ $activity->created_at->format('d/m/Y à H:i') }}</strong>
                        <br><small class="text-muted">{{ $activity->created_at->diffForHumans() }}</small>
                    </div>
                    <hr>
                    <div>
                        <small class="text-muted d-block mb-1">Modifiée le</small>
                        <strong>{{ $activity->updated_at->format('d/m/Y à H:i') }}</strong>
                        <br><small class="text-muted">{{ $activity->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}
</style>
@endsection
