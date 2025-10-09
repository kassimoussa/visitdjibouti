@extends('operator.layouts.app')

@section('title', 'Rapports')
@section('page-title', 'Rapports et Analyses')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Rapports et Statistiques</h2>
            <p class="text-muted mb-0">Analysez les performances de votre entreprise</p>
        </div>
        <div class="d-flex gap-2">
            <div class="dropdown">
                <button class="btn btn-success dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-2"></i>
                    Exporter
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="{{ route('operator.reports.export', ['type' => 'reservations']) }}">
                            <i class="fas fa-ticket-alt me-2"></i>
                            Rapport des réservations
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('operator.reports.export', ['type' => 'revenues']) }}">
                            <i class="fas fa-coins me-2"></i>
                            Rapport des revenus
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('operator.reports.export', ['type' => 'events']) }}">
                            <i class="fas fa-calendar-alt me-2"></i>
                            Rapport des événements
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="{{ route('operator.reports.export', ['type' => 'tours']) }}">
                            <i class="fas fa-route me-2"></i>
                            Rapport des tours
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="#">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">Date de début</label>
                        <input type="date"
                               class="form-control"
                               name="start_date"
                               value="{{ request('start_date', now()->startOfMonth()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Date de fin</label>
                        <input type="date"
                               class="form-control"
                               name="end_date"
                               value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Période prédéfinie</label>
                        <select class="form-control" name="period" id="periodSelect">
                            <option value="">Personnalisée</option>
                            <option value="today" {{ request('period') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>Hier</option>
                            <option value="this_week" {{ request('period') == 'this_week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="last_week" {{ request('period') == 'last_week' ? 'selected' : '' }}>Semaine dernière</option>
                            <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Ce mois</option>
                            <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                            <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Cette année</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>
                            Appliquer
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon success">
                    <i class="fas fa-coins"></i>
                </div>
                <h3 class="stats-number">{{ number_format($metrics['total_revenue'], 0, ',', ' ') }}</h3>
                <p class="stats-label">Revenus Total (DJF)</p>
                <small class="{{ $metrics['revenue_trend'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-{{ $metrics['revenue_trend'] >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                    {{ abs($metrics['revenue_trend']) }}% vs période précédente
                </small>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h3 class="stats-number">{{ $metrics['total_reservations'] }}</h3>
                <p class="stats-label">Réservations</p>
                <small class="{{ $metrics['reservations_trend'] >= 0 ? 'text-success' : 'text-danger' }}">
                    <i class="fas fa-{{ $metrics['reservations_trend'] >= 0 ? 'arrow-up' : 'arrow-down' }} me-1"></i>
                    {{ abs($metrics['reservations_trend']) }}% vs période précédente
                </small>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="stats-number">{{ $metrics['total_participants'] }}</h3>
                <p class="stats-label">Participants</p>
                <small class="text-info">
                    <i class="fas fa-chart-line me-1"></i>
                    Moyenne: {{ round($metrics['avg_participants_per_reservation'], 1) }}
                </small>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stats-card">
                <div class="stats-icon warning">
                    <i class="fas fa-percentage"></i>
                </div>
                <h3 class="stats-number">{{ $metrics['conversion_rate'] }}%</h3>
                <p class="stats-label">Taux de Conversion</p>
                <small class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Confirmées / Total
                </small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Revenue Chart -->
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-line me-2"></i>
                        Évolution des Revenus
                    </h5>
                    <div class="card-actions">
                        <div class="btn-group btn-group-sm">
                            <input type="radio" class="btn-check" name="chartPeriod" id="daily" value="daily" checked>
                            <label class="btn btn-outline-primary" for="daily">Jour</label>

                            <input type="radio" class="btn-check" name="chartPeriod" id="weekly" value="weekly">
                            <label class="btn btn-outline-primary" for="weekly">Semaine</label>

                            <input type="radio" class="btn-check" name="chartPeriod" id="monthly" value="monthly">
                            <label class="btn btn-outline-primary" for="monthly">Mois</label>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div id="revenueChart" style="height: 300px;">
                        <!-- Chart will be rendered here -->
                        <div class="d-flex align-items-center justify-content-center" style="height: 300px;">
                            <div class="text-center">
                                <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Graphique des revenus</p>
                                <small class="text-muted">Données pour la période sélectionnée</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Events -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-star me-2"></i>
                        Événements Populaires
                    </h5>
                </div>
                <div class="card-body">
                    @if($topEvents->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topEvents as $event)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ Str::limit($event->title, 30) }}</h6>
                                            <small class="text-muted">{{ $event->reservations_count }} réservations</small>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-success">
                                                {{ number_format($event->total_revenue, 0, ',', ' ') }} DJF
                                            </div>
                                        </div>
                                    </div>
                                    <div class="progress mt-2" style="height: 4px;">
                                        <div class="progress-bar bg-primary"
                                             style="width: {{ $topEvents->max('reservations_count') > 0 ? ($event->reservations_count / $topEvents->max('reservations_count') * 100) : 0 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-calendar-times fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Aucune donnée pour cette période</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Reservations by Status -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-pie me-2"></i>
                        Répartition des Réservations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-3">
                                <div class="h2 text-success mb-1">{{ $statusBreakdown['confirmed'] }}</div>
                                <small class="text-muted">Confirmées</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success"
                                         style="width: {{ $metrics['total_reservations'] > 0 ? ($statusBreakdown['confirmed'] / $metrics['total_reservations'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3">
                                <div class="h2 text-warning mb-1">{{ $statusBreakdown['pending'] }}</div>
                                <small class="text-muted">En attente</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning"
                                         style="width: {{ $metrics['total_reservations'] > 0 ? ($statusBreakdown['pending'] / $metrics['total_reservations'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="text-center p-3">
                                <div class="h2 text-danger mb-1">{{ $statusBreakdown['cancelled'] }}</div>
                                <small class="text-muted">Annulées</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-danger"
                                         style="width: {{ $metrics['total_reservations'] > 0 ? ($statusBreakdown['cancelled'] / $metrics['total_reservations'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3">
                                <div class="h2 text-info mb-1">{{ $statusBreakdown['completed'] ?? 0 }}</div>
                                <small class="text-muted">Terminées</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-info"
                                         style="width: {{ $metrics['total_reservations'] > 0 ? (($statusBreakdown['completed'] ?? 0) / $metrics['total_reservations'] * 100) : 0 }}%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-clock me-2"></i>
                        Activité Récente
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentReservations->count() > 0)
                        <div class="timeline">
                            @foreach($recentReservations as $reservation)
                                <div class="timeline-item">
                                    <div class="timeline-marker">
                                        <i class="fas fa-{{ $reservation->status === 'confirmed' ? 'check' : ($reservation->status === 'pending' ? 'clock' : 'times') }}"></i>
                                    </div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <strong>{{ $reservation->user_name }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    {{ $reservation->reservable->title ?? 'Activité supprimée' }}
                                                </small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge status-{{ $reservation->status }} small">
                                                    {{ ucfirst($reservation->status) }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $reservation->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-history fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Aucune activité récente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Reports -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-file-alt me-2"></i>
                        Rapports Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('operator.reports.export', ['type' => 'reservations', 'status' => 'pending']) }}"
                               class="btn btn-outline-warning w-100">
                                <i class="fas fa-clock me-2"></i>
                                Réservations en attente
                                <br>
                                <small>({{ $statusBreakdown['pending'] }} items)</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('operator.reports.export', ['type' => 'revenues', 'period' => 'this_month']) }}"
                               class="btn btn-outline-success w-100">
                                <i class="fas fa-coins me-2"></i>
                                Revenus du mois
                                <br>
                                <small>({{ number_format($metrics['total_revenue'], 0, ',', ' ') }} DJF)</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('operator.events.index', ['status' => 'published']) }}"
                               class="btn btn-outline-primary w-100">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Événements actifs
                                <br>
                                <small>({{ $user->managedEvents()->where('status', 'published')->count() }} events)</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('operator.tours.index', ['status' => 'published']) }}"
                               class="btn btn-outline-info w-100">
                                <i class="fas fa-route me-2"></i>
                                Tours disponibles
                                <br>
                                <small>({{ $user->managedTours()->where('status', 'published')->count() }} tours)</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -35px;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--bs-primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: -25px;
    top: 25px;
    width: 1px;
    height: calc(100% + 15px);
    background: #dee2e6;
}

.timeline-item:last-child:before {
    display: none;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-set dates based on period selection
    const periodSelect = document.getElementById('periodSelect');
    const startDate = document.querySelector('input[name="start_date"]');
    const endDate = document.querySelector('input[name="end_date"]');

    periodSelect.addEventListener('change', function() {
        const today = new Date();
        let start, end;

        switch(this.value) {
            case 'today':
                start = end = today;
                break;
            case 'yesterday':
                start = end = new Date(today.getTime() - 24 * 60 * 60 * 1000);
                break;
            case 'this_week':
                start = new Date(today.setDate(today.getDate() - today.getDay()));
                end = new Date();
                break;
            case 'last_week':
                end = new Date(today.setDate(today.getDate() - today.getDay() - 1));
                start = new Date(end.getTime() - 6 * 24 * 60 * 60 * 1000);
                break;
            case 'this_month':
                start = new Date(today.getFullYear(), today.getMonth(), 1);
                end = new Date();
                break;
            case 'last_month':
                start = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                end = new Date(today.getFullYear(), today.getMonth(), 0);
                break;
            case 'this_year':
                start = new Date(today.getFullYear(), 0, 1);
                end = new Date();
                break;
        }

        if (start && end) {
            startDate.value = start.toISOString().split('T')[0];
            endDate.value = end.toISOString().split('T')[0];
        }
    });
});
</script>
@endsection