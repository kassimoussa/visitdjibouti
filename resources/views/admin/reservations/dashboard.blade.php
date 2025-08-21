@extends('layouts.admin')

@section('title', 'Tableau de Bord des Réservations')
@section('page-title', 'Tableau de Bord des Réservations')

@section('content')
<div class="container-fluid">
    <!-- Navigation breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('reservations.index') }}">Réservations</a></li>
                    <li class="breadcrumb-item active">Tableau de bord</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Tableau de Bord des Réservations</h1>
                    <p class="text-muted mb-0">Analyses et statistiques détaillées</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reservations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <a href="{{ route('reservations.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i> Exporter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-gradient rounded-circle p-3">
                                <i class="fas fa-calendar-check text-white"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $globalStats['total_reservations'] }}</h4>
                            <p class="text-muted mb-0 small">Total réservations</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-gradient rounded-circle p-3">
                                <i class="fas fa-map-marker-alt text-white"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $globalStats['total_poi_reservations'] }}</h4>
                            <p class="text-muted mb-0 small">Réservations POI</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-gradient rounded-circle p-3">
                                <i class="fas fa-calendar-alt text-white"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $globalStats['total_event_reservations'] }}</h4>
                            <p class="text-muted mb-0 small">Réservations Events</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-gradient rounded-circle p-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $globalStats['total_people'] }}</h4>
                            <p class="text-muted mb-0 small">Total personnes</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques par statut -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Répartition par statut</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6 col-lg-3 mb-3">
                            <div class="text-center">
                                <div class="badge bg-warning fs-4 p-3 rounded-circle mb-2">
                                    {{ $globalStats['pending_reservations'] }}
                                </div>
                                <p class="small text-muted mb-0">En attente</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 mb-3">
                            <div class="text-center">
                                <div class="badge bg-success fs-4 p-3 rounded-circle mb-2">
                                    {{ $globalStats['confirmed_reservations'] }}
                                </div>
                                <p class="small text-muted mb-0">Confirmées</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 mb-3">
                            <div class="text-center">
                                <div class="badge bg-info fs-4 p-3 rounded-circle mb-2">
                                    {{ $globalStats['today_reservations'] }}
                                </div>
                                <p class="small text-muted mb-0">Aujourd'hui</p>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3 mb-3">
                            <div class="text-center">
                                <div class="badge bg-primary fs-4 p-3 rounded-circle mb-2">
                                    {{ $globalStats['week_reservations'] }}
                                </div>
                                <p class="small text-muted mb-0">Cette semaine</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Activité récente</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Aujourd'hui</span>
                        <span class="badge bg-primary">{{ $globalStats['today_reservations'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-muted">Cette semaine</span>
                        <span class="badge bg-info">{{ $globalStats['week_reservations'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Ce mois</span>
                        <span class="badge bg-success">{{ $globalStats['month_reservations'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top POIs et Events -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Top POIs les plus réservés</h5>
                </div>
                <div class="card-body">
                    @if($topPois->count() > 0)
                        @foreach($topPois as $poi)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">{{ $poi['name'] }}</h6>
                                <small class="text-muted">{{ $poi['reservations_count'] }} réservations</small>
                            </div>
                            <a href="{{ route('pois.show', $poi['id']) }}" class="btn btn-sm btn-outline-primary">
                                Voir
                            </a>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Aucune réservation POI trouvée</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Top Events les plus réservés</h5>
                </div>
                <div class="card-body">
                    @if($topEvents->count() > 0)
                        @foreach($topEvents as $event)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <h6 class="mb-0">{{ $event['name'] }}</h6>
                                <small class="text-muted">{{ $event['reservations_count'] }} réservations</small>
                            </div>
                            <a href="{{ route('events.show', $event['id']) }}" class="btn btn-sm btn-outline-success">
                                Voir
                            </a>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Aucune réservation Event trouvée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Réservations récentes -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Réservations récentes</h5>
                </div>
                <div class="card-body">
                    @if($recentReservations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Type</th>
                                        <th>Nom</th>
                                        <th>Client</th>
                                        <th>Date réservation</th>
                                        <th>Statut</th>
                                        <th>Créé le</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReservations as $reservation)
                                    <tr>
                                        <td>
                                            @if(str_contains($reservation->reservable_type, 'Poi'))
                                                <span class="badge bg-primary">POI</span>
                                            @else
                                                <span class="badge bg-success">Event</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reservation->reservable)
                                                @if(str_contains($reservation->reservable_type, 'Poi'))
                                                    {{ $reservation->reservable->translation('fr')->name ?? 'Sans nom' }}
                                                @else
                                                    {{ $reservation->reservable->translation('fr')->title ?? 'Sans nom' }}
                                                @endif
                                            @else
                                                <em>Resource supprimée</em>
                                            @endif
                                        </td>
                                        <td>
                                            @if($reservation->appUser)
                                                {{ $reservation->appUser->name }}
                                            @else
                                                {{ $reservation->guest_name ?? 'Invité' }}
                                            @endif
                                        </td>
                                        <td>{{ $reservation->reservation_date->format('d/m/Y') }}</td>
                                        <td>
                                            @php
                                                $badgeClass = match($reservation->status) {
                                                    'pending' => 'bg-warning',
                                                    'confirmed' => 'bg-success',
                                                    'cancelled' => 'bg-danger',
                                                    'completed' => 'bg-info',
                                                    default => 'bg-secondary'
                                                };
                                                $statusLabel = match($reservation->status) {
                                                    'pending' => 'En attente',
                                                    'confirmed' => 'Confirmée',
                                                    'cancelled' => 'Annulée',
                                                    'completed' => 'Terminée',
                                                    default => $reservation->status
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                                        </td>
                                        <td>{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">Aucune réservation récente trouvée</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection