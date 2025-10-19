@extends('operator.layouts.app')

@section('title', 'Réservations de Tours')
@section('page-title', 'Gestion des Réservations de Tours')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Réservations de Tours</h2>
            <p class="text-muted mb-0">Gérez toutes les réservations de vos tours guidés</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <parameter name="card-body">
            <form method="GET" action="{{ route('operator.tour-reservations.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                   class="form-control"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nom, email, téléphone...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="">Tous</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                            <option value="cancelled_by_user" {{ request('status') == 'cancelled_by_user' ? 'selected' : '' }}>Annulé (client)</option>
                            <option value="cancelled_by_operator" {{ request('status') == 'cancelled_by_operator' ? 'selected' : '' }}>Annulé (opérateur)</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Tour</label>
                        <select name="tour_id" class="form-control">
                            <option value="">Tous les tours</option>
                            @foreach($tours as $tour)
                                <option value="{{ $tour->id }}" {{ request('tour_id') == $tour->id ? 'selected' : '' }}>
                                    {{ $tour->translation(session('locale', 'fr'))->title ?? 'Sans titre' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date du</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">au</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-filter me-1"></i>
                                Filtrer
                            </button>
                            <a href="{{ route('operator.tour-reservations.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-1"></i>
                                Réinitialiser
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
            <div class="stats-card small">
                <div class="stats-icon primary">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h4>{{ $statistics['total'] }}</h4>
                <p>Total</p>
                <small class="text-muted">Toutes réservations</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>{{ $statistics['pending'] }}</h4>
                <p>En attente</p>
                <small class="text-warning">À traiter</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check"></i>
                </div>
                <h4>{{ $statistics['confirmed'] }}</h4>
                <p>Confirmées</p>
                <small class="text-success">Validées</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <h4>{{ $statistics['total_participants'] }}</h4>
                <p>Participants</p>
                <small class="text-muted">Total confirmés</small>
            </div>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i>
                Liste des Réservations
                <span class="badge bg-secondary ms-2">{{ $reservations->total() }}</span>
            </h5>
            @if($reservations->where('status', 'pending')->count() > 0)
                <form action="{{ route('operator.tour-reservations.bulk-confirm') }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success" title="Confirmer toutes les réservations en attente">
                        <i class="fas fa-check-double me-1"></i>
                        Confirmer en attente
                    </button>
                </form>
            @endif
        </div>
        <div class="card-body">
            @if($reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Client</th>
                                <th>Tour</th>
                                <th>Participants</th>
                                <th>Date réservation</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                                <tr>
                                    <td>
                                        <code class="text-primary">#{{ $reservation->id }}</code>
                                        <br>
                                        <small class="text-muted">{{ $reservation->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($reservation->appUser)
                                            <div>
                                                <strong><i class="fas fa-user me-1"></i>{{ $reservation->appUser->name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $reservation->appUser->email }}</small>
                                            </div>
                                        @else
                                            <div>
                                                <strong><i class="fas fa-user-tag me-1"></i>{{ $reservation->guest_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $reservation->guest_email }}</small>
                                                @if($reservation->guest_phone)
                                                    <br>
                                                    <small class="text-muted">
                                                        <i class="fas fa-phone me-1"></i>
                                                        {{ $reservation->guest_phone }}
                                                    </small>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($reservation->tour)
                                            <div>
                                                <strong>{{ $reservation->tour->translation(session('locale', 'fr'))->title ?? 'Sans titre' }}</strong>
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $reservation->tour->type }}
                                                </small>
                                            </div>
                                        @else
                                            <span class="text-muted">Tour supprimé</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary fs-6">
                                            <i class="fas fa-users me-1"></i>
                                            {{ $reservation->number_of_people }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $reservation->created_at->format('d/m/Y') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $reservation->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
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
                                                'cancelled_by_user' => 'Annulé (client)',
                                                'cancelled_by_operator' => 'Annulé',
                                                default => $reservation->status
                                            };
                                        @endphp
                                        <span class="badge {{ $statusBadge }}">
                                            {{ $statusLabel }}
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
                                            <a href="{{ route('operator.tour-reservations.show', $reservation) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($reservation->status === 'pending')
                                                <form action="{{ route('operator.tour-reservations.confirm', $reservation) }}" method="POST" class="d-inline">
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
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Annuler la réservation"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#cancelModal{{ $reservation->id }}">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                <!-- Cancel Modal -->
                                @if(in_array($reservation->status, ['pending', 'confirmed']))
                                    <div class="modal fade" id="cancelModal{{ $reservation->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('operator.tour-reservations.cancel', $reservation) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Annuler la réservation</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Êtes-vous sûr de vouloir annuler cette réservation ?</p>
                                                        <div class="mb-3">
                                                            <label class="form-label">Raison de l'annulation (optionnel)</label>
                                                            <textarea name="reason" class="form-control" rows="3" placeholder="Expliquez la raison de l'annulation..."></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                                        <button type="submit" class="btn btn-danger">
                                                            <i class="fas fa-times me-1"></i>
                                                            Annuler la réservation
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune réservation trouvée</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'tour_id', 'date_from', 'date_to']))
                            Modifiez vos filtres pour voir plus de réservations
                        @else
                            Les réservations de vos tours apparaîtront ici
                        @endif
                    </p>
                    @if(request()->hasAny(['search', 'status', 'tour_id', 'date_from', 'date_to']))
                        <a href="{{ route('operator.tour-reservations.index') }}" class="btn btn-outline-primary">
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
