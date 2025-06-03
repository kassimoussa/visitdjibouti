<div class="modal fade" id="{{ $delmodal }}" tabindex="-1" wire:ignore.self aria-labelledby="deleteFicheModal"
    aria-hidden="true">
    <div class="modal-dialog   modal-dialog-centered  " role="document">
        <div class="modal-content">
            <div class="modal-header d-flex justify-content-center">
                <h4>Confirmer Suppression </h4>
            </div>
            <div class="modal-body ">
                <h5 class="text-center"><i class="fas fa-exclamation-circle fa-3x warning"></i>
                </h5>
                <h5 class="text-center">{{ $message }}?</h5>
            </div>
            <div class="modal-footer d-flex justify-content-center">
                <button class="btn btn-primary fw-bold" wire:click="{{ $delf }}" data-bs-dismiss="modal">Supprimer</button>
                <button type="reset" class="btn btn-outline-danger  fw-bold" data-bs-dismiss="modal">Annuler</button>
            </div>
        </div>
    </div>
</div>
