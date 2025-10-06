@extends('operator.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="operator-fade-in">
    <!-- Welcome Banner -->
    <div class="operator-card mb-4">
        <div class="operator-card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2 class="mb-2">
                        <i class="fas fa-sun text-warning me-2"></i>
                        Bonjour, {{ $user->name }} !
                    </h2>
                    <p class="text-muted mb-0">
                        Bienvenue dans votre espace de gestion {{ $tourOperator->getTranslatedName(session('locale', 'fr')) }}.
                        Voici un aperçu de vos activités récentes.
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="text-muted">
                        <i class="fas fa-calendar me-1"></i>
                        {{ now()->format('d/m/Y') }}
                        <br>
                        <i class="fas fa-clock me-1"></i>
                        {{ now()->format('H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <!-- Total Events -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="operator-stats-card">
                <div class="operator-stats-icon primary">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h3 class="operator-stats-number">{{ $statistics['total_events'] ?? 0 }}</h3>
                <p class="operator-stats-label">Événements Total</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    {{ $statistics['active_events'] ?? 0 }} actifs
                </small>
            </div>
        </div>

        <!-- Total Reservations -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="operator-stats-card">
                <div class="operator-stats-icon success">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="operator-stats-number">{{ $statistics['total_reservations'] ?? 0 }}</h3>
                <p class="operator-stats-label">Réservations Total</p>
                <small class="text-warning">
                    <i class="fas fa-clock me-1"></i>
                    {{ $statistics['pending_reservations'] ?? 0 }} en attente
                </small>
            </div>
        </div>

        <!-- Revenue This Month -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="operator-stats-card">
                <div class="operator-stats-icon info">
                    <i class="fas fa-coins"></i>
                </div>
                <h3 class="operator-stats-number">{{ number_format($statistics['revenue_this_month'] ?? 0, 0, ',', ' ') }}</h3>
                <p class="operator-stats-label">Revenus ce mois (DJF)</p>
                <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>
                    Performance mensuelle
                </small>
            </div>
        </div>

        <!-- Tours -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="operator-stats-card">
                <div class="operator-stats-icon warning">
                    <i class="fas fa-route"></i>
                </div>
                <h3 class="operator-stats-number">{{ $statistics['total_tours'] ?? 0 }}</h3>
                <p class="operator-stats-label">Tours Guidés</p>
                <small class="text-success">
                    <i class="fas fa-check me-1"></i>
                    {{ $statistics['active_tours'] ?? 0 }} actifs
                </small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Events -->
        <div class="col-lg-8 mb-4">
            <div class="operator-card">
                <div class="operator-card-header">
                    <h5>
                        <i class="fas fa-calendar-alt me-2"></i>
                        Événements Récents
                    </h5>
                </div>
                <div class="operator-card-body">
                    @if($recentEvents->count() > 0)
                        <div class="operator-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Événement</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th>Participants</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEvents as $event)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($event->featuredImage)
                                                        <img src="{{ $event->featuredImage->getImageUrl() }}"
                                                             alt="{{ $event->title }}"
                                                             class="rounded me-3"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $event->title }}</h6>
                                                        <small class="text-muted">{{ Str::limit($event->short_description, 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $event->start_date->format('d/m/Y') }}</small>
                                                @if($event->start_time)
                                                    <br><small class="text-muted">{{ $event->start_time->format('H:i') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="operator-badge status-{{ $event->status }}">
                                                    {{ ucfirst($event->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $event->current_participants }}</span>
                                                @if($event->max_participants)
                                                    / {{ $event->max_participants }}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('operator.events.show', $event) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('operator.events.index') }}" class="operator-btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>
                                Voir tous les événements
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun événement récent</h5>
                            <p class="text-muted">Vos derniers événements apparaîtront ici</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Notifications -->
        <div class="col-lg-4 mb-4">
            <!-- Quick Actions -->
            <div class="operator-card mb-4">
                <div class="operator-card-header">
                    <h5>
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h5>
                </div>
                <div class="operator-card-body">
                    <div class="d-grid gap-2">
                        @if($user->canManageEvents())
                            <a href="{{ route('operator.events.index') }}"
                               class="operator-btn btn-outline-primary">
                                <i class="fas fa-calendar-plus me-2"></i>
                                Gérer les Événements
                            </a>
                        @endif

                        @if($user->canViewReservations())
                            <a href="{{ route('operator.reservations.index') }}"
                               class="operator-btn btn-outline-success">
                                <i class="fas fa-ticket-alt me-2"></i>
                                Voir les Réservations
                            </a>
                        @endif

                        @if($user->canManageTours())
                            <a href="{{ route('operator.tours.index') }}"
                               class="operator-btn btn-outline-info">
                                <i class="fas fa-route me-2"></i>
                                Gérer les Tours
                            </a>
                        @endif

                        <a href="{{ route('operator.reports.dashboard') }}"
                           class="operator-btn btn-outline-warning">
                            <i class="fas fa-chart-bar me-2"></i>
                            Voir les Rapports
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="operator-card">
                <div class="operator-card-header">
                    <h5>
                        <i class="fas fa-bell me-2"></i>
                        Notifications
                    </h5>
                </div>
                <div class="operator-card-body">
                    @if($pendingReservationsCount > 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>{{ $pendingReservationsCount }}</strong> réservation(s) en attente de confirmation
                            <div class="mt-2">
                                <a href="{{ route('operator.reservations.index') }}?status=pending"
                                   class="btn btn-sm btn-warning">
                                    Voir les réservations
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($upcomingEvents->count() > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-calendar-check me-2"></i>
                            <strong>{{ $upcomingEvents->count() }}</strong> événement(s) à venir cette semaine
                            <div class="mt-2">
                                @foreach($upcomingEvents->take(3) as $event)
                                    <div class="small mb-1">
                                        <strong>{{ $event->title }}</strong> - {{ $event->start_date->format('d/m') }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($pendingReservationsCount == 0 && $upcomingEvents->count() == 0)
                        <div class="text-center py-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <p class="text-muted mb-0">Tout est à jour !</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reservations -->
    @if($recentReservations->count() > 0)
        <div class="row">
            <div class="col-12">
                <div class="operator-card">
                    <div class="operator-card-header">
                        <h5>
                            <i class="fas fa-ticket-alt me-2"></i>
                            Réservations Récentes
                        </h5>
                    </div>
                    <div class="operator-card-body">
                        <div class="operator-table">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Numéro</th>
                                        <th>Client</th>
                                        <th>Événement/Tour</th>
                                        <th>Date</th>
                                        <th>Personnes</th>
                                        <th>Statut</th>
                                        <th>Montant</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReservations->take(10) as $reservation)
                                        <tr>
                                            <td>
                                                <code>{{ $reservation->confirmation_number }}</code>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $reservation->user_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $reservation->user_email }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($reservation->reservable)
                                                    <div>
                                                        <strong>{{ $reservation->reservable->translation(session('locale', 'fr'))->title ?? 'N/A' }}</strong>
                                                        <br>
                                                        <small class="text-muted">
                                                            {{ class_basename($reservation->reservable_type) }}
                                                        </small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Supprimé</span>
                                                @endif
                                            </td>
                                            <td>
                                                <small>{{ $reservation->reservation_date?->format('d/m/Y') ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary">{{ $reservation->number_of_people }}</span>
                                            </td>
                                            <td>
                                                <span class="operator-badge status-{{ $reservation->status }}">
                                                    {{ ucfirst($reservation->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($reservation->payment_amount, 0, ',', ' ') }} DJF</strong>
                                            </td>
                                            <td>
                                                <a href="{{ route('operator.reservations.show', $reservation) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('operator.reservations.index') }}" class="operator-btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>
                                Voir toutes les réservations
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection