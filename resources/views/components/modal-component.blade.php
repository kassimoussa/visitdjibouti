<div>
    <!-- Modal Réutilisable -->
    <div 
        class="modal fade" 
        id="appModal" 
        tabindex="-1" 
        aria-labelledby="appModalLabel" 
        wire:ignore.self 
        data-bs-backdrop="static"
    >
        <div class="modal-dialog {{ $modalSize ?? '' }}" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="appModalLabel">{{ $modalTitle ?? 'Modal' }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Le contenu sera injecté ici -->
                    {{ $slot }}
                </div>
                @if(isset($modalFooter))
                    <div class="modal-footer">
                        {{ $modalFooter }}
                    </div>
                @else
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                        @if(isset($modalAction))
                            <button type="button" class="btn btn-primary" id="modalActionButton">
                                {{ $modalActionText ?? 'Enregistrer' }}
                            </button>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Script pour gérer le modal -->
<script>
    document.addEventListener('livewire:initialized', () => {
        // Initialisation du modal
        const appModal = document.getElementById('appModal');
        let modalInstance = null;
        
        if (appModal) {
            modalInstance = new bootstrap.Modal(appModal);
            
            // Gestion de la fermeture du modal
            appModal.addEventListener('hidden.bs.modal', () => {
                window.Livewire.dispatch('modalClosed');
            });
            
            // Bouton d'action principal
            const actionButton = document.getElementById('modalActionButton');
            if (actionButton) {
                actionButton.addEventListener('click', () => {
                    window.Livewire.dispatch('modalAction');
                });
            }
        }
        
        // Écouteurs d'événements
        window.addEventListener('openModal', event => {
            const { size, title, content } = event.detail || {};
            
            // Mettre à jour la taille si spécifiée
            if (size) {
                const dialogElement = appModal.querySelector('.modal-dialog');
                // Réinitialiser les classes de taille
                dialogElement.classList.remove('modal-sm', 'modal-lg', 'modal-xl', 'modal-fullscreen');
                // Ajouter la nouvelle classe de taille
                dialogElement.classList.add(size);
            }
            
            // Mettre à jour le titre si spécifié
            if (title) {
                const titleElement = appModal.querySelector('.modal-title');
                titleElement.textContent = title;
            }
            
            // Ouvrir le modal
            if (modalInstance) {
                modalInstance.show();
            }
        });
        
        window.addEventListener('closeModal', () => {
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    });
</script>