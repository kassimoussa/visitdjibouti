<div>
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title text-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal" 
                                {{ $isDeleting ? 'disabled' : '' }}></button>
                    </div>
                    
                    <div class="modal-body text-center py-4">
                        <div class="mb-3">
                            <i class="fas fa-trash fa-3x text-danger mb-3"></i>
                        </div>
                        
                        <h6 class="mb-3">Êtes-vous sûr de vouloir supprimer cet article ?</h6>
                        
                        <div class="alert alert-light border">
                            <strong>{{ $newsTitle }}</strong>
                        </div>
                        
                        <p class="text-muted mb-0">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            Cette action est irréversible. L'article sera définitivement supprimé.
                        </p>
                    </div>
                    
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" 
                                class="btn btn-secondary" 
                                wire:click="closeModal"
                                {{ $isDeleting ? 'disabled' : '' }}>
                            <i class="fas fa-times me-2"></i>
                            Annuler
                        </button>
                        
                        <button type="button" 
                                class="btn btn-danger" 
                                wire:click="deleteNews"
                                {{ $isDeleting ? 'disabled' : '' }}
                                wire:loading.attr="disabled"
                                wire:target="deleteNews">
                            
                            <span wire:loading.remove wire:target="deleteNews">
                                <i class="fas fa-trash me-2"></i>
                                Supprimer définitivement
                            </span>
                            
                            <span wire:loading wire:target="deleteNews">
                                <i class="fas fa-spinner fa-spin me-2"></i>
                                Suppression...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>