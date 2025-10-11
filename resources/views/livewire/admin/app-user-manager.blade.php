<div>
    <!-- Statistiques en en-tête -->
    <div class="bg-light p-4 border-bottom">
        <div class="row g-3">
            <div class="col-lg-2 col-md-4 col-6">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle p-2 me-3">
                        <i class="fas fa-users text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['total'] }}</h5>
                        <small class="text-muted">Total</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="d-flex align-items-center">
                    <div class="bg-success rounded-circle p-2 me-3">
                        <i class="fas fa-user-check text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['active'] }}</h5>
                        <small class="text-muted">Actifs</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="d-flex align-items-center">
                    <div class="bg-warning rounded-circle p-2 me-3">
                        <i class="fas fa-user-times text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['inactive'] }}</h5>
                        <small class="text-muted">Inactifs</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="d-flex align-items-center">
                    <div class="bg-info rounded-circle p-2 me-3">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['with_reservations'] }}</h5>
                        <small class="text-muted">Avec réservations</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="d-flex align-items-center">
                    <div class="bg-danger rounded-circle p-2 me-3">
                        <i class="fas fa-heart text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['with_favorites'] }}</h5>
                        <small class="text-muted">Avec favoris</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-md-4 col-6">
                <div class="d-flex align-items-center">
                    <div class="bg-secondary rounded-circle p-2 me-3">
                        <i class="fas fa-user-plus text-white"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">{{ $globalStats['recent_signups'] }}</h5>
                        <small class="text-muted">Récents (30j)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="p-4 border-bottom">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="searchFilter" class="form-label">Recherche</label>
                <input wire:model.live.debounce.300ms="searchFilter" type="text" id="searchFilter" 
                       class="form-control" placeholder="Nom, email, téléphone...">
            </div>

            <div class="col-md-2">
                <label for="statusFilter" class="form-label">Statut</label>
                <select wire:model.live="statusFilter" id="statusFilter" class="form-select">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actifs</option>
                    <option value="inactive">Inactifs</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="providerFilter" class="form-label">Provider</label>
                <select wire:model.live="providerFilter" id="providerFilter" class="form-select">
                    <option value="">Tous les providers</option>
                    <option value="email">Email ({{ $providers['email'] ?? 0 }})</option>
                    @if(isset($providers['google']))
                        <option value="google">Google ({{ $providers['google'] }})</option>
                    @endif
                    @if(isset($providers['facebook']))
                        <option value="facebook">Facebook ({{ $providers['facebook'] }})</option>
                    @endif
                </select>
            </div>

            <div class="col-md-2">
                <label for="languageFilter" class="form-label">Langue</label>
                <select wire:model.live="languageFilter" id="languageFilter" class="form-select">
                    <option value="">Toutes les langues</option>
                    <option value="fr">Français ({{ $languages['fr'] ?? 0 }})</option>
                    <option value="en">Anglais ({{ $languages['en'] ?? 0 }})</option>
                </select>
            </div>

            <div class="col-md-2">
                <label for="registrationDateFrom" class="form-label">Inscrit du</label>
                <input wire:model.live="registrationDateFrom" type="date" id="registrationDateFrom" class="form-control">
            </div>

            <div class="col-md-1">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-1">
                    @if($searchFilter || $statusFilter || $providerFilter || $languageFilter || $registrationDateFrom || $registrationDateTo)
                        <button wire:click="clearFilters" class="btn btn-outline-secondary btn-sm" title="Effacer filtres">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions en lot -->
        @if(count($selectedUsers) > 0)
        <div class="row mt-3">
            <div class="col-12">
                <div class="alert alert-info d-flex justify-content-between align-items-center">
                    <span>{{ count($selectedUsers) }} utilisateur(s) sélectionné(s)</span>
                    <div class="btn-group">
                        <button wire:click="bulkAction('activate')" class="btn btn-success btn-sm">
                            <i class="fas fa-check me-1"></i> Activer
                        </button>
                        <button wire:click="bulkAction('deactivate')" class="btn btn-warning btn-sm">
                            <i class="fas fa-ban me-1"></i> Désactiver
                        </button>
                        <button wire:click="bulkAction('delete')" class="btn btn-danger btn-sm"
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer les utilisateurs sélectionnés ?')">
                            <i class="fas fa-trash me-1"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Table des utilisateurs -->
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="bg-light">
                <tr>
                    <th width="30">
                        <input type="checkbox" wire:model.live="selectAll" class="form-check-input">
                    </th>
                    <th width="60" style="cursor: pointer;" wire:click="sortBy('id')">
                        ID
                        @if($sortField === 'id')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @endif
                    </th>
                    <th style="cursor: pointer;" wire:click="sortBy('name')">
                        Utilisateur
                        @if($sortField === 'name')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @endif
                    </th>
                    <th style="cursor: pointer;" wire:click="sortBy('email')">
                        Contact
                        @if($sortField === 'email')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @endif
                    </th>
                    <th>Provider</th>
                    <th>Langue</th>
                    <th width="80">Réserv.</th>
                    <th width="80">Favoris</th>
                    <th width="100">Statut</th>
                    <th style="cursor: pointer;" wire:click="sortBy('created_at')">
                        Inscrit le
                        @if($sortField === 'created_at')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @endif
                    </th>
                    <th style="cursor: pointer;" wire:click="sortBy('last_login_at')">
                        Dernière connexion
                        @if($sortField === 'last_login_at')
                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }} ms-1"></i>
                        @endif
                    </th>
                    <th width="120">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>
                            <input type="checkbox" wire:model.live="selectedUsers" value="{{ $user->id }}" class="form-check-input">
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $user->id }}</span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                         class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-medium">{{ $user->name }}</div>
                                    @if($user->age)
                                        <small class="text-muted">{{ $user->age }} ans</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <span class="fw-medium">{{ $user->email }}</span>
                                @if($user->phone)
                                    <small class="text-muted">{{ $user->phone }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge {{ $this->getProviderBadgeClass($user->provider) }}">
                                {{ $this->getProviderLabel($user->provider) }}
                            </span>
                        </td>
                        <td>
                            @if($user->preferred_language)
                                <span class="badge bg-info">{{ strtoupper($user->preferred_language) }}</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-primary">{{ $user->reservations_count }}</span>
                        </td>
                        <td>
                            <span class="badge bg-warning">{{ $user->favorites_count }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex flex-column">
                                <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                <small class="text-muted">{{ $user->created_at->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            @if($user->last_login_at)
                                <div class="d-flex flex-column">
                                    <small>{{ $user->last_login_at->format('d/m/Y') }}</small>
                                    <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                </div>
                            @else
                                <span class="text-muted">Jamais</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group-sm" role="group">
                                <a href="{{ route('app-users.show', $user->id) }}" 
                                   class="btn btn-outline-primary btn-sm" 
                                   data-bs-toggle="tooltip" title="Voir détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                
                                @if($user->is_active)
                                    <button wire:click="openActionModal({{ $user->id }}, 'deactivate')" 
                                            class="btn btn-outline-warning btn-sm" 
                                            data-bs-toggle="tooltip" title="Désactiver">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                @else
                                    <button wire:click="openActionModal({{ $user->id }}, 'activate')" 
                                            class="btn btn-outline-success btn-sm" 
                                            data-bs-toggle="tooltip" title="Activer">
                                        <i class="fas fa-check"></i>
                                    </button>
                                @endif
                                 
                                <button wire:click="openActionModal({{ $user->id }}, 'delete')" 
                                        class="btn btn-outline-danger btn-sm" 
                                        data-bs-toggle="tooltip" title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center py-5">
                            <div class="text-muted">
                                <i class="fas fa-users fa-3x mb-3"></i>
                                <h5>Aucun utilisateur trouvé</h5>
                                <p>Aucun utilisateur ne correspond aux filtres sélectionnés.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
    <div class="p-4 border-top">
        {{ $users->links() }}
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
                                @case('activate')
                                    Activer l'utilisateur
                                    @break
                                @case('deactivate')
                                    Désactiver l'utilisateur
                                    @break
                                @case('delete')
                                    Supprimer l'utilisateur
                                    @break
                                @case('reset_password')
                                    Réinitialiser le mot de passe
                                    @break
                            @endswitch
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        @if($selectedUser)
                            <div class="mb-3">
                                <strong>Utilisateur:</strong> {{ $selectedUser->name }}<br>
                                <strong>Email:</strong> {{ $selectedUser->email }}
                            </div>

                            @if($actionType === 'delete')
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Cette action supprimera définitivement l'utilisateur et toutes ses données associées.
                                </div>
                            @endif

                            <p class="text-muted">
                                @switch($actionType)
                                    @case('activate')
                                        Êtes-vous sûr de vouloir activer cet utilisateur ?
                                        @break
                                    @case('deactivate')
                                        Êtes-vous sûr de vouloir désactiver cet utilisateur ?
                                        @break
                                    @case('delete')
                                        Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.
                                        @break
                                    @case('reset_password')
                                        Un email de réinitialisation sera envoyé à l'utilisateur.
                                        @break
                                @endswitch
                            </p>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Annuler</button>
                        <button type="button" class="btn btn-primary" wire:click="confirmAction">
                            @switch($actionType)
                                @case('activate')
                                    Activer
                                    @break
                                @case('deactivate')
                                    Désactiver
                                    @break
                                @case('delete')
                                    Supprimer
                                    @break
                                @case('reset_password')
                                    Envoyer l'email
                                    @break
                            @endswitch
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