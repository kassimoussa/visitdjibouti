<div>
    <!-- Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-user-plus me-2"></i>
                        Ajouter un utilisateur
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"></button>
                </div>

                <form wire:submit="save">
                    <div class="modal-body">
                        <div class="row">
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
                            <div class="col-md-12 mb-3">
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
                                    <div class="col-md-6 mb-2">
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
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" wire:click="closeModal">
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Créer l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:initialized', function () {
        Livewire.on('user-created', function () {
            // Fermer le modal après création
            var modal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
            if (modal) {
                modal.hide();
            }

            // Petit délai pour s'assurer que le modal se ferme avant de rafraîchir
            setTimeout(function() {
                // Dispatch l'événement pour rafraîchir tous les composants de liste d'utilisateurs
                Livewire.dispatch('refreshUserList');
            }, 100);
        });

        // S'assurer que le modal se ferme proprement
        document.getElementById('addUserModal').addEventListener('hidden.bs.modal', function () {
            // Reset du formulaire quand le modal est fermé
            Livewire.dispatch('resetForm');
        });
    });
</script>