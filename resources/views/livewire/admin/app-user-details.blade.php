<div>
    <!-- Statistiques utilisateur -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-check fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $userStats['total_reservations'] }}</h4>
                    <small>Réservations totales</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $userStats['pending_reservations'] }}</h4>
                    <small>En attente</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-heart fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $userStats['total_favorites'] }}</h4>
                    <small>Favoris totaux</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day fa-2x mb-2"></i>
                    <h4 class="mb-0">{{ $userStats['account_age_days'] }}</h4>
                    <small>Jours d'ancienneté</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Onglets -->
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'profile' ? 'active' : '' }}" 
                            wire:click="changeTab('profile')" type="button">
                        <i class="fas fa-user me-1"></i>Profil
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'reservations' ? 'active' : '' }}" 
                            wire:click="changeTab('reservations')" type="button">
                        <i class="fas fa-calendar-check me-1"></i>Réservations
                        <span class="badge bg-primary ms-1">{{ $userStats['total_reservations'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'favorites' ? 'active' : '' }}" 
                            wire:click="changeTab('favorites')" type="button">
                        <i class="fas fa-heart me-1"></i>Favoris
                        <span class="badge bg-warning ms-1">{{ $userStats['total_favorites'] }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'activity' ? 'active' : '' }}" 
                            wire:click="changeTab('activity')" type="button">
                        <i class="fas fa-history me-1"></i>Activité
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body">
            @if($activeTab === 'profile')
                <!-- Onglet Profil -->
                <div class="row">
                    <div class="col-md-6">
                        <h5 class="mb-3">Informations personnelles</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-medium">Nom complet:</td>
                                <td>{{ $appUser->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Email:</td>
                                <td>{{ $appUser->email }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Téléphone:</td>
                                <td>{{ $appUser->phone ?? 'Non renseigné' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Date de naissance:</td>
                                <td>
                                    @if($appUser->date_of_birth)
                                        {{ $appUser->date_of_birth->format('d/m/Y') }}
                                        @if($appUser->age)
                                            ({{ $appUser->age }} ans)
                                        @endif
                                    @else
                                        Non renseignée
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Genre:</td>
                                <td>
                                    @switch($appUser->gender)
                                        @case('male')
                                            Homme
                                            @break
                                        @case('female')
                                            Femme
                                            @break
                                        @case('other')
                                            Autre
                                            @break
                                        @default
                                            Non renseigné
                                    @endswitch
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Ville:</td>
                                <td>{{ $appUser->city ?? 'Non renseignée' }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Pays:</td>
                                <td>{{ $appUser->country ?? 'Non renseigné' }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5 class="mb-3">Paramètres compte</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-medium">Provider:</td>
                                <td>
                                    <span class="badge {{ $this->getProviderBadgeClass($appUser->provider) }}">
                                        {{ $this->getProviderLabel($appUser->provider) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Langue préférée:</td>
                                <td>
                                    @if($appUser->preferred_language)
                                        <span class="badge bg-info">{{ strtoupper($appUser->preferred_language) }}</span>
                                    @else
                                        Non renseignée
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Statut:</td>
                                <td>
                                    <span class="badge {{ $appUser->is_active ? 'bg-success' : 'bg-danger' }}">
                                        {{ $appUser->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Notifications push:</td>
                                <td>
                                    <span class="badge {{ $appUser->push_notifications_enabled ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $appUser->push_notifications_enabled ? 'Activées' : 'Désactivées' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Notifications email:</td>
                                <td>
                                    <span class="badge {{ $appUser->email_notifications_enabled ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $appUser->email_notifications_enabled ? 'Activées' : 'Désactivées' }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Inscrit le:</td>
                                <td>{{ $appUser->created_at->format('d/m/Y à H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="fw-medium">Dernière connexion:</td>
                                <td>
                                    @if($appUser->last_login_at)
                                        {{ $appUser->last_login_at->format('d/m/Y à H:i') }}
                                        <br><small class="text-muted">{{ $appUser->last_login_at->diffForHumans() }}</small>
                                    @else
                                        Jamais connecté
                                    @endif
                                </td>
                            </tr>
                            @if($appUser->last_login_ip)
                            <tr>
                                <td class="fw-medium">Dernière IP:</td>
                                <td><code>{{ $appUser->last_login_ip }}</code></td>
                            </tr>
                            @endif
                        </table>
                    </div>
                </div>

            @elseif($activeTab === 'reservations')
                <!-- Onglet Réservations -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Réservations de l'utilisateur</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-warning">{{ $userStats['pending_reservations'] }} en attente</span>
                        <span class="badge bg-success">{{ $userStats['confirmed_reservations'] }} confirmées</span>
                        <span class="badge bg-info">{{ $userStats['completed_reservations'] }} terminées</span>
                    </div>
                </div>

                @if(isset($reservations) && $reservations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Nom</th>
                                    <th>Date réservation</th>
                                    <th>Personnes</th>
                                    <th>Statut</th>
                                    <th>Créé le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservations as $reservation)
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
                                        <br><small class="text-muted">{{ $reservation->confirmation_number }}</small>
                                    </td>
                                    <td>
                                        {{ $reservation->reservation_date->format('d/m/Y') }}
                                        @if($reservation->reservation_time)
                                            <br><small>{{ $reservation->reservation_time }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark">{{ $reservation->number_of_people }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $this->getStatusBadgeClass($reservation->status) }}">
                                            {{ $this->getStatusLabel($reservation->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $reservation->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        @if($reservation->status === 'pending')
                                            <button wire:click="openReservationModal({{ $reservation->id }}, 'confirm')" 
                                                    class="btn btn-success btn-sm">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                        
                                        @if(in_array($reservation->status, ['pending', 'confirmed']))
                                            <button wire:click="openReservationModal({{ $reservation->id }}, 'cancel')" 
                                                    class="btn btn-danger btn-sm">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($reservations->hasPages())
                        <div class="mt-3">
                            {{ $reservations->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <h6>Aucune réservation</h6>
                        <p class="text-muted">Cet utilisateur n'a pas encore effectué de réservation.</p>
                    </div>
                @endif

            @elseif($activeTab === 'favorites')
                <!-- Onglet Favoris -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Favoris de l'utilisateur</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-primary">{{ $userStats['favorite_pois'] }} POIs</span>
                        <span class="badge bg-success">{{ $userStats['favorite_events'] }} Events</span>
                    </div>
                </div>

                @if(isset($favorites) && $favorites->count() > 0)
                    <div class="row">
                        @foreach($favorites as $favorite)
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            @if(str_contains($favorite->favoritable_type, 'Poi'))
                                                <span class="badge bg-primary mb-2">POI</span>
                                                <h6 class="card-title">{{ $favorite->favoritable->translation('fr')->name ?? 'Sans nom' }}</h6>
                                            @else
                                                <span class="badge bg-success mb-2">Event</span>
                                                <h6 class="card-title">{{ $favorite->favoritable->translation('fr')->title ?? 'Sans nom' }}</h6>
                                            @endif
                                            <small class="text-muted">Ajouté le {{ $favorite->created_at->format('d/m/Y') }}</small>
                                        </div>
                                        <button wire:click="removeFavorite({{ $favorite->id }})" 
                                                class="btn btn-outline-danger btn-sm"
                                                onclick="return confirm('Supprimer ce favori ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($favorites->hasPages())
                        <div class="mt-3">
                            {{ $favorites->links() }}
                        </div>
                    @endif
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-heart fa-3x text-muted mb-3"></i>
                        <h6>Aucun favori</h6>
                        <p class="text-muted">Cet utilisateur n'a pas encore ajouté de favoris.</p>
                    </div>
                @endif

            @elseif($activeTab === 'activity')
                <!-- Onglet Activité -->
                <h5 class="mb-3">Activité récente</h5>

                @if(isset($activities) && $activities->count() > 0)
                    <div class="timeline">
                        @foreach($activities as $activity)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                @if($activity['type'] === 'reservation')
                                    <div class="bg-primary rounded-circle p-2">
                                        <i class="fas fa-calendar-check text-white"></i>
                                    </div>
                                @else
                                    <div class="bg-warning rounded-circle p-2">
                                        <i class="fas fa-heart text-white"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="ms-3 flex-grow-1">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong>{{ $activity['description'] }}</strong>
                                        <br><small class="text-muted">{{ $activity['timestamp']->format('d/m/Y à H:i') }}</small>
                                    </div>
                                    <small class="text-muted">{{ $activity['timestamp']->diffForHumans() }}</small>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-history fa-3x text-muted mb-3"></i>
                        <h6>Aucune activité</h6>
                        <p class="text-muted">Aucune activité récente pour cet utilisateur.</p>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Modal de confirmation pour les réservations -->
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
                                    <textarea wire:model="actionReason" id="actionReason" class="form-control" rows="3"></textarea>
                                </div>
                            @endif
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Annuler</button>
                        <button type="button" class="btn btn-primary" wire:click="confirmReservationAction">
                            Confirmer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Messages flash -->
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