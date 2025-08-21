<div class="reservation-manager">
    <!-- En-tête avec statistiques -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-primary">{{ $stats['total'] }}</div>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-warning">{{ $stats['pending'] }}</div>
                    <small class="text-muted">En attente</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-success">{{ $stats['confirmed'] }}</div>
                    <small class="text-muted">Confirmées</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-danger">{{ $stats['cancelled'] }}</div>
                    <small class="text-muted">Annulées</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-info">{{ $stats['completed'] }}</div>
                    <small class="text-muted">Terminées</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-secondary">{{ $stats['total_people'] }}</div>
                    <small class="text-muted">Personnes</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Statut</label>
                    <select wire:model.live="statusFilter" class="form-select">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="confirmed">Confirmées</option>
                        <option value="cancelled">Annulées</option>
                        <option value="completed">Terminées</option>
                        <option value="no_show">Absent</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date de réservation</label>
                    <input type="date" wire:model.live="dateFilter" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="searchFilter" 
                           class="form-control" 
                           placeholder="Nom, email, numéro de confirmation...">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times me-1"></i>Effacer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Table des réservations -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            @if($reservations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Confirmation</th>
                                <th>Client</th>
                                <th>Date/Heure</th>
                                <th>Personnes</th>
                                <th>Statut</th>
                                <th>Paiement</th>
                                <th>Créée le</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reservations as $reservation)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $reservation->confirmation_number }}</div>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $reservation->user_name }}</div>
                                        <small class="text-muted">{{ $reservation->user_email }}</small>
                                        @if($reservation->user_phone)
                                            <br><small class="text-muted">{{ $reservation->user_phone }}</small>
                                        @endif
                                        @if($reservation->appUser)
                                            <span class="badge bg-primary ms-1">Inscrit</span>
                                        @else
                                            <span class="badge bg-secondary ms-1">Invité</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div>{{ $reservation->reservation_date->format('d/m/Y') }}</div>
                                        @if($reservation->reservation_time)
                                            <small class="text-muted">{{ $reservation->reservation_time->format('H:i') }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $reservation->number_of_people }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $this->getStatusBadgeClass($reservation->status) }}">
                                            {{ $this->getStatusLabel($reservation->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($reservation->payment_amount)
                                            <div>{{ number_format($reservation->payment_amount, 0, ',', ' ') }} DJF</div>
                                            <span class="badge bg-{{ $reservation->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                {{ $reservation->payment_status === 'paid' ? 'Payé' : 'En attente' }}
                                            </span>
                                        @else
                                            <span class="text-muted">Gratuit</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $reservation->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if($reservation->status === 'pending')
                                                <button wire:click="openActionModal({{ $reservation->id }}, 'confirm')" 
                                                        class="btn btn-success" title="Confirmer">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            @endif
                                            
                                            @if(in_array($reservation->status, ['pending', 'confirmed']))
                                                <button wire:click="openActionModal({{ $reservation->id }}, 'cancel')" 
                                                        class="btn btn-danger" title="Annuler">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                            
                                            @if($reservation->status === 'confirmed')
                                                <button wire:click="openActionModal({{ $reservation->id }}, 'complete')" 
                                                        class="btn btn-info" title="Marquer terminée">
                                                    <i class="fas fa-flag-checkered"></i>
                                                </button>
                                            @endif
                                            
                                            <button class="btn btn-outline-primary" title="Voir détails" 
                                                    data-bs-toggle="collapse" 
                                                    data-bs-target="#details-{{ $reservation->id }}">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <!-- Ligne de détails -->
                                <tr class="collapse" id="details-{{ $reservation->id }}">
                                    <td colspan="8" class="bg-light">
                                        <div class="p-3">
                                            <div class="row">
                                                @if($reservation->special_requirements)
                                                    <div class="col-md-6">
                                                        <strong>Exigences spéciales :</strong>
                                                        <p class="mb-1">{{ $reservation->special_requirements }}</p>
                                                    </div>
                                                @endif
                                                @if($reservation->notes)
                                                    <div class="col-md-6">
                                                        <strong>Notes :</strong>
                                                        <p class="mb-1">{{ $reservation->notes }}</p>
                                                    </div>
                                                @endif
                                                @if($reservation->cancellation_reason)
                                                    <div class="col-md-6">
                                                        <strong>Raison d'annulation :</strong>
                                                        <p class="mb-1">{{ $reservation->cancellation_reason }}</p>
                                                    </div>
                                                @endif
                                                <div class="col-md-6">
                                                    <strong>Dernière mise à jour :</strong>
                                                    <p class="mb-1">{{ $reservation->updated_at->format('d/m/Y à H:i') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="card-footer bg-transparent">
                    {{ $reservations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="text-muted">
                        <i class="fas fa-calendar-times fa-3x mb-3"></i>
                        <h5>Aucune réservation trouvée</h5>
                        <p>{{ $reservableType === 'poi' ? 'Ce POI' : 'Cet événement' }} n'a pas encore de réservations.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal d'action -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5)">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            @switch($actionType)
                                @case('confirm')
                                    Confirmer la réservation
                                    @break
                                @case('cancel')
                                    Annuler la réservation
                                    @break
                                @case('complete')
                                    Marquer comme terminée
                                    @break
                            @endswitch
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($selectedReservation)
                            <div class="mb-3">
                                <strong>Réservation :</strong> {{ $selectedReservation->confirmation_number }}<br>
                                <strong>Client :</strong> {{ $selectedReservation->user_name }}<br>
                                <strong>Email :</strong> {{ $selectedReservation->user_email }}<br>
                                <strong>Date :</strong> {{ $selectedReservation->reservation_date->format('d/m/Y') }}
                                @if($selectedReservation->reservation_time)
                                    {{ $selectedReservation->reservation_time->format('H:i') }}
                                @endif
                            </div>
                        @endif
                        
                        @if($actionType === 'cancel')
                            <div class="mb-3">
                                <label class="form-label">Raison de l'annulation (optionnel)</label>
                                <textarea wire:model="actionReason" class="form-control" rows="3" 
                                         placeholder="Précisez la raison de l'annulation..."></textarea>
                            </div>
                        @endif
                        
                        <div class="alert alert-info">
                            @switch($actionType)
                                @case('confirm')
                                    Êtes-vous sûr de vouloir confirmer cette réservation ?
                                    @break
                                @case('cancel')
                                    Êtes-vous sûr de vouloir annuler cette réservation ? Cette action ne peut pas être annulée.
                                    @break
                                @case('complete')
                                    Marquer cette réservation comme terminée ?
                                    @break
                            @endswitch
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            Annuler
                        </button>
                        <button type="button" class="btn btn-{{ $actionType === 'cancel' ? 'danger' : 'primary' }}" 
                                wire:click="confirmAction">
                            @switch($actionType)
                                @case('confirm')
                                    Confirmer
                                    @break
                                @case('cancel')
                                    Annuler la réservation
                                    @break
                                @case('complete')
                                    Marquer terminée
                                    @break
                            @endswitch
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
<style>
.reservation-manager .table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.reservation-manager .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}
</style>
@endpush