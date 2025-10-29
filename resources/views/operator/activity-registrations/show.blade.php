@extends('operator.layouts.app')

@section('title', 'Détails de l\'Inscription')

@section('page-title', 'Détails de l\'Inscription')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <nav aria-label="breadcrumb" class="mb-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.activity-registrations.index') }}">
                            <i class="fas fa-user-check me-1"></i>
                            Inscriptions
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Inscription #{{ $registration->id }}</li>
                </ol>
            </nav>
            <h2 class="mb-1">Inscription #{{ $registration->id }}</h2>
            <div class="d-flex align-items-center gap-3">
                {!! $registration->status_badge !!}
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    Créée {{ $registration->created_at->diffForHumans() }}
                </small>
            </div>
        </div>
        <div class="d-flex gap-2">
            @if($registration->status === 'pending')
                <form action="{{ route('operator.activity-registrations.confirm', $registration) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success" onclick="return confirm('Confirmer cette inscription ?')">
                        <i class="fas fa-check me-2"></i>Confirmer
                    </button>
                </form>
            @endif
            @if($registration->canBeCancelled())
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cancelModal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
            @endif
            @if($registration->status === 'confirmed')
                <form action="{{ route('operator.activity-registrations.complete', $registration) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Marquer comme terminée ?')">
                        <i class="fas fa-flag-checkered me-2"></i>Marquer terminée
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Inscription Details -->
        <div class="col-lg-8">
            <!-- Activité -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-running me-2"></i>Activité</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start">
                        @if($registration->activity->featuredImage)
                            <img src="{{ asset($registration->activity->featuredImage->thumbnail_path ?? $registration->activity->featuredImage->path) }}"
                                 alt="{{ $registration->activity->title }}"
                                 class="rounded me-3"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @endif
                        <div class="flex-grow-1">
                            <h5>{{ $registration->activity->title }}</h5>
                            <p class="text-muted mb-2">{{ $registration->activity->short_description }}</p>
                            <div class="d-flex gap-3">
                                <span><i class="fas fa-money-bill text-success me-1"></i>{{ number_format($registration->activity->price, 0, ',', ' ') }} DJF</span>
                                <span><i class="fas fa-clock text-primary me-1"></i>{{ $registration->activity->formatted_duration }}</span>
                                <span class="badge bg-{{ $registration->activity->difficulty_level === 'easy' ? 'success' : 'warning' }}">
                                    {{ $registration->activity->difficulty_label }}
                                </span>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('operator.activities.show', $registration->activity) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye me-1"></i>Voir l'activité
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informations Client -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Informations du Client</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nom</label>
                            <div><strong>{{ $registration->customer_name }}</strong></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Email</label>
                            <div>
                                <a href="mailto:{{ $registration->customer_email }}">{{ $registration->customer_email }}</a>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Téléphone</label>
                            <div>
                                @if($registration->customer_phone)
                                    <a href="tel:{{ $registration->customer_phone }}">{{ $registration->customer_phone }}</a>
                                @else
                                    <span class="text-muted">Non fourni</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Type de client</label>
                            <div>
                                @if($registration->appUser)
                                    <span class="badge bg-primary">Utilisateur inscrit</span>
                                @else
                                    <span class="badge bg-secondary">Invité</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails de l'inscription -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-clipboard-list me-2"></i>Détails de l'Inscription</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Nombre de participants</label>
                            <div><span class="badge bg-secondary fs-6">{{ $registration->number_of_people }}</span></div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Prix total</label>
                            <div><strong class="text-success">{{ number_format($registration->total_price, 0, ',', ' ') }} DJF</strong></div>
                        </div>
                        @if($registration->preferred_date)
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-muted">Date préférée suggérée</label>
                            <div><strong>{{ $registration->preferred_date->format('d/m/Y') }}</strong></div>
                        </div>
                        @endif
                    </div>

                    @if($registration->special_requirements)
                    <div class="mb-3">
                        <label class="form-label text-muted">Exigences spéciales</label>
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>{{ $registration->special_requirements }}
                        </div>
                    </div>
                    @endif

                    @if($registration->medical_conditions)
                    <div class="mb-3">
                        <label class="form-label text-muted">Conditions médicales</label>
                        <div class="alert alert-warning mb-0">
                            <i class="fas fa-exclamation-triangle me-2"></i>{{ $registration->medical_conditions }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Historique des statuts -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history me-2"></i>Historique</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <strong>Inscription créée</strong>
                                <div class="text-muted">{{ $registration->created_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>

                        @if($registration->confirmed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success"></div>
                            <div class="timeline-content">
                                <strong>Confirmée</strong>
                                <div class="text-muted">{{ $registration->confirmed_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                        @endif

                        @if($registration->completed_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <strong>Terminée</strong>
                                <div class="text-muted">{{ $registration->completed_at->format('d/m/Y à H:i') }}</div>
                            </div>
                        </div>
                        @endif

                        @if($registration->cancelled_at)
                        <div class="timeline-item">
                            <div class="timeline-marker bg-danger"></div>
                            <div class="timeline-content">
                                <strong>Annulée</strong>
                                <div class="text-muted">{{ $registration->cancelled_at->format('d/m/Y à H:i') }}</div>
                                @if($registration->cancellation_reason)
                                    <div class="alert alert-danger mt-2 mb-0">
                                        <strong>Raison:</strong> {{ $registration->cancellation_reason }}
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Paiement -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-credit-card me-2"></i>Paiement</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('operator.activity-registrations.update-payment', $registration) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Statut du paiement</label>
                            <select name="payment_status" class="form-select">
                                <option value="pending" {{ $registration->payment_status === 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="paid" {{ $registration->payment_status === 'paid' ? 'selected' : '' }}>Payé</option>
                                <option value="refunded" {{ $registration->payment_status === 'refunded' ? 'selected' : '' }}>Remboursé</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Méthode de paiement</label>
                            <input type="text" name="payment_method" class="form-control"
                                   value="{{ $registration->payment_method }}"
                                   placeholder="Ex: Espèces, Carte, Virement">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Mettre à jour
                        </button>
                    </form>
                </div>
            </div>

            <!-- Statut -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Statut</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">Statut actuel</label>
                        <div>{!! $registration->status_badge !!}</div>
                    </div>

                    <div class="d-grid gap-2">
                        @if($registration->status === 'pending')
                            <form action="{{ route('operator.activity-registrations.confirm', $registration) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100" onclick="return confirm('Confirmer cette inscription ?')">
                                    <i class="fas fa-check me-2"></i>Confirmer
                                </button>
                            </form>
                        @endif

                        @if($registration->status === 'confirmed')
                            <form action="{{ route('operator.activity-registrations.complete', $registration) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Marquer comme terminée ?')">
                                    <i class="fas fa-flag-checkered me-2"></i>Marquer terminée
                                </button>
                            </form>
                        @endif

                        @if($registration->canBeCancelled())
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="fas fa-times me-2"></i>Annuler l'inscription
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-cogs me-2"></i>Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="mailto:{{ $registration->customer_email }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-envelope me-2"></i>Envoyer email
                        </a>
                        @if($registration->customer_phone)
                        <a href="tel:{{ $registration->customer_phone }}" class="btn btn-outline-success w-100">
                            <i class="fas fa-phone me-2"></i>Appeler
                        </a>
                        @endif
                        <hr>
                        <a href="{{ route('operator.activity-registrations.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal d'annulation -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('operator.activity-registrations.cancel', $registration) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Annuler l'inscription</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Vous êtes sur le point d'annuler cette inscription.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Raison de l'annulation *</label>
                        <textarea name="cancellation_reason" class="form-control" rows="4" required
                                  placeholder="Veuillez préciser la raison de l'annulation..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times me-2"></i>Confirmer l'annulation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-marker {
    position: absolute;
    left: -26px;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    border: 3px solid #fff;
}
.timeline-content {
    padding-left: 10px;
}
</style>
@endsection
