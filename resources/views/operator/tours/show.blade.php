@extends('operator.layouts.app')

@section('title', $tour->title)
@section('page-title', 'Détails du Tour')

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
                    <li class="breadcrumb-item active">{{ Str::limit($tour->title, 40) }}</li>
                </ol>
            </nav>
            <h2 class="mb-1">{{ $tour->title }}</h2>
            <div class="d-flex align-items-center gap-3">
                {!! $tour->status_badge !!}
                @if($tour->is_featured)
                    <span class="badge bg-warning">
                        <i class="fas fa-star me-1"></i>
                        Mis en avant
                    </span>
                @endif
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Modifié {{ $tour->updated_at->diffForHumans() }}
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if(in_array($tour->status, ['draft', 'rejected']))
                <a href="{{ route('operator.tours.edit', $tour) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Modifier
                </a>
                <form action="{{ route('operator.tours.submit-for-approval', $tour) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir soumettre ce tour pour approbation ?');">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-paper-plane me-2"></i>
                        Soumettre pour Approbation
                    </button>
                </form>
            @elseif($tour->status === 'pending_approval')
                <button class="btn btn-warning" disabled>
                    <i class="fas fa-clock me-2"></i>
                    En Attente d'Approbation
                </button>
            @elseif($tour->status === 'approved')
                <span class="badge bg-success p-2">
                    <i class="fas fa-check-circle me-1"></i>
                    Tour Approuvé et Publié
                </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($tour->status === 'rejected' && $tour->rejection_reason)
        <div class="alert alert-danger">
            <h5 class="alert-heading"><i class="fas fa-times-circle me-2"></i>Tour Rejeté</h5>
            <hr>
            <p class="mb-0"><strong>Raison du rejet :</strong> {{ $tour->rejection_reason }}</p>
            <small class="text-muted">Veuillez modifier votre tour en tenant compte de ces remarques, puis resoumettez-le.</small>
        </div>
    @endif

    <div class="row">
        <!-- Tour Details -->
        <div class="col-lg-8">
            <!-- Workflow Information -->
            @if($tour->created_by_operator_user_id)
            <div class="card mb-4 border-{{ $tour->status === 'approved' ? 'success' : ($tour->status === 'rejected' ? 'danger' : 'warning') }}">
                <div class="card-header bg-{{ $tour->status === 'approved' ? 'success' : ($tour->status === 'rejected' ? 'danger' : 'warning') }} bg-opacity-10">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations d'Approbation
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user me-2 text-primary"></i>Créé par:</strong><br>
                            <span class="ms-4">{{ $tour->createdBy->name ?? 'N/A' }}</span>
                            @if($tour->createdBy)
                                <br><small class="text-muted ms-4">{{ $tour->createdBy->email }}</small>
                            @endif
                        </div>
                        @if($tour->submitted_at)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-paper-plane me-2 text-info"></i>Soumis le:</strong><br>
                            <span class="ms-4">{{ $tour->submitted_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @endif
                        @if($tour->approved_at && $tour->approvedBy)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user-check me-2 text-success"></i>Approuvé par:</strong><br>
                            <span class="ms-4">{{ $tour->approvedBy->name }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-calendar-check me-2 text-success"></i>Date d'approbation:</strong><br>
                            <span class="ms-4">{{ $tour->approved_at->format('d/m/Y à H:i') }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            <!-- Tour Image and Basic Info -->
            <div class="card mb-4">
                <div class="card-body">
                    @if($tour->featuredImage)
                        <div class="mb-4">
                            <img src="{{ $tour->featuredImage->getImageUrl() }}"
                                 alt="{{ $tour->title }}"
                                 class="img-fluid rounded"
                                 style="width: 100%; height: 300px; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Galerie d'images -->
                    @if($tour->media && $tour->media->count() > 0)
                        <div class="mb-4">
                            <h6><i class="fas fa-images me-2 text-primary"></i>Galerie ({{ $tour->media->count() }} images)</h6>
                            <div class="row g-2">
                                @foreach($tour->media as $media)
                                    <div class="col-md-3">
                                        <img src="{{ $media->getImageUrl() }}"
                                             alt="{{ $media->translation('fr')->alt_text ?? $tour->title }}"
                                             class="img-fluid rounded"
                                             style="width: 100%; height: 120px; object-fit: cover; cursor: pointer;">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-clock me-2 text-primary"></i>Durée</h6>
                            <p class="mb-3">
                                <strong>{{ $tour->formatted_duration }}</strong>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-marker-alt me-2 text-primary"></i>Point de rencontre</h6>
                            <p class="mb-3">
                                {{ $tour->meeting_point_address ?? 'Non spécifié' }}
                            </p>
                        </div>
                    </div>

                    @if($tour->short_description)
                        <div class="mb-4">
                            <h6><i class="fas fa-info-circle me-2 text-primary"></i>Description Courte</h6>
                            <p class="text-muted">{{ $tour->short_description }}</p>
                        </div>
                    @endif

                    @if($tour->description)
                        <div class="mb-4">
                            <h6><i class="fas fa-align-left me-2 text-primary"></i>Description Complète</h6>
                            <div class="content">
                                {!! nl2br(e($tour->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($tour->itinerary)
                        <div class="mb-4">
                            <h6><i class="fas fa-route me-2 text-primary"></i>Itinéraire</h6>
                            <div class="content">
                                {!! nl2br(e($tour->itinerary)) !!}
                            </div>
                        </div>
                    @endif

                    @if($tour->includes && count($tour->includes) > 0)
                        <div class="mb-4">
                            <h6><i class="fas fa-check-circle me-2 text-success"></i>Inclus</h6>
                            <ul class="list-unstyled">
                                @foreach($tour->includes as $item)
                                    <li><i class="fas fa-check text-success me-2"></i>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if($tour->requirements && count($tour->requirements) > 0)
                        <div class="mb-4">
                            <h6><i class="fas fa-exclamation-triangle me-2 text-warning"></i>Exigences</h6>
                            <ul class="list-unstyled">
                                @foreach($tour->requirements as $item)
                                    <li><i class="fas fa-circle-notch text-warning me-2"></i>{{ $item }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tour Details Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-info me-2"></i>
                        Informations Détaillées
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>Type:</strong>
                            <span class="badge bg-info ms-2">{{ $tour->type_label }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Niveau de difficulté:</strong>
                            <span class="badge bg-secondary ms-2">{{ $tour->difficulty_label }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Prix:</strong>
                            <span class="text-primary fw-bold ms-2">{{ $tour->formatted_price }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Participants:</strong>
                            <span class="ms-2">
                                Min: {{ $tour->min_participants ?? 'N/A' }} /
                                Max: {{ $tour->max_participants ?? 'Illimité' }}
                            </span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Restrictions d'âge:</strong>
                            <span class="ms-2">{{ $tour->age_restrictions_text }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Dépend de la météo:</strong>
                            <span class="ms-2">
                                @if($tour->weather_dependent)
                                    <i class="fas fa-check text-success"></i> Oui
                                @else
                                    <i class="fas fa-times text-danger"></i> Non
                                @endif
                            </span>
                        </div>
                    </div>

                    @if($tour->cancellation_policy)
                        <div class="mt-3 p-3 bg-light rounded">
                            <strong><i class="fas fa-file-contract me-2"></i>Politique d'annulation:</strong>
                            <p class="mb-0 mt-2">{{ $tour->cancellation_policy }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>
                        <i class="fas fa-calendar-check me-2"></i>
                        Réservations Récentes
                    </h5>
                    <a href="{{ route('operator.tour-reservations.index', ['tour_id' => $tour->id]) }}" class="btn btn-sm btn-primary">
                        <i class="fas fa-list me-1"></i>
                        Voir toutes
                    </a>
                </div>
                <div class="card-body">
                    @if($tour->reservations()->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Client</th>
                                        <th>Participants</th>
                                        <th>Date réservation</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tour->reservations()->latest()->take(5)->get() as $reservation)
                                        <tr>
                                            <td>
                                                {{ $reservation->appUser->name ?? $reservation->guest_name ?? 'N/A' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ $reservation->number_of_people }}
                                                </span>
                                            </td>
                                            <td>{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                @php
                                                    $statusBadge = match($reservation->status) {
                                                        'pending' => 'bg-warning',
                                                        'confirmed' => 'bg-success',
                                                        'completed' => 'bg-info',
                                                        'cancelled_by_user' => 'bg-danger',
                                                        'cancelled_by_operator' => 'bg-secondary',
                                                        default => 'bg-secondary'
                                                    };
                                                    $statusLabel = match($reservation->status) {
                                                        'pending' => 'En attente',
                                                        'confirmed' => 'Confirmé',
                                                        'completed' => 'Terminé',
                                                        'cancelled_by_user' => 'Annulé',
                                                        'cancelled_by_operator' => 'Annulé',
                                                        default => $reservation->status
                                                    };
                                                @endphp
                                                <span class="badge {{ $statusBadge }}">
                                                    {{ $statusLabel }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('operator.tour-reservations.show', $reservation) }}" class="btn btn-sm btn-outline-primary" title="Voir">
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
                            <h5 class="text-muted">Aucune réservation</h5>
                            <p class="text-muted mb-0">Les réservations pour ce tour apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistics Sidebar -->
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
                            <h3 class="text-success mb-1">{{ $reservationStats['total'] ?? 0 }}</h3>
                            <small class="text-muted">Réservations Totales</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-primary mb-1">{{ $reservationStats['confirmed'] ?? 0 }}</h3>
                            <small class="text-muted">Confirmées</small>
                        </div>
                        <div class="col-4">
                            <h3 class="text-warning mb-1">{{ $reservationStats['pending'] ?? 0 }}</h3>
                            <small class="text-muted">En attente</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="text-info">
                                <strong>{{ $tour->current_participants ?? 0 }}</strong>
                                <br><small>Participants Actuels</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-secondary">
                                <strong>{{ $tour->max_participants ?? '∞' }}</strong>
                                <br><small>Capacité Max</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Target Info (POI or Event) -->
            @if($tour->target)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-link me-2"></i>
                            Cible du Tour
                        </h5>
                    </div>
                    <div class="card-body">
                        <p><strong>Type:</strong> {{ class_basename($tour->target_type) }}</p>
                        <p><strong>Nom:</strong> {{ $tour->target->title ?? 'N/A' }}</p>
                        @if($tour->target_type === 'App\\Models\\Poi')
                            <a href="#" class="btn btn-sm btn-outline-info w-100">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                Voir le POI
                            </a>
                        @elseif($tour->target_type === 'App\\Models\\Event')
                            <a href="{{ route('operator.events.show', $tour->target) }}" class="btn btn-sm btn-outline-info w-100">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Voir l'événement
                            </a>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Tour Management -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-cogs me-2"></i>
                        Gestion
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if(in_array($tour->status, ['draft', 'rejected']))
                            <a href="{{ route('operator.tours.edit', $tour) }}" class="btn btn-warning w-100">
                                <i class="fas fa-edit me-2"></i>
                                Modifier
                            </a>
                            <form action="{{ route('operator.tours.submit-for-approval', $tour) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir soumettre ce tour pour approbation ?');">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-paper-plane me-2"></i>
                                    Soumettre pour Approbation
                                </button>
                            </form>
                        @elseif($tour->status === 'pending_approval')
                            <button class="btn btn-warning w-100" disabled>
                                <i class="fas fa-clock me-2"></i>
                                En Attente d'Approbation
                            </button>
                            <small class="text-muted text-center">Votre tour est en cours d'examen par l'administration</small>
                        @elseif($tour->status === 'approved')
                            <div class="alert alert-success mb-0 text-center">
                                <i class="fas fa-check-circle me-2"></i>
                                <strong>Tour Approuvé et Publié</strong>
                                <br><small>Visible publiquement</small>
                            </div>
                        @endif

                        <hr>

                        <a href="{{ route('operator.tours.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
