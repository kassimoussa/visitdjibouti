<div>
    <!-- Statistiques en en-tête -->
    <div class="bg-light p-4 border-bottom">
        <div class="row g-3">
            <div class="col-lg-3 col-md-6">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['total'] }}</h5>
                        <small class="text-muted">Total réservations</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="d-flex align-items-center">
                    <div class="bg-info rounded-circle p-2 me-3">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['total_poi'] }}</h5>
                        <small class="text-muted">POI</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="d-flex align-items-center">
                    <div class="bg-success rounded-circle p-2 me-3">
                        <i class="fas fa-calendar-alt text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['total_event'] }}</h5>
                        <small class="text-muted">Events</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-circle p-2 me-3">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['pending'] }}</h5>
                        <small class="text-muted">En attente</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-6">
                <div class="d-flex align-items-center">
                    <div class="bg-danger rounded-circle p-2 me-3">
                        <i class="fas fa-calendar-day text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['today'] }}</h5>
                        <small class="text-muted">Aujourd'hui</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-1 col-md-6">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary rounded-circle p-2 me-3">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['total_people'] }}</h5>
                        <small class="text-muted">Personnes</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres avancés -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-2">
                <label for="typeFilter" class="form-label">Type</label>
                <select wire:model.live="typeFilter" id="typeFilter" class="form-select">
                    <option value="">Tous</option>
                    <option value="poi">POI seulement</option>
                    <option value="event">Events seulement</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="statusFilter" class="form-label">Statut</label>
                <select wire:model.live="statusFilter" id="statusFilter" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="pending">En attente</option>
                    <option value="confirmed">Confirmée</option>
                    <option value="cancelled">Annulée</option>
                    <option value="completed">Terminée</option>
                    <option value="no_show">Absent</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="resourceFilter" class="form-label">
                    @if($typeFilter === 'poi')
                        POI spécifique
                    @elseif($typeFilter === 'event')
                        Event spécifique
                    @else
                        Resource spécifique
                    @endif
                </label>
                <select wire:model.live="resourceFilter" id="resourceFilter" class="form-select">
                    <option value="">Toutes les resources</option>
                    @foreach($filteredResources as $resource)
                        <option value="{{ $resource['id'] }}">
                            {{ $resource['name'] }} ({{ $resource['reservations_count'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label for="dateFromFilter" class="form-label">Du</label>
                <input wire:model.live="dateFromFilter" type="date" id="dateFromFilter" class="form-control">
            </div>

            <div class="col-md-2">
                <label for="dateToFilter" class="form-label">Au</label>
                <input wire:model.live="dateToFilter" type="date" id="dateToFilter" class="form-control">
            </div>

            <div class="col-md-2">
                <label for="searchFilter" class="form-label">Recherche</label>
                <div class="input-group">
                    <input wire:model.live.debounce.300ms="searchFilter" type="text" id="searchFilter" 
                           class="form-control" placeholder="Nom, email, n°...">
                    @if($searchFilter || $typeFilter || $statusFilter || $resourceFilter || $dateFromFilter || $dateToFilter)
                        <button wire:click="clearFilters" class="btn btn-outline-secondary" type="button">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Top ressources (si pas de filtres spécifiques) -->
    @if(!$typeFilter && !$resourceFilter && !$searchFilter)
    <div class="p-4 bg-light border-bottom">
        <h6 class="mb-3">Top Resources les plus réservées</h6>
        <div class="row g-2">
            @foreach($topResources as $resource)
            <div class="col-auto">
                <button 
                    wire:click="$set('resourceFilter', '{{ $resource['id'] }}')"
                    class="btn btn-sm {{ $resource['type'] === 'poi' ? 'btn-outline-primary' : 'btn-outline-success' }}">
                    {{ $resource['name'] }} ({{ $resource['reservations_count'] }})
                </button>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Table des réservations -->
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th width="60">Type</th>
                    <th>Resource</th>
                    <th>Client</th>
                    <th>Date réservation</th>
                    <th width="80">Personnes</th>
                    <th width="100">Statut</th>
                    <th width="120">Créé le</th>
                    <th width="150">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reservations as $reservation)
                    <tr>
                        <td>
                            @php $typeBadge = $this->getResourceTypeBadge($reservation->reservable_type); @endphp
                            <span class="badge {{ $typeBadge['class'] }}">{{ $typeBadge['label'] }}</span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                @if($reservation->reservable)
                                    <span class="fw-medium">
                                        @if(str_contains($reservation->reservable_type, 'Poi'))
                                            {{ $reservation->reservable->translation('fr')->name ?? 'Sans nom' }}
                                        @else
                                            {{ $reservation->reservable->translation('fr')->title ?? 'Sans nom' }}
                                        @endif
                                    </span>
                                    <small class="text-muted">{{ $reservation->confirmation_number }}</small>
                                @else
                                    <span class="text-muted"><em>Resource supprimée</em></span>
                                    <small class="text-muted">{{ $reservation->confirmation_number }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                @if($reservation->appUser)
                                    <span class="fw-medium">{{ $reservation->appUser->name }}</span>
                                    <small class="text-muted">{{ $reservation->appUser->email }}</small>
                                    <span class="badge bg-info badge-sm">Inscrit</span>
                                @else
                                    <span class="fw-medium">{{ $reservation->guest_name ?? 'Invité' }}</span>
                                    <small class="text-muted">{{ $reservation->guest_email ?? 'Pas d\'email' }}</small>
                                    <span class="badge bg-secondary badge-sm">Invité</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ $reservation->reservation_date->format('d/m/Y') }}</span>
                                @if($reservation->reservation_time)
                                    <small class="text-muted">{{ $reservation->reservation_time }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark">{{ $reservation->number_of_people }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $this->getStatusBadgeClass($reservation->status) }}">
                                {{ $this->getStatusLabel($reservation->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small>{{ $reservation->created_at->format('d/m/Y') }}</small>
                                <small class="text-muted">{{ $reservation->created_at->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group-sm" role="group">
                                @if($reservation->status === 'pending')
                                    <button wire:click="openActionModal({{ $reservation->id }}, 'confirm')" 
                                            class="btn btn-success btn-sm" 
                                            data-bs-toggle="tooltip" title="Confirmer">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                
                                @if(in_array($reservation->status, ['pending', 'confirmed']))
                                    <button wire:click="openActionModal({{ $reservation->id }}, 'cancel')" 
                                            class="btn btn-danger btn-sm" 
                                            data-bs-toggle="tooltip" title="Annuler">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                                
                                @if($reservation->status === 'confirmed')
                                    <button wire:click="openActionModal({{ $reservation->id }}, 'complete')" 
                                            class="btn btn-info btn-sm" 
                                            data-bs-toggle="tooltip" title="Marquer terminée">
                                        <i class="fas fa-flag-checkered"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                <h5>Aucune réservation trouvée</h5>
                                <p>Aucune réservation ne correspond aux filtres sélectionnés.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($reservations->hasPages())
    <div class="p-4 border-top">
        {{ $reservations->links() }}
    </div>
    @endif

    <!-- Modal de confirmation d'action -->
    @if($showModal)
        <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
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
                                <strong>Réservation:</strong> {{ $selectedReservation->confirmation_number }}<br>
                                <strong>Client:</strong> 
                                @if($selectedReservation->appUser)
                                    {{ $selectedReservation->appUser->name }} ({{ $selectedReservation->appUser->email }})
                                @else
                                    {{ $selectedReservation->guest_name }} ({{ $selectedReservation->guest_email }})
                                @endif
                                <br>
                                <strong>Resource:</strong>
                                @if($selectedReservation->reservable)
                                    @if(str_contains($selectedReservation->reservable_type, 'Poi'))
                                        {{ $selectedReservation->reservable->translation('fr')->name ?? 'Sans nom' }}
                                    @else
                                        {{ $selectedReservation->reservable->translation('fr')->title ?? 'Sans nom' }}
                                    @endif
                                @endif
                            </div>

                            @if($actionType === 'cancel')
                                <div class="mb-3">
                                    <label for="actionReason" class="form-label">Raison de l'annulation</label>
                                    <textarea wire:model="actionReason" id="actionReason" class="form-control" rows="3" 
                                              placeholder="Expliquez pourquoi cette réservation est annulée..."></textarea>
                                </div>
                            @endif

                            <p class="text-muted">
                                @switch($actionType)
                                    @case('confirm')
                                        Êtes-vous sûr de vouloir confirmer cette réservation ?
                                        @break
                                    @case('cancel')
                                        Êtes-vous sûr de vouloir annuler cette réservation ? Cette action est irréversible.
                                        @break
                                    @case('complete')
                                        Êtes-vous sûr de vouloir marquer cette réservation comme terminée ?
                                        @break
                                @endswitch
                            </p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Annuler</button>
                        <button type="button" class="btn btn-primary" wire:click="confirmAction">
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

    <!-- Affichage des messages flash -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show position-fixed top-0 end-0 m-3" style="z-index: 9999;" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>

@script
<script>
    // Initialiser les tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })

    // Auto-dismissal des alerts après 5 secondes
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert-dismissible');
        alerts.forEach(alert => {
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        });
    }, 5000);
</script>
@endscript