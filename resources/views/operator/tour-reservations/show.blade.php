@extends('operator.layouts.app')

@section('title', 'Détails de la Réservation #' . $reservation->id)
@section('page-title', 'Réservation #' . $reservation->id)

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Réservation #{{ $reservation->id }}</h2>
            <p class="text-muted mb-0">
                Créée le {{ $reservation->created_at->format('d/m/Y à H:i') }}
            </p>
        </div>
        <div>
            <a href="{{ route('operator.tour-reservations.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <!-- Status and Actions Card -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Statut et Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Statut Actuel</h6>
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
                                    'cancelled_by_user' => 'Annulé par le client',
                                    'cancelled_by_operator' => 'Annulé par l\'opérateur',
                                    default => $reservation->status
                                };
                            @endphp
                            <span class="badge {{ $statusBadge }} fs-5">{{ $statusLabel }}</span>
                        </div>
                        <div class="col-md-6 text-end">
                            @if($reservation->status === 'pending')
                                <form action="{{ route('operator.tour-reservations.confirm', $reservation) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success">
                                        <i class="fas fa-check me-2"></i>
                                        Confirmer
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-2"></i>
                                    Annuler
                                </button>
                            @elseif($reservation->status === 'confirmed')
                                <form action="{{ route('operator.tour-reservations.check-in', $reservation) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-check-circle me-2"></i>
                                        Marquer comme terminé
                                    </button>
                                </form>
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                    <i class="fas fa-times me-2"></i>
                                    Annuler
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tour Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-route me-2"></i>
                        Informations du Tour
                    </h5>
                </div>
                <div class="card-body">
                    @if($reservation->tour)
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <h6>Nom du Tour</h6>
                                <p class="mb-0">
                                    <strong>{{ $reservation->tour->translation(session('locale', 'fr'))->title ?? 'Sans titre' }}</strong>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Type</h6>
                                <p class="mb-0">{{ ucfirst($reservation->tour->type) }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Durée</h6>
                                <p class="mb-0">{{ $reservation->tour->duration_hours ?? 'N/A' }} heures</p>
                            </div>
                            @if($reservation->tour->price)
                                <div class="col-md-6 mt-3">
                                    <h6>Prix par personne</h6>
                                    <p class="mb-0">
                                        {{ number_format($reservation->tour->price, 0, ',', ' ') }} {{ $reservation->tour->currency }}
                                    </p>
                                </div>
                            @endif
                            @if($reservation->tour->difficulty_level)
                                <div class="col-md-6 mt-3">
                                    <h6>Niveau de difficulté</h6>
                                    <p class="mb-0">{{ ucfirst($reservation->tour->difficulty_level) }}</p>
                                </div>
                            @endif
                            @if($reservation->tour->meeting_point_address)
                                <div class="col-md-12 mt-3">
                                    <h6>Point de rencontre</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        {{ $reservation->tour->meeting_point_address }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('operator.tours.show', $reservation->tour) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye me-1"></i>
                                Voir le tour
                            </a>
                        </div>
                    @else
                        <p class="text-muted">Tour supprimé</p>
                    @endif
                </div>
            </div>

            <!-- Client Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user me-2"></i>
                        Informations du Client
                    </h5>
                </div>
                <div class="card-body">
                    @if($reservation->appUser)
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Nom</h6>
                                <p class="mb-0">
                                    <i class="fas fa-user-circle me-2"></i>
                                    {{ $reservation->appUser->name }}
                                    <span class="badge bg-info ms-2">Utilisateur inscrit</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Email</h6>
                                <p class="mb-0">
                                    <i class="fas fa-envelope me-2"></i>
                                    <a href="mailto:{{ $reservation->appUser->email }}">{{ $reservation->appUser->email }}</a>
                                </p>
                            </div>
                            @if($reservation->appUser->phone)
                                <div class="col-md-6 mt-3">
                                    <h6>Téléphone</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-phone me-2"></i>
                                        <a href="tel:{{ $reservation->appUser->phone }}">{{ $reservation->appUser->phone }}</a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Nom</h6>
                                <p class="mb-0">
                                    <i class="fas fa-user-tag me-2"></i>
                                    {{ $reservation->guest_name }}
                                    <span class="badge bg-secondary ms-2">Invité</span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <h6>Email</h6>
                                <p class="mb-0">
                                    <i class="fas fa-envelope me-2"></i>
                                    <a href="mailto:{{ $reservation->guest_email }}">{{ $reservation->guest_email }}</a>
                                </p>
                            </div>
                            @if($reservation->guest_phone)
                                <div class="col-md-6 mt-3">
                                    <h6>Téléphone</h6>
                                    <p class="mb-0">
                                        <i class="fas fa-phone me-2"></i>
                                        <a href="tel:{{ $reservation->guest_phone }}">{{ $reservation->guest_phone }}</a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Notes -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sticky-note me-2"></i>
                        Notes
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('operator.tour-reservations.update-notes', $reservation) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Notes internes (visibles uniquement par vous)</label>
                            <textarea name="notes" class="form-control" rows="4" placeholder="Ajoutez des notes sur cette réservation...">{{ $reservation->notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Enregistrer les notes
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Reservation Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Résumé
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6>ID Réservation</h6>
                        <p class="mb-0"><code>#{{ $reservation->id }}</code></p>
                    </div>
                    <div class="mb-3">
                        <h6>Nombre de participants</h6>
                        <p class="mb-0">
                            <span class="badge bg-secondary fs-5">
                                <i class="fas fa-users me-1"></i>
                                {{ $reservation->number_of_people }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <h6>Date de création</h6>
                        <p class="mb-0">{{ $reservation->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <h6>Dernière modification</h6>
                        <p class="mb-0">{{ $reservation->updated_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt me-2"></i>
                        Actions Rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($reservation->status === 'pending')
                            <form action="{{ route('operator.tour-reservations.confirm', $reservation) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-2"></i>
                                    Confirmer la réservation
                                </button>
                            </form>
                        @endif

                        @if($reservation->status === 'confirmed')
                            <form action="{{ route('operator.tour-reservations.check-in', $reservation) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Marquer comme terminé
                                </button>
                            </form>
                        @endif

                        @if(in_array($reservation->status, ['pending', 'confirmed']))
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fas fa-times me-2"></i>
                                Annuler la réservation
                            </button>
                        @endif

                        @if($reservation->appUser)
                            <a href="mailto:{{ $reservation->appUser->email }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-envelope me-2"></i>
                                Contacter le client
                            </a>
                        @elseif($reservation->guest_email)
                            <a href="mailto:{{ $reservation->guest_email }}" class="btn btn-outline-primary w-100">
                                <i class="fas fa-envelope me-2"></i>
                                Contacter le client
                            </a>
                        @endif

                        <a href="{{ route('operator.tour-reservations.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>
                            Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Modal -->
@if(in_array($reservation->status, ['pending', 'confirmed']))
    <div class="modal fade" id="cancelModal" tabindex="-1">
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
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Le nombre de participants sera automatiquement décrémenté.
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Raison de l'annulation</label>
                            <textarea name="reason" class="form-control" rows="3" placeholder="Expliquez la raison de l'annulation au client..." required></textarea>
                            <small class="text-muted">Cette raison sera ajoutée aux notes de la réservation.</small>
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
@endsection
