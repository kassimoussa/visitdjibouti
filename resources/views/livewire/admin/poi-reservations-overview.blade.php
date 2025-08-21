<div class="poi-reservations-overview">
    <!-- Statistiques globales -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-primary">{{ $globalStats['total'] }}</div>
                    <small class="text-muted">Total</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-warning">{{ $globalStats['pending'] }}</div>
                    <small class="text-muted">En attente</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-success">{{ $globalStats['confirmed'] }}</div>
                    <small class="text-muted">Confirmées</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-info">{{ $globalStats['today'] }}</div>
                    <small class="text-muted">Aujourd'hui</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-dark">{{ $globalStats['this_week'] }}</div>
                    <small class="text-muted">Cette semaine</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="h4 mb-0 text-secondary">{{ $globalStats['total_people'] }}</div>
                    <small class="text-muted">Personnes</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Top POIs et Statistiques -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trophy me-2 text-warning"></i>POIs les plus réservés
                    </h5>
                </div>
                <div class="card-body">
                    @if($topPois->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($topPois as $index => $item)
                                <div class="list-group-item border-0 px-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                            <div>
                                                <div class="fw-medium">{{ $item['name'] }}</div>
                                                <small class="text-muted">ID: {{ $item['poi']->id }}</small>
                                            </div>
                                        </div>
                                        <span class="badge bg-success">{{ $item['reservations_count'] }} réservations</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <div>Aucune réservation encore</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2 text-info"></i>Statistiques additionnelles
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="text-center">
                                    <div class="h4 mb-0 text-danger">{{ $globalStats['cancelled'] }}</div>
                                    <small class="text-muted">Annulées</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center">
                                <div class="h4 mb-0 text-info">{{ $globalStats['completed'] }}</div>
                                <small class="text-muted">Terminées</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="h4 mb-0 text-primary">{{ $globalStats['this_month'] }}</div>
                        <small class="text-muted">Ce mois-ci</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <label class="form-label">POI</label>
                    <select wire:model.live="poiFilter" class="form-select">
                        <option value="">Tous les POIs</option>
                        @foreach($poisWithReservations as $poi)
                            <option value="{{ $poi->id }}">
                                {{ $poi->translation('fr')->name ?? 'Sans nom' }} ({{ $poi->reservations_count }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Du</label>
                    <input type="date" wire:model.live="dateFromFilter" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Au</label>
                    <input type="date" wire:model.live="dateToFilter" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="searchFilter" 
                           class="form-control" 
                           placeholder="Nom, email, numéro...">
                </div>
                <div class="col-md-1">
                    <label class="form-label">&nbsp;</label>
                    <button wire:click="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i>
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
                                <th>POI</th>
                                <th>Client</th>
                                <th>Date/Heure</th>
                                <th>Personnes</th>
                                <th>Statut</th>
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
                                        <div class="fw-medium">
                                            {{ $reservation->reservable->translation('fr')->name ?? 'Sans nom' }}
                                        </div>
                                        <small class="text-muted">ID: {{ $reservation->reservable_id }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-medium">{{ $reservation->user_name }}</div>
                                        <small class="text-muted">{{ $reservation->user_email }}</small>
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
                                            
                                            <a href="{{ route('pois.show', $reservation->reservable_id) }}" 
                                               class="btn btn-outline-primary" title="Voir POI">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
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
                        <p>Aucune réservation ne correspond aux filtres sélectionnés.</p>
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
                                <strong>POI :</strong> {{ $selectedReservation->reservable->translation('fr')->name ?? 'Sans nom' }}<br>
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
.poi-reservations-overview .table th {
    font-weight: 600;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.poi-reservations-overview .btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.poi-reservations-overview .list-group-item:last-child {
    border-bottom: 0;
}
</style>
@endpush