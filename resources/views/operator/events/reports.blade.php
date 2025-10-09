@extends('operator.layouts.app')

@section('title', 'Rapports - Événements')
@section('page-title', 'Rapports et Statistiques')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Rapports - Événements</h2>
            <p class="text-muted mb-0">Analyses et statistiques détaillées de vos événements</p>
        </div>
        <a href="{{ route('operator.events.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour aux événements
        </a>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.events.reports') }}">
                <div class="row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Date de début</label>
                        <input type="date" class="form-control" name="date_from" value="{{ $dateFrom }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date de fin</label>
                        <input type="date" class="form-control" name="date_to" value="{{ $dateTo }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>
                            Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Event Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon primary">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4>{{ $eventStats['total_events'] }}</h4>
                <p>Total Événements</p>
                <small class="text-muted">Période sélectionnée</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>{{ $eventStats['published_events'] }}</h4>
                <p>Publiés</p>
                <small class="text-success">Actifs</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>{{ $eventStats['upcoming_events'] }}</h4>
                <p>À venir</p>
                <small class="text-warning">Futurs</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon info">
                    <i class="fas fa-history"></i>
                </div>
                <h4>{{ $eventStats['past_events'] }}</h4>
                <p>Terminés</p>
                <small class="text-muted">Passés</small>
            </div>
        </div>
    </div>

    <!-- Reservation Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h4>{{ $reservationStats['total_reservations'] }}</h4>
                <p>Total Réservations</p>
                <small class="text-muted">{{ number_format($reservationStats['total_revenue'], 0, ',', ' ') }} DJF</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check"></i>
                </div>
                <h4>{{ $reservationStats['confirmed_reservations'] }}</h4>
                <p>Confirmées</p>
                <small class="text-success">Validées</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon warning">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <h4>{{ $reservationStats['pending_reservations'] }}</h4>
                <p>En Attente</p>
                <small class="text-warning">À traiter</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <h4>{{ $reservationStats['total_participants'] }}</h4>
                <p>Participants</p>
                <small class="text-muted">Total confirmés</small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-line me-2"></i>
                        Tendances Mensuelles (6 derniers mois)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Revenue Chart -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-dollar-sign me-2"></i>
                        Revenus (6 derniers mois)
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Events -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-star me-2"></i>
                Top 10 - Événements les Plus Populaires
            </h5>
        </div>
        <div class="card-body">
            @if($popularEvents->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Événement</th>
                                <th>Date</th>
                                <th>Réservations</th>
                                <th>Participants</th>
                                <th>Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($popularEvents as $index => $event)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $index + 1 }}</strong>
                                    </td>
                                    <td>
                                        <a href="{{ route('operator.events.show', $event) }}">
                                            {{ $event->title }}
                                        </a>
                                    </td>
                                    <td>
                                        <small>{{ $event->start_date->format('d/m/Y') }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $event->total_reservations }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $event->current_participants }}</span>
                                    </td>
                                    <td>
                                        <span class="badge status-{{ $event->status }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune donnée disponible</h5>
                    <p class="text-muted">Les statistiques apparaîtront une fois que vous aurez des événements avec des réservations</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Monthly Trend Chart
    const monthlyData = @json($monthlyData);
    const months = Object.values(monthlyData).map(d => d.month);
    const events = Object.values(monthlyData).map(d => d.events);
    const reservations = Object.values(monthlyData).map(d => d.reservations);

    new Chart(document.getElementById('monthlyTrendChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [
                {
                    label: 'Événements',
                    data: events,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.3
                },
                {
                    label: 'Réservations',
                    data: reservations,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Revenue Chart
    const revenue = Object.values(monthlyData).map(d => d.revenue);

    new Chart(document.getElementById('revenueChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Revenus (DJF)',
                data: revenue,
                backgroundColor: 'rgba(13, 110, 253, 0.8)',
                borderColor: '#0d6efd',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
});
</script>
@endsection
