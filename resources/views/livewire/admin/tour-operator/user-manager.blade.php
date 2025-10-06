<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Utilisateurs Tour Operators</h4>
            <p class="text-muted mb-0">Gérez les comptes d'accès des tour operators</p>
        </div>
        <button wire:click="showCreateForm" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Nouvel Utilisateur
        </button>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text"
                               class="form-control"
                               wire:model.live="search"
                               placeholder="Nom, email, poste...">
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tour Operator</label>
                    <select class="form-select" wire:model.live="selectedTourOperator">
                        <option value="">Tous les tour operators</option>
                        @foreach($tourOperators as $operator)
                            <option value="{{ $operator->id }}">
                                {{ $operator->getTranslatedName('fr') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire de création/édition -->
    @if($showCreateForm)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-{{ $editingUserId ? 'edit' : 'plus' }} me-2"></i>
                    {{ $editingUserId ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur' }}
                </h5>
            </div>
            <div class="card-body">
                <form wire:submit="{{ $editingUserId ? 'update' : 'save' }}">
                    <div class="row">
                        <!-- Tour Operator -->
                        <div class="col-md-6 mb-3">
                            <label for="tour_operator_id" class="form-label">Tour Operator *</label>
                            <select class="form-select @error('tour_operator_id') is-invalid @enderror"
                                    wire:model="tour_operator_id">
                                <option value="">Sélectionner un tour operator</option>
                                @foreach($tourOperators as $operator)
                                    <option value="{{ $operator->id }}">
                                        {{ $operator->getTranslatedName('fr') }}
                                    </option>
                                @endforeach
                            </select>
                            @error('tour_operator_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Nom -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nom complet *</label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   wire:model="name"
                                   placeholder="Nom et prénom">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   wire:model="email"
                                   placeholder="email@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Téléphone -->
                        <div class="col-md-6 mb-3">
                            <label for="phone_number" class="form-label">Téléphone</label>
                            <input type="tel"
                                   class="form-control @error('phone_number') is-invalid @enderror"
                                   wire:model="phone_number"
                                   placeholder="+253 XX XX XX XX">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Poste -->
                        <div class="col-md-6 mb-3">
                            <label for="position" class="form-label">Poste/Fonction</label>
                            <input type="text"
                                   class="form-control @error('position') is-invalid @enderror"
                                   wire:model="position"
                                   placeholder="Ex: Directeur, Guide, Manager">
                            @error('position')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Langue -->
                        <div class="col-md-6 mb-3">
                            <label for="language_preference" class="form-label">Langue préférée</label>
                            <select class="form-select @error('language_preference') is-invalid @enderror"
                                    wire:model="language_preference">
                                <option value="fr">Français</option>
                                <option value="en">English</option>
                                <option value="ar">العربية</option>
                            </select>
                            @error('language_preference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="mb-3">
                        <label class="form-label">Permissions</label>
                        <div class="row">
                            @php
                                $permissionsList = [
                                    'manage_events' => ['label' => 'Gérer les événements', 'icon' => 'calendar-alt'],
                                    'manage_tours' => ['label' => 'Gérer les tours guidés', 'icon' => 'route'],
                                    'view_reservations' => ['label' => 'Voir les réservations', 'icon' => 'eye'],
                                    'manage_reservations' => ['label' => 'Gérer les réservations', 'icon' => 'ticket-alt'],
                                    'view_reports' => ['label' => 'Voir les rapports', 'icon' => 'chart-bar'],
                                    'manage_profile' => ['label' => 'Gérer le profil', 'icon' => 'user-cog'],
                                ];
                            @endphp

                            @foreach($permissionsList as $key => $permission)
                                <div class="col-md-4 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input"
                                               type="checkbox"
                                               wire:model="permissions.{{ $key }}"
                                               id="permission_{{ $key }}">
                                        <label class="form-check-label" for="permission_{{ $key }}">
                                            <i class="fas fa-{{ $permission['icon'] }} me-1"></i>
                                            {{ $permission['label'] }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       wire:model="is_active"
                                       id="is_active">
                                <label class="form-check-label" for="is_active">
                                    <i class="fas fa-check-circle me-1"></i>
                                    Compte actif
                                </label>
                            </div>
                        </div>
                        @if(!$editingUserId)
                            <div class="col-md-6">
                                <div class="form-check">
                                    <input class="form-check-input"
                                           type="checkbox"
                                           wire:model="send_invitation"
                                           id="send_invitation">
                                    <label class="form-check-label" for="send_invitation">
                                        <i class="fas fa-envelope me-1"></i>
                                        Envoyer l'invitation par email
                                    </label>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            {{ $editingUserId ? 'Mettre à jour' : 'Créer l\'utilisateur' }}
                        </button>
                        <button type="button" wire:click="hideCreateForm" class="btn btn-secondary">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Messages -->
    @if(session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Liste des utilisateurs -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-users me-2"></i>
                Liste des Utilisateurs
                <span class="badge bg-secondary ms-2">{{ $users->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Utilisateur</th>
                                <th>Tour Operator</th>
                                <th>Contact</th>
                                <th>Permissions</th>
                                <th>Statut</th>
                                <th>Dernière connexion</th>
                                <th width="200">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($user->avatar)
                                                <img src="{{ Storage::url($user->avatar) }}"
                                                     alt="{{ $user->name }}"
                                                     class="rounded-circle me-3"
                                                     style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                     style="width: 40px; height: 40px;">
                                                    <i class="fas fa-user text-white"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <strong>{{ $user->name }}</strong>
                                                @if($user->position)
                                                    <br><small class="text-muted">{{ $user->position }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $user->tourOperator->getTranslatedName('fr') }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-building me-1"></i>
                                                Tour Operator
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <div>{{ $user->email }}</div>
                                            @if($user->phone_number)
                                                <small class="text-muted">{{ $user->phone_number }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @if($user->permissions)
                                                @foreach($user->permissions as $permission => $enabled)
                                                    @if($enabled)
                                                        <span class="badge bg-light text-dark small">
                                                            @switch($permission)
                                                                @case('manage_events')
                                                                    <i class="fas fa-calendar-alt"></i>
                                                                    @break
                                                                @case('manage_tours')
                                                                    <i class="fas fa-route"></i>
                                                                    @break
                                                                @case('view_reservations')
                                                                    <i class="fas fa-eye"></i>
                                                                    @break
                                                                @case('manage_reservations')
                                                                    <i class="fas fa-ticket-alt"></i>
                                                                    @break
                                                                @case('view_reports')
                                                                    <i class="fas fa-chart-bar"></i>
                                                                    @break
                                                                @case('manage_profile')
                                                                    <i class="fas fa-user-cog"></i>
                                                                    @break
                                                            @endswitch
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($user->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-danger">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($user->last_login_at)
                                            <small>{{ $user->last_login_at->diffForHumans() }}</small>
                                        @else
                                            <small class="text-muted">Jamais</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <button wire:click="edit({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-primary"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="toggleStatus({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                                                    title="{{ $user->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $user->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button wire:click="resetPassword({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-info"
                                                    title="Réinitialiser le mot de passe">
                                                <i class="fas fa-key"></i>
                                            </button>
                                            <button wire:click="delete({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-danger"
                                                    title="Supprimer"
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
                        Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} utilisateurs
                    </div>
                    {{ $users->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun utilisateur trouvé</h5>
                    <p class="text-muted mb-4">
                        @if($search || $selectedTourOperator)
                            Modifiez vos filtres pour voir plus d'utilisateurs
                        @else
                            Créez le premier compte utilisateur tour operator
                        @endif
                    </p>
                    @if(!$search && !$selectedTourOperator)
                        <button wire:click="showCreateForm" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer un utilisateur
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>