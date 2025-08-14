<div>
    <div class="container-fluid">


        <!-- Filtres et actions -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Gestion des utilisateurs administrateurs</h5>
                <button wire:click="openCreateModal" class="btn btn-primary">
                    <i class="fas fa-plus-circle me-1"></i> Nouvel utilisateur
                </button>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                                placeholder="Rechercher...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <select wire:model.live="selectedRole" class="form-select">
                            <option value="">Tous les rôles</option>
                            @foreach ($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select wire:model.live="statusFilter" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="1">Actif</option>
                            <option value="0">Inactif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des utilisateurs -->
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th wire:click="sortBy('name')" style="cursor: pointer;">
                                    Nom
                                    @if ($sortField === 'name')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('email')" style="cursor: pointer;">
                                    Email
                                    @if ($sortField === 'email')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th>Téléphone</th>
                                <th wire:click="sortBy('role')" style="cursor: pointer;">
                                    Rôle
                                    @if ($sortField === 'role')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th>Statut</th>
                                <th wire:click="sortBy('last_login_at')" style="cursor: pointer;">
                                    Dernière connexion
                                    @if ($sortField === 'last_login_at')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th wire:click="sortBy('created_at')" style="cursor: pointer;">
                                    Date création
                                    @if ($sortField === 'created_at')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3">
                                                @if ($user->avatar)
                                                    <img src="{{ asset($user->avatar) }}" class="rounded-circle"
                                                        width="40" height="40">
                                                @else
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white"
                                                        style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $user->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number ?? '-' }}</td>
                                    <td>
                                        @if ($user->role)
                                            <span class="badge"
                                                style="background-color: {{ $user->role->name === 'Administrateur' ? '#dc3545' : ($user->role->name === 'Gestionnaire' ? '#0d6efd' : '#6f42c1') }}">
                                                {{ $user->role->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Non défini</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input wire:click="toggleUserStatus({{ $user->id }})"
                                                class="form-check-input" type="checkbox"
                                                {{ $user->is_active ? 'checked' : '' }} style="cursor: pointer;">
                                            <label class="form-check-label">
                                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                            </label>
                                        </div>
                                    </td>
                                    <td>
                                        @if ($user->last_login_at)
                                            <span title="{{ $user->last_login_at->format('d/m/Y H:i:s') }}">
                                                {{ $user->last_login_at->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-muted">Jamais connecté</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span title="{{ $user->created_at->format('d/m/Y H:i:s') }}">
                                            {{ $user->created_at->format('d/m/Y') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button wire:click="openEditModal({{ $user->id }})"
                                                class="btn btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button wire:click="openDeleteModal({{ $user->id }})"
                                                class="btn btn-outline-danger" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">Aucun utilisateur trouvé</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4 mb-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Créer Utilisateur -->
    <div class="modal fade" wire:ignore.self tabindex="-1" id="createUserModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Créer un nouvel utilisateur</h5>
                    <button type="button" class="btn-close" wire:click="closeCreateModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="create">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom complet <span
                                        class="text-danger">*</span></label>
                                <input wire:model="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror" id="name">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email <span
                                        class="text-danger">*</span></label>
                                <input wire:model="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" id="email">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Numéro de téléphone</label>
                                <input wire:model="phone_number" type="text"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    id="phone_number">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="role_id" class="form-label">Rôle <span
                                        class="text-danger">*</span></label>
                                <select wire:model="role_id"
                                    class="form-select @error('role_id') is-invalid @enderror" id="role_id">
                                    <option value="">Sélectionner un rôle</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mot de passe <span
                                        class="text-danger">*</span></label>
                                <input wire:model="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span
                                        class="text-danger">*</span></label>
                                <input wire:model="password_confirmation" type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input wire:model="is_active" type="checkbox" class="form-check-input"
                                        id="is_active_create" value="1">
                                    <label class="form-check-label" for="is_active_create">
                                        <strong>Utilisateur actif</strong>
                                        <small class="text-muted d-block">L'utilisateur pourra se connecter et accéder au système</small>
                                    </label>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeCreateModal">Annuler</button>
                    <button type="button" class="btn btn-primary" wire:click="create">Créer l'utilisateur</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Modifier Utilisateur -->
    <div class="modal fade" wire:ignore.self tabindex="-1" id="editUserModal" data-bs-backdrop="static">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modifier l'utilisateur</h5>
                    <button type="button" class="btn-close" wire:click="closeEditModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit="update">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name_edit" class="form-label">Nom complet <span
                                        class="text-danger">*</span></label>
                                <input wire:model="name" type="text"
                                    class="form-control @error('name') is-invalid @enderror" id="name_edit">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="email_edit" class="form-label">Email <span
                                        class="text-danger">*</span></label>
                                <input wire:model="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" id="email_edit">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone_number_edit" class="form-label">Numéro de téléphone</label>
                                <input wire:model="phone_number" type="text"
                                    class="form-control @error('phone_number') is-invalid @enderror"
                                    id="phone_number_edit">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="role_id_edit" class="form-label">Rôle <span
                                        class="text-danger">*</span></label>
                                <select wire:model="role_id" class="form-select @error('role_id') is-invalid @enderror"
                                    id="role_id_edit">
                                    <option value="">Sélectionner un rôle</option>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('role_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password_edit" class="form-label">Nouveau mot de passe</label>
                                <input wire:model="password" type="password"
                                    class="form-control @error('password') is-invalid @enderror" id="password_edit"
                                    placeholder="Laisser vide pour ne pas modifier">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation_edit" class="form-label">Confirmer le mot de passe</label>
                                <input wire:model="password_confirmation" type="password"
                                    class="form-control @error('password_confirmation') is-invalid @enderror"
                                    id="password_confirmation_edit"
                                    placeholder="Confirmer le nouveau mot de passe">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="form-check">
                                    <input wire:model="is_active" type="checkbox" class="form-check-input"
                                        id="is_active_edit" value="1">
                                    <label class="form-check-label" for="is_active_edit">
                                        <strong>Utilisateur actif</strong>
                                        <small class="text-muted d-block">Décochez pour désactiver l'accès de cet utilisateur</small>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeEditModal">Annuler</button>
                    <button type="button" class="btn btn-primary" wire:click="update">Mettre à jour</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Supprimer Utilisateur -->
    <div class="modal fade" wire:ignore.self tabindex="-1" id="deleteUserModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirmation de suppression</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeDeleteModal"></button>
                </div>
                <div class="modal-body text-center p-4">
                    <i class="fas fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                    <h5>Êtes-vous sûr de vouloir supprimer cet utilisateur ?</h5>
                    <p class="text-muted">Cette action est irréversible et supprimera toutes les données associées à
                        cet utilisateur.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeDeleteModal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="delete">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast pour les notifications -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 5">
        <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header" id="toastHeader">
                <strong class="me-auto" id="toastTitle">Notification</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    @script
        <script>
            // On utilise une fonction anonyme immédiatement invoquée pour éviter les problèmes de portée
            (function() {
                // Ces variables sont déclarées dans le scope de la fonction

                var createModalElement = document.getElementById('createUserModal');
                var createModalInstance = null;

                var editModalElement = document.getElementById('editUserModal');
                var editModalInstance = null;

                var deleteModalElement = document.getElementById('deleteUserModal');
                var deleteModalInstance = null;

                // Initialisation après chargement de Livewire
                document.addEventListener('livewire:initialized', function() {
                    // Initialisation des modals Bootstrap
                    if (createModalElement) {
                        createModalInstance = new bootstrap.Modal(createModalElement);
                    }
                    if (editModalElement) {
                        editModalInstance = new bootstrap.Modal(editModalElement);
                    }

                    if (deleteModalElement) {
                        deleteModalInstance = new bootstrap.Modal(deleteModalElement);
                    }

                    // Écoute des événements Livewire
                    $wire.on('show-create-modal', function() {
                        if (createModalInstance) {
                            createModalInstance.show();
                        }
                    });

                    $wire.on('hide-create-modal', function() {
                        if (createModalInstance) {
                            createModalInstance.hide();
                        }
                    });

                    $wire.on('show-edit-modal', function() {
                        if (editModalInstance) {
                            editModalInstance.show();
                        }
                    });

                    $wire.on('hide-edit-modal', function() {
                        if (editModalInstance) {
                            editModalInstance.hide();
                        }
                    });

                    $wire.on('show-delete-modal', function() {
                        if (deleteModalInstance) {
                            deleteModalInstance.show();
                        }
                    });

                    $wire.on('hide-delete-modal', function() {
                        if (deleteModalInstance) {
                            deleteModalInstance.hide();
                        }
                    });

                    // Écouter l'événement pour les notifications toast
                    $wire.on('show-toast', function(data) {
                        const toast = document.getElementById('liveToast');
                        const toastHeader = document.getElementById('toastHeader');
                        const toastMessage = document.getElementById('toastMessage');

                        // Définir le message
                        toastMessage.textContent = data.message;

                        // Définir le type de notification
                        toastHeader.className = 'toast-header';
                        if (data.type === 'success') {
                            toastHeader.classList.add('bg-success', 'text-white');
                        } else if (data.type === 'error') {
                            toastHeader.classList.add('bg-danger', 'text-white');
                        } else if (data.type === 'warning') {
                            toastHeader.classList.add('bg-warning', 'text-dark');
                        } else {
                            toastHeader.classList.add('bg-info', 'text-white');
                        }

                        // Afficher la notification
                        const bsToast = new bootstrap.Toast(toast);
                        bsToast.show();
                    });
                });
            })();
        </script>
    @endscript
</div>
