<div>
    <!-- Indicateur de chargement -->
    <div wire:loading class="text-center py-3">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Chargement...</span>
        </div>
        <div class="text-muted mt-2">Mise à jour en cours...</div>
    </div>

    <!-- Contenu principal -->
    <div wire:loading.remove>
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

    @if($users->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Contact</th>
                        <th>Permissions</th>
                        <th>Statut</th>
                        <th>Dernière connexion</th>
                        <th width="150">Actions</th>
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
    @else
        <div class="text-center py-4">
            <i class="fas fa-users fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun utilisateur</h5>
            <p class="text-muted mb-4">Ce tour operator n'a pas encore d'utilisateurs.</p>
            <button type="button"
                    class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addUserModal">
                <i class="fas fa-plus me-2"></i>
                Ajouter le premier utilisateur
            </button>
        </div>
    @endif
    </div> <!-- Fin wire:loading.remove -->
</div>