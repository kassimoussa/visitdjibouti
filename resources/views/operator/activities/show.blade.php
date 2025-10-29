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
            @if($activity->status === 'draft')
                <a href="{{ route('operator.activities.edit', $activity) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Modifier
                </a>
            @endif
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
            <div class="card mb-4">
                <div class="card-body">
                    @if($activity->featuredImage)
                        <div class="mb-4">
                            <img src="{{ asset($activity->featuredImage->path) }}"
                                 alt="{{ $activity->title }}"
                                 class="img-fluid rounded"
                                 style="width: 100%; height: 300px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Galerie -->
                    @if($activity->media && $activity->media->count() > 0)
                        <div class="mb-4">
                            <h6><i class="fas fa-images me-2 text-primary"></i>Galerie ({{ $activity->media->count() }} images)</h6>
                            <div class="row g-2">
                                @foreach($activity->media as $media)
                                    <div class="col-md-3">
                                        <img src="{{ asset($media->thumbnail_path ?? $media->path) }}"
                                             alt="{{ $activity->title }}"
                                             class="img-fluid rounded"
                                             style="width: 100%; height: 120px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($activity->short_description)
                        <div class="mb-4">
                            <h6><i class="fas fa-info-circle me-2 text-primary"></i>Description Courte</h6>
                            <p class="text-muted">{{ $activity->short_description }}</p>
                        </div>
                    @endif

                    @if($activity->description)
                        <div class="mb-4">
                            <h6><i class="fas fa-align-left me-2 text-primary"></i>Description Complète</h6>
                            <div class="content">
                                {!! nl2br(e($activity->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($activity->translation('fr')->what_to_bring)
                        <div class="mb-4">
                            <h6><i class="fas fa-backpack me-2 text-primary"></i>Quoi Apporter</h6>
                            <div class="content">
                                {!! nl2br(e($activity->translation('fr')->what_to_bring)) !!}
                            </div>
                        </div>
                    @endif

                    @if($activity->translation('fr')->additional_info)
                        <div class="mb-4">
                            <h6><i class="fas fa-clipboard-list me-2 text-primary"></i>Informations Additionnelles</h6>
                            <div class="content">
                                {!! nl2br(e($activity->translation('fr')->additional_info)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Équipement -->
            @if(($activity->equipment_provided && count($activity->equipment_provided) > 0) || ($activity->equipment_required && count($activity->equipment_required) > 0))
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-tools me-2"></i>Équipement</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($activity->equipment_provided && count($activity->equipment_provided) > 0)
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-check-circle me-2 text-success"></i>Fourni</h6>
                            <ul class="list-unstyled">
                                @foreach($activity->equipment_provided as $item)
                                    <li><i class="fas fa-check text-success me-2"></i>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        @if($activity->equipment_required && count($activity->equipment_required) > 0)
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-exclamation-circle me-2 text-warning"></i>À Apporter</h6>
                            <ul class="list-unstyled">
                                @foreach($activity->equipment_required as $item)
                                    <li><i class="fas fa-circle-notch text-warning me-2"></i>{{ $item }}</li>
                                @endforeach
                            </ul>
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
                    <h5><i class="fas fa-clipboard-check me-2"></i>Prérequis</h5>
                </div>
                <div class="card-body">
                    @if($activity->physical_requirements && count($activity->physical_requirements) > 0)
                    <div class="mb-3">
                        <h6><i class="fas fa-heartbeat me-2 text-danger"></i>Condition Physique</h6>
                        <ul>
                            @foreach($activity->physical_requirements as $req)
                                <li>{{ $req }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    @if($activity->certifications_required && count($activity->certifications_required) > 0)
                    <div>
                        <h6><i class="fas fa-certificate me-2 text-info"></i>Certifications</h6>
                        <ul>
                            @foreach($activity->certifications_required as $cert)
                                <li>{{ $cert }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Recent Registrations -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>
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
                            <table class="table">
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
                                            <td>{{ $registration->customer_name }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $registration->number_of_people }}
                                                </span>
                                            </td>
                                            <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{!! $registration->status_badge !!}</td>
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
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-line me-2"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h3 class="text-success mb-1">{{ $registrationStats['total'] ?? 0 }}</h3>
                            <small class="text-muted">Inscriptions</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-primary mb-1">{{ $registrationStats['confirmed'] ?? 0 }}</h3>
                            <small class="text-muted">Confirmées</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-warning mb-1">{{ $registrationStats['pending'] ?? 0 }}</h3>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="text-info">
                                <strong>{{ $activity->current_participants ?? 0 }}</strong>
                                <br><small>Participants</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-secondary">
                                <strong>{{ $activity->max_participants ?? '∞' }}</strong>
                                <br><small>Capacité</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Activity Info -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Détails</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block">Prix</small>
                        <strong class="h5 text-primary">{{ number_format($activity->price, 0, ',', ' ') }} {{ $activity->currency }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Durée</small>
                        <strong>{{ $activity->formatted_duration }}</strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Difficulté</small>
                        <span class="badge bg-{{ $activity->difficulty_level === 'easy' ? 'success' : ($activity->difficulty_level === 'moderate' ? 'warning' : 'danger') }}">
                            {{ $activity->difficulty_label }}
                        </span>
                    </div>

                    @if($activity->region)
                    <div class="mb-3">
                        <small class="text-muted d-block">Région</small>
                        <strong>{{ $activity->region }}</strong>
                    </div>
                    @endif

                    @if($activity->location_address)
                    <div class="mb-3">
                        <small class="text-muted d-block">Lieu</small>
                        <p class="mb-0">{{ $activity->location_address }}</p>
                    </div>
                    @endif

                    @if($activity->has_age_restrictions)
                    <div class="mb-3">
                        <small class="text-muted d-block">Restrictions d'âge</small>
                        <strong>{{ $activity->age_restrictions_text }}</strong>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Options -->
            @if($activity->includes && count($activity->includes) > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-check-double me-2"></i>Inclus</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($activity->includes as $item)
                            <li><i class="fas fa-check text-success me-2"></i>{{ $item }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <!-- Management -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-cogs me-2"></i>Gestion</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($activity->status === 'draft')
                            <a href="{{ route('operator.activities.edit', $activity) }}" class="btn btn-warning w-100">
                                <i class="fas fa-edit me-2"></i>Modifier
                            </a>
                        @endif

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
                        @endif

                        <hr>

                        <a href="{{ route('operator.activities.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
