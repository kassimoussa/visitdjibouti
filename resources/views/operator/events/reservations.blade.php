@extends('operator.layouts.app')

@section('title', 'Réservations - ' . $event->title)
@section('page-title', 'Réservations de l\'Événement')

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
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.events.show', $event) }}">
                            {{ Str::limit($event->title, 30) }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Réservations</li>
                </ol>
            </nav>
            <h2 class="mb-1">Réservations - {{ $event->title }}</h2>
            <p class="text-muted mb-0">Gérez les réservations pour cet événement</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('operator.events.show', $event) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour
            </a>
            <a href="{{ route('operator.events.export-reservations', $event) }}" class="btn btn-success">
                <i class="fas fa-download me-2"></i>
                Exporter CSV
            </a>
        </div>
    </div>

    <!-- Event Info Card -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-3">
                    @if($event->featuredImage)
                        <img src="{{ $event->featuredImage->getImageUrl() }}"
                             alt="{{ $event->title }}"
                             class="img-fluid rounded"
                             style="width: 100%; height: 120px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 120px;">
                            <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-3">
                            <h6 class="text-muted mb-1">Date</h6>
                            <p class="mb-0">
                                <strong>{{ $event->start_date->format('d/m/Y') }}</strong>
                                @if($event->end_date && $event->end_date != $event->start_date)
                                    <br><small>au {{ $event->end_date->format('d/m/Y') }}</small>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-1">Participants</h6>
                            <p class="mb-0">
                                <strong class="text-primary">{{ $event->current_participants }}</strong>
                                @if($event->max_participants)
                                    / {{ $event->max_participants }}
                                @endif
                            </p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-1">Réservations</h6>
                            <p class="mb-0">
                                <strong class="text-success">{{ $reservations->total() }}</strong>
                            </p>
                        </div>
                        <div class="col-md-3">
                            <h6 class="text-muted mb-1">Statut</h6>
                            <p class="mb-0">
                                <span class="badge status-{{ $event->status }}">
                                    {{ ucfirst($event->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reservations List -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-list me-2"></i>
                Liste des Réservations
                <span class="badge bg-secondary ms-2">{{ $reservations->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Numéro</th>
                                <th>Client</th>
                                <th>Date Réservation</th>
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
                                            <code class="text-primary">{{ $reservation->confirmation_number ?? 'N/A' }}</code>
                                            <br>
                                            <small class="text-muted">{{ $reservation->created_at->format('d/m/Y H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $reservation->user_name ?? ($reservation->appUser ? $reservation->appUser->name : 'N/A') }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $reservation->user_email ?? ($reservation->appUser ? $reservation->appUser->email : 'N/A') }}</small>
                                            @if($reservation->user_phone)
                                                <br>
                                                <small class="text-muted">
                                                    <i class="fas fa-phone me-1"></i>
                                                    {{ $reservation->user_phone }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($reservation->reservation_date)
                                                <strong>{{ $reservation->reservation_date->format('d/m/Y') }}</strong>
                                                @if($reservation->reservation_time)
                                                    <br><small class="text-muted">{{ $reservation->reservation_time->format('H:i') }}</small>
                                                @endif
                                            @else
                                                <span class="text-muted">{{ $event->start_date->format('d/m/Y') }}</span>
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
                                            <strong>{{ number_format($reservation->payment_amount ?? 0, 0, ',', ' ') }} DJF</strong>
                                            @if($reservation->payment_method)
                                                <br>
                                                <small class="text-muted">{{ ucfirst($reservation->payment_method) }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge status-{{ $reservation->status }}">
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
                    {{ $reservations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune réservation</h5>
                    <p class="text-muted mb-4">
                        Aucune réservation n'a encore été effectuée pour cet événement
                    </p>
                    <a href="{{ route('operator.events.show', $event) }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour à l'événement
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
