@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('page-title', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- POIs Card -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="content-card" style="background-color: #f0f7ff; border-left: 4px solid #3860f8;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Points d'Intérêt</h5>
                    <i class="fas fa-map-marker-alt fa-2x text-primary"></i>
                </div>
                <h2 class="mb-2">{{ $totalPois }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span><i class="fas fa-check-circle text-success"></i> {{ $publishedPois }} publiés</span>
                    <span><i class="fas fa-star text-warning"></i> {{ $featuredPois }} vedettes</span>
                </div>
                <a href="{{ route('pois.index') }}" class="btn btn-sm btn-outline-primary mt-3 w-100">
                    Gérer les POIs
                </a>
            </div>
        </div>

        <!-- Events Card -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="content-card" style="background-color: #fff8f0; border-left: 4px solid #ff9800;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Événements</h5>
                    <i class="fas fa-calendar-alt fa-2x text-warning"></i>
                </div>
                <h2 class="mb-2">{{ $totalEvents }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span><i class="fas fa-calendar-check text-success"></i> {{ $upcomingEvents }} à venir</span>
                    <span><i class="fas fa-calendar-times text-muted"></i> {{ $endedEvents }} terminés</span>
                </div>
                <a href="{{ route('events.index') }}" class="btn btn-sm btn-outline-warning mt-3 w-100">
                    Gérer les événements
                </a>
            </div>
        </div>

        <!-- Reservations Card -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="content-card" style="background-color: #f0fff4; border-left: 4px solid #4caf50;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Réservations</h5>
                    <i class="fas fa-ticket-alt fa-2x text-success"></i>
                </div>
                <h2 class="mb-2">{{ $totalReservations }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span><i class="fas fa-check text-success"></i> {{ $confirmedReservations }} confirmées</span>
                    <span><i class="fas fa-clock text-warning"></i> {{ $pendingReservations }} en attente</span>
                </div>
                <a href="{{ route('reservations.index') }}" class="btn btn-sm btn-outline-success mt-3 w-100">
                    Gérer les réservations
                </a>
            </div>
        </div>

        <!-- App Users Card -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="content-card" style="background-color: #fff0f0; border-left: 4px solid #f44336;">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="mb-0">Utilisateurs App</h5>
                    <i class="fas fa-users fa-2x text-danger"></i>
                </div>
                <h2 class="mb-2">{{ $totalAppUsers }}</h2>
                <div class="d-flex justify-content-between text-muted small">
                    <span><i class="fas fa-user-plus text-success"></i> {{ $newUsersToday }} aujourd'hui</span>
                    <span><i class="fas fa-user-check text-info"></i> {{ $activeAppUsers }} actifs</span>
                </div>
                <a href="{{ route('app-users.index') }}" class="btn btn-sm btn-outline-danger mt-3 w-100">
                    Gérer les utilisateurs
                </a>
            </div>
        </div>
    </div>

    <!-- Secondary Statistics -->
    <div class="row mb-4">
        <!-- Tours Card -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="content-card border-start border-primary border-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Tours</h6>
                        <h3 class="mb-0">{{ $totalTours }}</h3>
                        <small class="text-success">{{ $activeTours }} actifs</small>
                    </div>
                    <i class="fas fa-route fa-2x text-primary opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Tour Operators Card -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="content-card border-start border-info border-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Tour Opérateurs</h6>
                        <h3 class="mb-0">{{ $totalTourOperators }}</h3>
                        <small class="text-success">{{ $activeTourOperators }} actifs</small>
                    </div>
                    <i class="fas fa-building fa-2x text-info opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Revenue Card -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="content-card border-start border-success border-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Revenus</h6>
                        <h3 class="mb-0">{{ number_format($totalRevenue, 0, ',', ' ') }}</h3>
                        <small class="text-muted">DJF</small>
                    </div>
                    <i class="fas fa-dollar-sign fa-2x text-success opacity-50"></i>
                </div>
            </div>
        </div>

        <!-- Media Card -->
        <div class="col-md-3 col-sm-6 mb-4">
            <div class="content-card border-start border-warning border-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted mb-1">Médias</h6>
                        <h3 class="mb-0">{{ $totalMedia }}</h3>
                        <small class="text-success">+{{ $mediaThisMonth }} ce mois</small>
                    </div>
                    <i class="fas fa-images fa-2x text-warning opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
    <div class="row">
        <!-- Reservations Chart -->
        <div class="col-md-8 mb-4">
            <div class="content-card">
                <h4 class="mb-4">
                    <i class="fas fa-chart-line me-2"></i>
                    Réservations (6 derniers mois)
                </h4>
                <div class="chart-container" style="height: 300px;">
                    <canvas id="reservationsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="col-md-4 mb-4">
            <div class="content-card">
                <h4 class="mb-4">
                    <i class="fas fa-clock me-2"></i>
                    Activités récentes
                </h4>
                <div class="activities-list" style="max-height: 300px; overflow-y: auto;">
                    @forelse($recentActivities as $activity)
                        <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                            <div class="flex-shrink-0" style="width: 40px; height: 40px; border-radius: 50%; background-color: rgba(var(--bs-{{ $activity['color'] }}-rgb), 0.1); display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                <i class="fas fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0">{{ $activity['title'] }}</h6>
                                <p class="mb-1 text-muted small">{{ $activity['description'] }}</p>
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $activity['time']->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucune activité récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Content Row -->
    <div class="row">
        <!-- Top POIs -->
        <div class="col-md-6 mb-4">
            <div class="content-card">
                <h4 class="mb-4">
                    <i class="fas fa-trophy me-2"></i>
                    POIs les plus réservés
                </h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Région</th>
                                <th class="text-end">Réservations</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topPois as $poi)
                                <tr>
                                    <td>
                                        <a href="{{ route('pois.show', $poi->id) }}" class="text-decoration-none">
                                            {{ $poi->name ?: 'Sans nom' }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $poi->region }}</span>
                                    </td>
                                    <td class="text-end">
                                        <strong>{{ number_format($poi->reservations_count) }}</strong>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Aucune donnée disponible
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Upcoming Events -->
        <div class="col-md-6 mb-4">
            <div class="content-card">
                <h4 class="mb-4">
                    <i class="fas fa-calendar-check me-2"></i>
                    Événements à venir
                </h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingEventsList as $event)
                                <tr>
                                    <td>
                                        <a href="{{ route('events.show', $event->id) }}" class="text-decoration-none">
                                            {{ Str::limit($event->title ?: 'Sans titre', 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <small>{{ $event->start_date->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ ucfirst($event->status) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">
                                        Aucun événement à venir
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="content-card">
                <h4 class="mb-4">
                    <i class="fas fa-bolt me-2"></i>
                    Actions rapides
                </h4>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('pois.create') }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-plus-circle me-2"></i>
                            Créer un POI
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('events.create') }}" class="btn btn-outline-warning w-100">
                            <i class="fas fa-plus-circle me-2"></i>
                            Créer un événement
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('tours.create') }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-plus-circle me-2"></i>
                            Créer un tour
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('media.index') }}" class="btn btn-outline-info w-100">
                            <i class="fas fa-upload me-2"></i>
                            Upload média
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Reservations Chart
    const ctx = document.getElementById('reservationsChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($reservationsByMonth->pluck('month')) !!},
                datasets: [{
                    label: 'Réservations',
                    data: {!! json_encode($reservationsByMonth->pluck('count')) !!},
                    borderColor: '#3860f8',
                    backgroundColor: 'rgba(56, 96, 248, 0.1)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointBackgroundColor: '#3860f8',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        borderRadius: 8,
                        titleFont: {
                            size: 14
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
</script>
@endpush
@endsection
