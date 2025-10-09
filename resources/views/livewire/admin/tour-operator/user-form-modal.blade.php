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

                            <!-- Username -->
                            <div class="col-md-6 mb-3">
                                <label for="username" class="form-label">Nom d'utilisateur <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control @error('username') is-invalid @enderror"
                                       wire:model="username"
                                       placeholder="Ex: ali.oudoum">
                                @error('username')
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

                            <!-- Mot de passe -->
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       wire:model="password"
                                       placeholder="Minimum 8 caractères">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Confirmation mot de passe -->
                            <div class="col-md-6 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                <input type="password"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       wire:model="password_confirmation"
                                       placeholder="Confirmer le mot de passe">
                                @error('password_confirmation')
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

                        <!-- Options -->
                        <div class="mb-3">
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