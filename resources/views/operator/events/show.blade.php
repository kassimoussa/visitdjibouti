@extends('operator.layouts.app')

@section('title', $event->title)
@section('page-title', 'Détails de l\'Événement')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.events.index') }}">
                            <i class="fas fa-calendar-alt me-1"></i>
                            Événements
                        </a>
                    </li>
                    <li class="breadcrumb-item active">{{ Str::limit($event->title, 40) }}</li>
                </ol>
            </nav>
            <h2 class="mb-1">{{ $event->title }}</h2>
            <div class="d-flex align-items-center gap-3">
                <span class="badge status-{{ $event->status }}">
                    {{ ucfirst($event->status) }}
                </span>
                @if($event->is_featured)
                    <span class="badge bg-warning">
                        <i class="fas fa-star me-1"></i>
                        Mis en avant
                    </span>
                @endif
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Modifié {{ $event->updated_at->diffForHumans() }}
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($user->canManageEvents())
                <a href="{{ route('operator.events.edit', $event) }}" class="btn btn-warning">
                    <i class="fas fa-edit me-2"></i>
                    Modifier
                </a>
            @endif
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v me-2"></i>
                    Actions
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('operator.events.reservations', $event) }}">
                            <i class="fas fa-ticket-alt me-2"></i>
                            Gérer les réservations
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('operator.events.export-reservations', $event) }}">
                            <i class="fas fa-download me-2"></i>
                            Exporter les réservations
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="#" target="_blank">
                            <i class="fas fa-external-link-alt me-2"></i>
                            Voir sur le site public
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Event Details -->
        <div class="col-lg-8">
            <!-- Event Image and Basic Info -->
            <div class="card mb-4">
                <div class="card-body">
                    @if($event->featuredImage)
                        <div class="mb-4">
                            <img src="{{ $event->featuredImage->getImageUrl() }}"
                                 alt="{{ $event->title }}"
                                 class="img-fluid rounded"
                                 style="width: 100%; height: 300px; object-fit: cover;">
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-calendar-alt me-2 text-primary"></i>Date et Heure</h6>
                            <p class="mb-3">
                                <strong>{{ $event->start_date->format('d/m/Y') }}</strong>
                                @if($event->start_time)
                                    à {{ $event->start_time->format('H:i') }}
                                @endif
                                @if($event->end_date && $event->end_date != $event->start_date)
                                    <br>au {{ $event->end_date->format('d/m/Y') }}
                                    @if($event->end_time)
                                        à {{ $event->end_time->format('H:i') }}
                                    @endif
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-map-marker-alt me-2 text-primary"></i>Localisation</h6>
                            <p class="mb-3">
                                @if($event->location)
                                    {{ $event->location }}
                                    @if($event->region)
                                        <br><small class="text-muted">{{ $event->region }}</small>
                                    @endif
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($event->short_description)
                        <div class="mb-4">
                            <h6><i class="fas fa-info-circle me-2 text-primary"></i>Description Courte</h6>
                            <p class="text-muted">{{ $event->short_description }}</p>
                        </div>
                    @endif

                    @if($event->description)
                        <div class="mb-4">
                            <h6><i class="fas fa-align-left me-2 text-primary"></i>Description Complète</h6>
                            <div class="content">
                                {!! nl2br(e($event->description)) !!}
                            </div>
                        </div>
                    @endif

                    @if($event->category)
                        <div class="mb-4">
                            <h6><i class="fas fa-tag me-2 text-primary"></i>Catégorie</h6>
                            <span class="badge bg-secondary fs-6">
                                {{ $event->category->translation(session('locale', 'fr'))->name ?? $event->category->name }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Pricing Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-tags me-2"></i>
                        Tarification
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center p-3 bg-light rounded">
                                <h4 class="text-primary mb-1">
                                    {{ number_format($event->price_adult, 0, ',', ' ') }} DJF
                                </h4>
                                <small class="text-muted">Adulte</small>
                            </div>
                        </div>
                        @if($event->price_child)
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="text-success mb-1">
                                        {{ number_format($event->price_child, 0, ',', ' ') }} DJF
                                    </h4>
                                    <small class="text-muted">Enfant</small>
                                </div>
                            </div>
                        @endif
                        @if($event->price_group)
                            <div class="col-md-4">
                                <div class="text-center p-3 bg-light rounded">
                                    <h4 class="text-info mb-1">
                                        {{ number_format($event->price_group, 0, ',', ' ') }} DJF
                                    </h4>
                                    <small class="text-muted">Groupe</small>
                                </div>
                            </div>
                        @endif
                    </div>
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
                        <div class="col-6 border-end">
                            <h3 class="text-primary mb-1">{{ $event->current_participants }}</h3>
                            <small class="text-muted">Participants</small>
                            @if($event->max_participants)
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-primary"
                                         style="width: {{ $event->max_participants > 0 ? ($event->current_participants / $event->max_participants * 100) : 0 }}%"></div>
                                </div>
                                <small class="text-muted">sur {{ $event->max_participants }}</small>
                            @endif
                        </div>
                        <div class="col-6">
                            <h3 class="text-success mb-1">{{ number_format($event->total_revenue, 0, ',', ' ') }}</h3>
                            <small class="text-muted">DJF Revenus</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-primary">
                                <strong>{{ $reservationStats['confirmed'] }}</strong>
                                <br><small>Confirmées</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-warning">
                                <strong>{{ $reservationStats['pending'] }}</strong>
                                <br><small>En attente</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-danger">
                                <strong>{{ $reservationStats['cancelled'] }}</strong>
                                <br><small>Annulées</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Reservations -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-ticket-alt me-2"></i>
                        Réservations Récentes
                    </h5>
                    <a href="{{ route('operator.events.reservations', $event) }}" class="btn btn-sm btn-outline-primary">
                        Voir tout
                    </a>
                </div>
                <div class="card-body">
                    @if($recentReservations->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($recentReservations as $reservation)
                                <div class="list-group-item px-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">{{ $reservation->user_name }}</h6>
                                            <small class="text-muted">{{ $reservation->user_email }}</small>
                                            <br>
                                            <small class="badge bg-secondary">{{ $reservation->number_of_people }} pers.</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge status-{{ $reservation->status }} small">
                                                {{ ucfirst($reservation->status) }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $reservation->created_at->format('d/m') }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-ticket-alt fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Aucune réservation</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Event Management -->
            @if($user->canManageEvents())
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-cogs me-2"></i>
                            Gestion
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($event->status == 'draft')
                                <form action="{{ route('operator.events.publish', $event) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-eye me-2"></i>
                                        Publier l'événement
                                    </button>
                                </form>
                            @elseif($event->status == 'published')
                                <form action="{{ route('operator.events.unpublish', $event) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fas fa-eye-slash me-2"></i>
                                        Dépublier
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('operator.events.duplicate', $event) }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-copy me-2"></i>
                                Dupliquer
                            </a>

                            @if($event->status != 'cancelled')
                                <form action="{{ route('operator.events.cancel', $event) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-danger w-100">
                                        <i class="fas fa-ban me-2"></i>
                                        Annuler l'événement
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection