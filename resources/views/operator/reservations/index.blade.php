@extends('operator.layouts.app')

@section('title', 'Réservations')
@section('page-title', 'Gestion des Réservations')

@section('content')
<div class="operator-fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mes Réservations</h2>
            <p class="text-muted mb-0">Gérez toutes les réservations de vos événements et tours</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('operator.reservations.export') }}" class="operator-btn btn-success">
                <i class="fas fa-download me-2"></i>
                Exporter
            </a>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="operator-card mb-4">
        <div class="operator-card-body">
            <form method="GET" action="{{ route('operator.reservations.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                   class="operator-form-control"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nom, email, numéro...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="operator-form-control">
                            <option value="">Tous</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="operator-form-control">
                            <option value="">Tous</option>
                            <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Événements</option>
                            <option value="tour" {{ request('type') == 'tour' ? 'selected' : '' }}>Tours</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date</label>
                        <select name="date_filter" class="operator-form-control">
                            <option value="">Toutes</option>
                            <option value="today" {{ request('date_filter') == 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                            <option value="week" {{ request('date_filter') == 'week' ? 'selected' : '' }}>Cette semaine</option>
                            <option value="month" {{ request('date_filter') == 'month' ? 'selected' : '' }}>Ce mois</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Tri</label>
                        <select name="sort" class="operator-form-control">
                            <option value="recent" {{ request('sort') == 'recent' ? 'selected' : '' }}>Plus récent</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Plus ancien</option>
                            <option value="amount_desc" {{ request('sort') == 'amount_desc' ? 'selected' : '' }}>Montant ↓</option>
                            <option value="amount_asc" {{ request('sort') == 'amount_asc' ? 'selected' : '' }}>Montant ↑</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="operator-btn btn-outline-primary">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('operator.reservations.index') }}" class="operator-btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="operator-stats-card small">
                <div class="operator-stats-icon primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <h4>{{ $statistics['total'] }}</h4>
                <p>Total</p>
                <small class="text-muted">{{ number_format($statistics['total_revenue'], 0, ',', ' ') }} DJF</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="operator-stats-card small">
                <div class="operator-stats-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>{{ $statistics['pending'] }}</h4>
                <p>En attente</p>
                <small class="text-warning">À traiter</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="operator-stats-card small">
                <div class="operator-stats-icon success">
                    <i class="fas fa-check"></i>
                </div>
                <h4>{{ $statistics['confirmed'] }}</h4>
                <p>Confirmées</p>
                <small class="text-success">Validées</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="operator-stats-card small">
                <div class="operator-stats-icon danger">
                    <i class="fas fa-times"></i>
                </div>
                <h4>{{ $statistics['cancelled'] }}</h4>
                <p>Annulées</p>
                <small class="text-danger">Remboursées</small>
            </div>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="operator-card">
        <div class="operator-card-header">
            <h5>
                <i class="fas fa-list me-2"></i>
                Liste des Réservations
                <span class="badge bg-secondary ms-2">{{ $reservations->total() }}</span>
            </h5>
            @if($reservations->where('status', 'pending')->count() > 0)
                <div class="operator-card-actions">
                    <form action="{{ route('operator.reservations.bulk-confirm') }}" method="POST" class="d-inline">
                        @csrf
                        <input type="hidden" name="filter" value="{{ http_build_query(request()->all()) }}">
                        <button type="submit" class="btn btn-sm btn-success" title="Confirmer toutes les réservations en attente">
                            <i class="fas fa-check-double me-1"></i>
                            Confirmer en attente
                        </button>
                    </form>
                </div>
            @endif
        </div>
        <div class="operator-card-body">
            @if($reservations->count() > 0)
                <div class="operator-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numéro</th>
                                <th>Client</th>
                                <th>Événement/Tour</th>
                                <th>Date</th>
                                <th>Participants</th>
                                <th>Montant</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                                <tr>
                                    <td>
                                        <div>
                                            <code class="text-primary">{{ $reservation->confirmation_number }}</code>
                                            <br>
                                            <small class="text-muted">{{ $reservation->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $reservation->user_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $reservation->user_email }}</small>
                                            @if($reservation->phone_number)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    {{ $reservation->phone_number }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($reservation->reservable)
                                            <div>
                                                <strong>{{ $reservation->reservable->translation(session('locale', 'fr'))->title ?? $reservation->reservable->title ?? 'N/A' }}</strong>
                                                <br>
                                                <small class="badge bg-info">
                                                    {{ class_basename($reservation->reservable_type) }}
                                                </small>
                                                @if($reservation->reservable_type === 'App\\Models\\Event' && $reservation->reservable->category)
                                                    <br>
                                                    <small class="text-muted">
                                                        {{ $reservation->reservable->category->translation(session('locale', 'fr'))->name ?? $reservation->reservable->category->name }}
                                                    </small>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted">Élément supprimé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            @if($reservation->reservation_date)
                                                <strong>{{ $reservation->reservation_date->format('d/m/Y') }}</strong>
                                                @if($reservation->reservation_time)
                                                    <br><small class="text-muted">{{ $reservation->reservation_time->format('H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">Non spécifié</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <span class="badge bg-secondary fs-6">{{ $reservation->number_of_people }}</span>
                                            @if($reservation->adults > 0 || $reservation->children > 0)
                                                <br>
                                                <small class="text-muted">
                                                    @if($reservation->adults > 0){{ $reservation->adults }}A @endif
                                                    @if($reservation->children > 0){{ $reservation->children }}E @endif
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ number_format($reservation->payment_amount, 0, ',', ' ') }} DJF</strong>
                                            @if($reservation->payment_method)
                                                <br>
                                                <small class="text-muted">{{ ucfirst($reservation->payment_method) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="operator-badge status-{{ $reservation->status }}">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                        @if($reservation->status === 'pending')
                                            <br>
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                Action requise
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operator.reservations.show', $reservation) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($reservation->status === 'pending')
                                                <form action="{{ route('operator.reservations.confirm', $reservation) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-success"
                                                            title="Confirmer la réservation">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            @if(in_array($reservation->status, ['pending', 'confirmed']))
                                                <form action="{{ route('operator.reservations.cancel', $reservation) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger"
                                                            title="Annuler la réservation"
                                                            onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $reservations->firstItem() }} à {{ $reservations->lastItem() }} sur {{ $reservations->total() }} réservations
                    </div>
                    {{ $reservations->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune réservation trouvée</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'type', 'date_filter']))
                            Modifiez vos filtres pour voir plus de réservations
                        @else
                            Les réservations de vos événements apparaîtront ici
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'type', 'date_filter']))
                        <a href="{{ route('operator.reservations.index') }}" class="operator-btn btn-outline-primary">
                            <i class="fas fa-times me-2"></i>
                            Effacer les filtres
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection