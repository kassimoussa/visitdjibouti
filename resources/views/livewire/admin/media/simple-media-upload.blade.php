<!-- resources/views/livewire/admin/simple-media-upload.blade.php -->
<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Upload de fichier simplifié</h5>
        </div>
        <div class="card-body">
            @if($successMessage)
                <div class="alert alert-success">{{ $successMessage }}</div>
            @endif
            
            @if($errorMessage)
                <div class="alert alert-danger">{{ $errorMessage }}</div>
            @endif
            
            <form wire:submit.prevent="uploadFile">
                <div class="mb-3">
                    <label for="file" class="form-label">Choisir un fichier</label>
                    <input wire:model="file" type="file" class="form-control" id="file">
                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                
                @if($file)
                    <div class="mb-3">
                        <h6>Fichier sélectionné</h6>
                        <p>{{ $file->getClientOriginalName() }} ({{ round($file->getSize() / 1024) }} KB)</p>
                    </div>
                @endif
                
                <div class="text-end">
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading wire:target="uploadFile" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                        Téléverser
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>