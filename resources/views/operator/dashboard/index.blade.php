@extends('operator.layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="fade-in">
    <!-- Welcome Banner -->
    <div class="card mb-4">
        <div class="card-body">
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
        <!-- Total Tours -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon primary">
                    <i class="fas fa-route"></i>
                </div>
                <h3 class="stats-number">{{ $statistics['total_tours'] ?? 0 }}</h3>
                <p class="stats-label">Tours Guidés</p>
                <small class="text-success">
                    <i class="fas fa-check me-1"></i>
                    {{ $statistics['active_tours'] ?? 0 }} approuvés
                </small>
            </div>
        </div>

        <!-- Total Activities -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon warning">
                    <i class="fas fa-hiking"></i>
                </div>
                <h3 class="stats-number">{{ $statistics['total_activities'] ?? 0 }}</h3>
                <p class="stats-label">Activités</p>
                <small class="text-success">
                    <i class="fas fa-arrow-up me-1"></i>
                    {{ $statistics['active_activities'] ?? 0 }} actives
                </small>
            </div>
        </div>

        <!-- Total Reservations -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon success">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="stats-number">{{ $statistics['total_reservations'] ?? 0 }}</h3>
                <p class="stats-label">Réservations Total</p>
                <small class="text-warning">
                    <i class="fas fa-clock me-1"></i>
                    {{ $statistics['pending_reservations'] ?? 0 }} en attente
                </small>
            </div>
        </div>

        <!-- Revenue This Month -->
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon info">
                    <i class="fas fa-coins"></i>
                </div>
                <h3 class="stats-number">{{ number_format($statistics['revenue_this_month'] ?? 0, 0, ',', ' ') }}</h3>
                <p class="stats-label">Revenus ce mois (DJF)</p>
                <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>
                    Performance mensuelle
                </small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Tours -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-route me-2"></i>
                        Tours Récents
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentTours->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Tour</th>
                                        <th>Dates</th>
                                        <th>Statut</th>
                                        <th>Prix</th>
                                        <th>Participants</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTours as $tour)
                                        @php
                                            $translation = $tour->translation(session('locale', 'fr'));
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($tour->featuredImage)
                                                        <img src="{{ $tour->featuredImage->file_path }}"
                                                             alt="{{ $translation->title ?? 'Tour' }}"
                                                             class="rounded me-3"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $translation->title ?? 'N/A' }}</h6>
                                                        <small class="text-muted">{{ Str::limit($translation->short_description ?? '', 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $tour->start_date?->format('d/m/Y') ?? 'N/A' }}</small>
                                                @if($tour->end_date)
                                                    <br><small class="text-muted">au {{ $tour->end_date->format('d/m/Y') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge status-{{ $tour->status }}">
                                                    {{ ucfirst($tour->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($tour->price ?? 0, 0, ',', ' ') }}</strong>
                                                <small class="text-muted">{{ $tour->currency ?? 'DJF' }}</small>
                                            </td>
                                            <td>
                                                <span class="fw-bold">{{ $tour->current_participants ?? 0 }}</span>
                                                @if($tour->max_participants)
                                                    / {{ $tour->max_participants }}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('operator.tours.show', $tour) }}"
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
                            <a href="{{ route('operator.tours.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>
                                Voir tous les tours
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-route fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucun tour récent</h5>
                            <p class="text-muted">Vos derniers tours apparaîtront ici</p>
                            <a href="{{ route('operator.tours.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>
                                Créer un tour
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-hiking me-2"></i>
                        Activités Récentes
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentActivities->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Activité</th>
                                        <th>Région</th>
                                        <th>Statut</th>
                                        <th>Prix</th>
                                        <th>Difficulté</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentActivities as $activity)
                                        @php
                                            $translation = $activity->translation(session('locale', 'fr'));
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($activity->featuredImage)
                                                        <img src="{{ $activity->featuredImage->file_path }}"
                                                             alt="{{ $translation->title ?? 'Activité' }}"
                                                             class="rounded me-3"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-0">{{ $translation->title ?? 'N/A' }}</h6>
                                                        <small class="text-muted">{{ Str::limit($translation->short_description ?? '', 50) }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>{{ $activity->region ?? 'N/A' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge status-{{ $activity->status }}">
                                                    {{ ucfirst($activity->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <strong>{{ number_format($activity->price ?? 0, 0, ',', ' ') }}</strong>
                                                <small class="text-muted">{{ $activity->currency ?? 'DJF' }}</small>
                                            </td>
                                            <td>
                                                @if($activity->difficulty_level)
                                                    <span class="badge bg-{{ $activity->difficulty_level == 'easy' ? 'success' : ($activity->difficulty_level == 'intermediate' ? 'warning' : 'danger') }}">
                                                        {{ ucfirst($activity->difficulty_level) }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('operator.activities.show', $activity) }}"
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
                            <a href="{{ route('operator.activities.index') }}" class="btn btn-outline-primary">
                                <i class="fas fa-list me-2"></i>
                                Voir toutes les activités
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-hiking fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Aucune activité récente</h5>
                            <p class="text-muted">Vos dernières activités apparaîtront ici</p>
                            <a href="{{ route('operator.activities.create') }}" class="btn btn-primary mt-2">
                                <i class="fas fa-plus me-2"></i>
                                Créer une activité
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions & Notifications -->
        <div class="col-lg-4 mb-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($user->canManageTours())
                            <a href="{{ route('operator.tours.index') }}"
                               class="btn btn-outline-primary">
                                <i class="fas fa-route me-2"></i>
                                Gérer les Tours
                            </a>
                            <a href="{{ route('operator.tours.create') }}"
                               class="btn btn-outline-primary">
                                <i class="fas fa-plus me-2"></i>
                                Créer un Tour
                            </a>
                        @endif

                        @if($user->canManageActivities())
                            <a href="{{ route('operator.activities.index') }}"
                               class="btn btn-outline-warning">
                                <i class="fas fa-hiking me-2"></i>
                                Gérer les Activités
                            </a>
                            <a href="{{ route('operator.activities.create') }}"
                               class="btn btn-outline-warning">
                                <i class="fas fa-plus me-2"></i>
                                Créer une Activité
                            </a>
                        @endif

                        @if($user->canViewReservations())
                            <a href="{{ route('operator.reservations.index') }}"
                               class="btn btn-outline-success">
                                <i class="fas fa-ticket-alt me-2"></i>
                                Voir les Réservations
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-bell me-2"></i>
                        Notifications
                    </h5>
                </div>
                <div class="card-body">
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

                    @if($upcomingTours->count() > 0)
                        <div class="alert alert-info">
                            <i class="fas fa-route me-2"></i>
                            <strong>{{ $upcomingTours->count() }}</strong> tour(s) à venir
                            <div class="mt-2">
                                @foreach($upcomingTours->take(3) as $tour)
                                    @php
                                        $translation = $tour->translation(session('locale', 'fr'));
                                    @endphp
                                    <div class="small mb-1">
                                        <strong>{{ $translation->title ?? 'N/A' }}</strong> - {{ $tour->start_date->format('d/m') }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($activeActivities->count() > 0)
                        <div class="alert alert-success">
                            <i class="fas fa-hiking me-2"></i>
                            <strong>{{ $activeActivities->count() }}</strong> activité(s) active(s)
                            <div class="mt-2">
                                <a href="{{ route('operator.activities.index') }}"
                                   class="btn btn-sm btn-success">
                                    Gérer les activités
                                </a>
                            </div>
                        </div>
                    @endif

                    @if($pendingReservationsCount == 0 && $upcomingTours->count() == 0 && $activeActivities->count() == 0)
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
                <div class="card">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-ticket-alt me-2"></i>
                            Réservations Récentes
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
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
                                                <span class="badge status-{{ $reservation->status }}">
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
                            <a href="{{ route('operator.reservations.index') }}" class="btn btn-outline-primary">
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