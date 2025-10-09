@extends('operator.layouts.app')

@section('title', 'Réservation #' . $reservation->confirmation_number)
@section('page-title', 'Détails de la Réservation')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.reservations.index') }}">
                            <i class="fas fa-ticket-alt me-1"></i>
                            Réservations
                        </a>
                    </li>
                    <li class="breadcrumb-item active">#{{ $reservation->confirmation_number }}</li>
                </ol>
            </nav>
            <h2 class="mb-1">Réservation #{{ $reservation->confirmation_number }}</h2>
            <div class="d-flex align-items-center gap-3">
                <span class="badge status-{{ $reservation->status }}">
                    {{ ucfirst($reservation->status) }}
                </span>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Créée {{ $reservation->created_at->diffForHumans() }}
                </small>
                @if($reservation->updated_at != $reservation->created_at)
                    <small class="text-muted">
                        <i class="fas fa-edit me-1"></i>
                        Modifiée {{ $reservation->updated_at->diffForHumans() }}
                    </small>
                @endif
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($reservation->status === 'pending')
                <form action="{{ route('operator.reservations.confirm', $reservation) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check me-2"></i>
                        Confirmer
                    </button>
                </form>
            @endif

            @if(in_array($reservation->status, ['pending', 'confirmed']))
                <form action="{{ route('operator.reservations.cancel', $reservation) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Êtes-vous sûr de vouloir annuler cette réservation ?')">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </button>
                </form>
            @endif

            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-ellipsis-v me-2"></i>
                    Actions
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="mailto:{{ $reservation->user_email }}?subject=Réservation {{ $reservation->confirmation_number }}">
                            <i class="fas fa-envelope me-2"></i>
                            Envoyer un email
                        </a>
                    </li>
                    @if($reservation->phone_number)
                        <li>
                            <a class="dropdown-item" href="tel:{{ $reservation->phone_number }}">
                                <i class="fas fa-phone me-2"></i>
                                Appeler le client
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Reservation Details -->
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-user me-2"></i>
                        Informations Client
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="mb-3">Contact Principal</h6>
                            <div class="mb-3">
                                <strong>{{ $reservation->user_name }}</strong>
                                <br>
                                <a href="mailto:{{ $reservation->user_email }}" class="text-decoration-none">
                                    <i class="fas fa-envelope me-1"></i>
                                    {{ $reservation->user_email }}
                                </a>
                                @if($reservation->phone_number)
                                    <br>
                                    <a href="tel:{{ $reservation->phone_number }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $reservation->phone_number }}
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="mb-3">Détails de la Réservation</h6>
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Nombre de personnes</small>
                                    <div class="fw-bold">{{ $reservation->number_of_people }}</div>
                                </div>
                                @if($reservation->adults > 0 || $reservation->children > 0)
                                    <div class="col-6">
                                        <small class="text-muted">Répartition</small>
                                        <div class="fw-bold">
                                            @if($reservation->adults > 0){{ $reservation->adults }} Adultes @endif
                                            @if($reservation->children > 0){{ $reservation->children }} Enfants @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($reservation->special_requests)
                        <hr>
                        <h6>Demandes Spéciales</h6>
                        <div class="bg-light p-3 rounded">
                            {{ $reservation->special_requests }}
                        </div>
                    @endif
                </div>
            </div>

            <!-- Event/Tour Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-calendar-alt me-2"></i>
                        Détails de l'Activité
                    </h5>
                </div>
                <div class="card-body">
                    @if($reservation->reservable)
                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-start">
                                    @if($reservation->reservable->featuredImage ?? false)
                                        <img src="{{ $reservation->reservable->featuredImage->getImageUrl() }}"
                                             alt="{{ $reservation->reservable->title }}"
                                             class="rounded me-3"
                                             style="width: 80px; height: 80px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <h5 class="mb-2">{{ $reservation->reservable->translation(session('locale', 'fr'))->title ?? $reservation->reservable->title }}</h5>
                                        <div class="mb-2">
                                            <span class="badge bg-info">
                                                {{ class_basename($reservation->reservable_type) }}
                                            </span>
                                            @if($reservation->reservable_type === 'App\\Models\\Event' && $reservation->reservable->category)
                                                <span class="badge bg-secondary ms-1">
                                                    {{ $reservation->reservable->category->translation(session('locale', 'fr'))->name ?? $reservation->reservable->category->name }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($reservation->reservable->short_description)
                                            <p class="text-muted mb-0">{{ Str::limit($reservation->reservable->short_description, 150) }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h6>Date & Heure</h6>
                                @if($reservation->reservation_date)
                                    <div class="fw-bold">{{ $reservation->reservation_date->format('d/m/Y') }}</div>
                                    @if($reservation->reservation_time)
                                        <div class="text-muted">{{ $reservation->reservation_time->format('H:i') }}</div>
                                    @endif
                                @else
                                    <div class="text-muted">Non spécifié</div>
                                @endif

                                @if($reservation->reservable->location)
                                    <h6 class="mt-3">Lieu</h6>
                                    <div>{{ $reservation->reservable->location }}</div>
                                    @if($reservation->reservable->region)
                                        <small class="text-muted">{{ $reservation->reservable->region }}</small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            L'activité associée à cette réservation a été supprimée.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Payment Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-credit-card me-2"></i>
                        Informations de Paiement
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6>Montant Total</h6>
                            <div class="h4 text-success mb-3">{{ number_format($reservation->payment_amount, 0, ',', ' ') }} DJF</div>
                        </div>
                        <div class="col-md-4">
                            <h6>Méthode de Paiement</h6>
                            <div class="mb-3">
                                @if($reservation->payment_method)
                                    <span class="badge bg-info">{{ ucfirst($reservation->payment_method) }}</span>
                                @else
                                    <span class="text-muted">Non spécifié</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h6>Statut du Paiement</h6>
                            <div>
                                @if($reservation->payment_status)
                                    <span class="badge bg-success">{{ ucfirst($reservation->payment_status) }}</span>
                                @else
                                    <span class="badge bg-warning">En attente</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($reservation->payment_details)
                        <hr>
                        <h6>Détails du Paiement</h6>
                        <div class="bg-light p-3 rounded">
                            <pre class="mb-0">{{ $reservation->payment_details }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Status Timeline -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-history me-2"></i>
                        Historique
                    </h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item {{ $reservation->status === 'cancelled' ? 'text-danger' : ($reservation->status === 'confirmed' ? 'text-success' : 'text-warning') }}">
                            <div class="timeline-marker">
                                <i class="fas fa-{{ $reservation->status === 'cancelled' ? 'times' : ($reservation->status === 'confirmed' ? 'check' : 'clock') }}"></i>
                            </div>
                            <div class="timeline-content">
                                <strong>{{ ucfirst($reservation->status) }}</strong>
                                <br>
                                <small class="text-muted">{{ $reservation->updated_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>

                        <div class="timeline-item text-muted">
                            <div class="timeline-marker">
                                <i class="fas fa-plus"></i>
                            </div>
                            <div class="timeline-content">
                                <strong>Réservation créée</strong>
                                <br>
                                <small>{{ $reservation->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

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
                        @if($reservation->status === 'pending')
                            <form action="{{ route('operator.reservations.confirm', $reservation) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-check me-2"></i>
                                    Confirmer la réservation
                                </button>
                            </form>
                        @endif

                        @if($reservation->status === 'confirmed')
                            <button type="button" class="btn btn-outline-info w-100" data-bs-toggle="modal" data-bs-target="#checkInModal">
                                <i class="fas fa-check-double me-2"></i>
                                Marquer comme présent
                            </button>
                        @endif

                        <a href="mailto:{{ $reservation->user_email }}?subject=Réservation {{ $reservation->confirmation_number }}"
                           class="btn btn-outline-primary w-100">
                            <i class="fas fa-envelope me-2"></i>
                            Envoyer un email
                        </a>

                        @if($reservation->phone_number)
                            <a href="tel:{{ $reservation->phone_number }}" class="btn btn-outline-info w-100">
                                <i class="fas fa-phone me-2"></i>
                                Appeler le client
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-sticky-note me-2"></i>
                        Notes Internes
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('operator.reservations.update-notes', $reservation) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <textarea class="form-control"
                                      name="internal_notes"
                                      rows="4"
                                      placeholder="Ajouter des notes internes...">{{ $reservation->internal_notes }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="fas fa-save me-2"></i>
                            Sauvegarder les notes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Check-in Modal -->
<div class="modal fade" id="checkInModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmer la présence</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('operator.reservations.check-in', $reservation) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p>Confirmez-vous que le client <strong>{{ $reservation->user_name }}</strong> s'est bien présenté pour cette activité ?</p>
                    <div class="mb-3">
                        <label class="form-label">Nombre de participants présents</label>
                        <input type="number"
                               class="form-control"
                               name="actual_participants"
                               value="{{ $reservation->number_of_people }}"
                               min="0"
                               max="{{ $reservation->number_of_people }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notes (optionnel)</label>
                        <textarea class="form-control" name="check_in_notes" rows="3" placeholder="Remarques particulières..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-success">Confirmer la présence</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection