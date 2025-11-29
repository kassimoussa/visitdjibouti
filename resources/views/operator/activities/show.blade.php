@extends('operator.layouts.app')

@section('title', 'Détails de l\'Activité')

@section('page-title', 'Détails de l\'Activité')

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
                    <li class="breadcrumb-item active">{{ Str::limit($activity->title, 40) }}</li>
                </ol>
            </nav>
            <h2 class="mb-1">{{ $activity->title }}</h2>
            <div class="d-flex align-items-center gap-3">
                {!! $activity->status_badge !!}
                @if($activity->is_featured)
                    <span class="badge bg-warning">
                        <i class="fas fa-star me-1"></i>
                        Mis en avant
                    </span>
                @endif
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Modifié {{ $activity->updated_at->diffForHumans() }}
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('operator.activity-registrations.index', ['activity_id' => $activity->id]) }}" class="btn btn-outline-primary">
                <i class="fas fa-user-check me-2"></i>
                Inscriptions
            </a>
            <a href="{{ route('operator.activities.edit', $activity) }}" class="btn btn-warning">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            @if($activity->status !== 'draft')
                <form action="{{ route('operator.activities.toggle-status', $activity) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-{{ $activity->status === 'active' ? 'warning' : 'success' }}">
                        <i class="fas fa-{{ $activity->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                        {{ $activity->status === 'active' ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Activity Details -->
        <div class="col-lg-8">
            <!-- Images -->
            @if($activity->featuredImage || ($activity->media && $activity->media->count() > 0))
            <div class="card mb-4">
                <div class="card-body">
                    @if($activity->featuredImage)
                        <div class="mb-4">
                            <h6 class="mb-3"><i class="fas fa-image me-2 text-primary"></i>Image Principale</h6>
                            <img src="{{ asset($activity->featuredImage->path) }}"
                                 alt="{{ $activity->title }}"
                                 class="img-fluid rounded shadow-sm"
                                 style="width: 100%; height: 400px; object-fit: cover;">
                        </div>
                    @endif

                    @if($activity->media && $activity->media->count() > 0)
                        <div>
                            <h6 class="mb-3"><i class="fas fa-images me-2 text-primary"></i>Galerie ({{ $activity->media->count() }} images)</h6>
                            <div class="row g-3">
                                @foreach($activity->media as $media)
                                    <div class="col-md-3">
                                        <img src="{{ asset($media->thumbnail_path ?? $media->path) }}"
                                             alt="{{ $activity->title }}"
                                             class="img-fluid rounded shadow-sm"
                                             style="width: 100%; height: 150px; object-fit: cover; cursor: pointer;"
                                             data-bs-toggle="modal"
                                             data-bs-target="#imageModal{{ $media->id }}">

                                        <!-- Modal pour l'image -->
                                        <div class="modal fade" id="imageModal{{ $media->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-lg modal-dialog-centered">
                                                <div class="modal-content">
                                                    <div class="modal-body p-0">
                                                        <img src="{{ asset($media->path) }}" class="img-fluid w-100">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Contenus Multilingues -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-globe me-2"></i>Contenu Traduit</h5>
                </div>
                <div class="card-body">
                    <!-- Onglets de langue -->
                    <ul class="nav nav-tabs mb-4" role="tablist">
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

                    <div class="tab-content">
                        <!-- Contenu FR -->
                        <div class="tab-pane fade show active" id="content-fr">
                            @php
                                $translationFr = $activity->translation('fr');
                            @endphp

                            @if($translationFr)
                                <div class="mb-4">
                                    <h6 class="text-primary"><i class="fas fa-heading me-2"></i>Titre</h6>
                                    <p class="h5 mb-0">{{ $translationFr->title }}</p>
                                </div>

                                @if($translationFr->short_description)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Description Courte</h6>
                                        <p class="text-muted">{{ $translationFr->short_description }}</p>
                                    </div>
                                @endif

                                @if($translationFr->description)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-align-left me-2"></i>Description Complète</h6>
                                        <div class="content bg-light p-3 rounded">
                                            {!! nl2br(e($translationFr->description)) !!}
                                        </div>
                                    </div>
                                @endif

                                @if($translationFr->what_to_bring)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-backpack me-2"></i>Quoi Apporter</h6>
                                        <div class="content bg-light p-3 rounded">
                                            {!! nl2br(e($translationFr->what_to_bring)) !!}
                                        </div>
                                    </div>
                                @endif

                                @if($translationFr->meeting_point_description)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-map-marked-alt me-2"></i>Point de Rendez-vous</h6>
                                        <div class="content bg-light p-3 rounded">
                                            {!! nl2br(e($translationFr->meeting_point_description)) !!}
                                        </div>
                                    </div>
                                @endif

                                @if($translationFr->additional_info)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-clipboard-list me-2"></i>Informations Additionnelles</h6>
                                        <div class="content bg-light p-3 rounded">
                                            {!! nl2br(e($translationFr->additional_info)) !!}
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Aucun contenu en français disponible
                                </div>
                            @endif
                        </div>

                        <!-- Contenu EN -->
                        <div class="tab-pane fade" id="content-en">
                            @php
                                $translationEn = $activity->translation('en');
                            @endphp

                            @if($translationEn)
                                <div class="mb-4">
                                    <h6 class="text-primary"><i class="fas fa-heading me-2"></i>Title</h6>
                                    <p class="h5 mb-0">{{ $translationEn->title }}</p>
                                </div>

                                @if($translationEn->short_description)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-info-circle me-2"></i>Short Description</h6>
                                        <p class="text-muted">{{ $translationEn->short_description }}</p>
                                    </div>
                                @endif

                                @if($translationEn->description)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-align-left me-2"></i>Full Description</h6>
                                        <div class="content bg-light p-3 rounded">
                                            {!! nl2br(e($translationEn->description)) !!}
                                        </div>
                                    </div>
                                @endif

                                @if($translationEn->what_to_bring)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-backpack me-2"></i>What to Bring</h6>
                                        <div class="content bg-light p-3 rounded">
                                            {!! nl2br(e($translationEn->what_to_bring)) !!}
                                        </div>
                                    </div>
                                @endif

                                @if($translationEn->meeting_point_description)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-map-marked-alt me-2"></i>Meeting Point</h6>
                                        <div class="content bg-light p-3 rounded">
                                            {!! nl2br(e($translationEn->meeting_point_description)) !!}
                                        </div>
                                    </div>
                                @endif

                                @if($translationEn->additional_info)
                                    <div class="mb-4">
                                        <h6 class="text-primary"><i class="fas fa-clipboard-list me-2"></i>Additional Information</h6>
                                        <div class="content bg-light p-3 rounded">
                                            {!! nl2br(e($translationEn->additional_info)) !!}
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No English content available
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Équipement -->
            @if(($activity->equipment_provided && count($activity->equipment_provided) > 0) || ($activity->equipment_required && count($activity->equipment_required) > 0))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Équipement</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($activity->equipment_provided && count($activity->equipment_provided) > 0)
                        <div class="col-md-6 mb-3">
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <h6 class="text-success mb-3"><i class="fas fa-check-circle me-2"></i>Fourni</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($activity->equipment_provided as $item)
                                        <li class="mb-2"><i class="fas fa-check text-success me-2"></i>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif

                        @if($activity->equipment_required && count($activity->equipment_required) > 0)
                        <div class="col-md-6 mb-3">
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <h6 class="text-warning mb-3"><i class="fas fa-exclamation-circle me-2"></i>À Apporter</h6>
                                <ul class="list-unstyled mb-0">
                                    @foreach($activity->equipment_required as $item)
                                        <li class="mb-2"><i class="fas fa-circle-notch text-warning me-2"></i>{{ $item }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Prérequis -->
            @if(($activity->physical_requirements && count($activity->physical_requirements) > 0) || ($activity->certifications_required && count($activity->certifications_required) > 0))
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clipboard-check me-2"></i>Prérequis</h5>
                </div>
                <div class="card-body">
                    @if($activity->physical_requirements && count($activity->physical_requirements) > 0)
                    <div class="mb-4">
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <h6 class="text-danger mb-3"><i class="fas fa-heartbeat me-2"></i>Condition Physique</h6>
                            <ul class="mb-0">
                                @foreach($activity->physical_requirements as $req)
                                    <li class="mb-1">{{ $req }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif

                    @if($activity->certifications_required && count($activity->certifications_required) > 0)
                    <div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <h6 class="text-info mb-3"><i class="fas fa-certificate me-2"></i>Certifications</h6>
                            <ul class="mb-0">
                                @foreach($activity->certifications_required as $cert)
                                    <li class="mb-1">{{ $cert }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Inclus -->
            @if($activity->includes && count($activity->includes) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-check-double me-2"></i>Inclus dans l'Activité</h5>
                </div>
                <div class="card-body">
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <ul class="list-unstyled mb-0 row">
                            @foreach($activity->includes as $item)
                                <li class="col-md-6 mb-2"><i class="fas fa-check text-primary me-2"></i>{{ $item }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Inscriptions Récentes -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-check me-2"></i>
                        Inscriptions Récentes
                    </h5>
                    <a href="{{ route('operator.activity-registrations.index', ['activity_id' => $activity->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-1"></i>
                        Voir toutes
                    </a>
                </div>
                <div class="card-body">
                    @if($activity->registrations()->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Participants</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($activity->registrations()->latest()->take(5)->get() as $registration)
                                        <tr>
                                            <td>
                                                {{ $registration->appUser ? $registration->appUser->name : $registration->guest_name }}
                                                <br>
                                                <small class="text-muted">{{ $registration->appUser ? $registration->appUser->email : $registration->guest_email }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $registration->number_of_participants }}
                                                </span>
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
                                                <a href="{{ route('operator.activity-registrations.show', $registration) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune inscription</h5>
                            <p class="text-muted mb-0">Les inscriptions pour cette activité apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Section Commentaires -->
            <x-operator.comments-section
                :comments="$activity->approvedRootComments"
                title="Commentaires des utilisateurs"
                :limit="5"
                :viewAllUrl="route('operator.activities.comments', $activity)" />
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Statistiques -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Statistiques
                    </h5>
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
                            <div class="p-2 bg-primary bg-opacity-10 rounded">
                                <h3 class="text-primary mb-1">{{ $registrationStats['confirmed'] ?? 0 }}</h3>
                                <small class="text-muted">Confirmées</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-warning bg-opacity-10 rounded">
                                <h3 class="text-warning mb-1">{{ $registrationStats['pending'] ?? 0 }}</h3>
                                <small class="text-muted">Attente</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="text-info">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h4 class="mb-0">{{ $activity->current_participants ?? 0 }}</h4>
                                <small class="text-muted">Participants Actuels</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-secondary">
                                <i class="fas fa-user-friends fa-2x mb-2"></i>
                                <h4 class="mb-0">{{ $activity->max_participants ?? '∞' }}</h4>
                                <small class="text-muted">Capacité Max</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <i class="fas fa-eye fa-2x mb-2 text-muted"></i>
                        <h4 class="mb-0">{{ $activity->views_count ?? 0 }}</h4>
                        <small class="text-muted">Vues</small>
                    </div>
                </div>
            </div>

            <!-- Informations de l'Activité -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Prix</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-tag fa-2x text-primary me-3"></i>
                            <strong class="h4 mb-0 text-primary">{{ number_format($activity->price, 0, ',', ' ') }} {{ $activity->currency }}</strong>
                        </div>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Durée</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-2x text-warning me-3"></i>
                            <strong>{{ $activity->formatted_duration }}</strong>
                        </div>
                    </div>

                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Difficulté</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-mountain fa-2x me-3 text-{{ $activity->difficulty_level === 'easy' ? 'success' : ($activity->difficulty_level === 'moderate' ? 'warning' : 'danger') }}"></i>
                            <span class="badge bg-{{ $activity->difficulty_level === 'easy' ? 'success' : ($activity->difficulty_level === 'moderate' ? 'warning' : 'danger') }} fs-6">
                                {{ $activity->difficulty_label }}
                            </span>
                        </div>
                    </div>

                    @if($activity->region)
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Région</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-map fa-2x text-success me-3"></i>
                            <strong>{{ $activity->region }}</strong>
                        </div>
                    </div>
                    @endif

                    @if($activity->location_address)
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Lieu</small>
                        <div class="d-flex align-items-start">
                            <i class="fas fa-map-marker-alt fa-2x text-danger me-3"></i>
                            <p class="mb-0">{{ $activity->location_address }}</p>
                        </div>
                    </div>
                    @endif

                    @if($activity->has_age_restrictions)
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Restrictions d'âge</small>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-user-clock fa-2x text-info me-3"></i>
                            <strong>{{ $activity->age_restrictions_text }}</strong>
                        </div>
                    </div>
                    @endif

                    @if($activity->weather_dependent)
                    <div class="mb-3">
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-cloud-sun me-2"></i>
                            <small>Dépendant de la météo</small>
                        </div>
                    </div>
                    @endif

                    @if($activity->cancellation_policy)
                    <div>
                        <small class="text-muted d-block mb-2">Politique d'annulation</small>
                        <div class="bg-light p-2 rounded">
                            <small>{{ $activity->cancellation_policy }}</small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions de Gestion -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Gestion</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('operator.activities.edit', $activity) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Modifier
                        </a>

                        @if($activity->status !== 'draft')
                            <form action="{{ route('operator.activities.toggle-status', $activity) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-{{ $activity->status === 'active' ? 'warning' : 'success' }} w-100">
                                    <i class="fas fa-{{ $activity->status === 'active' ? 'pause' : 'play' }} me-2"></i>
                                    {{ $activity->status === 'active' ? 'Désactiver' : 'Activer' }}
                                </button>
                            </form>
                        @endif

                        @if($activity->registrations()->count() === 0)
                            <form action="{{ route('operator.activities.destroy', $activity) }}" method="POST"
                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette activité ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger w-100">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </button>
                            </form>
                        @else
                            <button type="button" class="btn btn-danger w-100" disabled title="Impossible de supprimer une activité avec des inscriptions">
                                <i class="fas fa-trash me-2"></i>Supprimer
                            </button>
                        @endif

                        <hr>

                        <a href="{{ route('operator.activities.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
