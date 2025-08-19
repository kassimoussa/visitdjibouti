<div>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Téléverser des médias</h1>
            <a href="{{ route('media.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-4">
                @if($successMessage)
                    <div class="alert alert-success">{{ $successMessage }}</div>
                @endif
                
                @if($errorMessage)
                    <div class="alert alert-danger">{{ $errorMessage }}</div>
                @endif
                
                <form wire:submit="save"  enctype="multipart/form-data">
                    <div class="mb-4">
                        <label for="file" class="form-label">Choisir un fichier</label>
                        <input wire:model="file" type="file" id="file" class="form-control @error('file') is-invalid @enderror">
                        @error('file') 
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        
                        @if($file)
                            <div class="mt-3">
                                <h6>Fichier sélectionné :</h6>
                                <div class="d-flex align-items-center">
                                    @if(in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                                        <img src="{{ $file->temporaryUrl() }}" class="img-thumbnail me-3" style="max-width: 100px; max-height: 100px;">
                                    @else
                                        <div class="bg-light rounded p-3 me-3">
                                            @if(in_array($file->getClientOriginalExtension(), ['pdf', 'doc', 'docx']))
                                                <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                            @elseif(in_array($file->getClientOriginalExtension(), ['mp4', 'webm']))
                                                <i class="fas fa-file-video fa-2x text-primary"></i>
                                            @else
                                                <i class="fas fa-file fa-2x text-secondary"></i>
                                            @endif
                                        </div>
                                    @endif
                                    <div>
                                        <p class="mb-0 fw-medium">{{ $file->getClientOriginalName() }}</p>
                                        <p class="mb-0 text-muted small">{{ round($file->getSize() / 1024) }} KB</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                            <span wire:loading wire:target="file" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                            Téléverser
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('mediaUploaded', () => {
                // Attendre un peu pour que l'utilisateur voie le message de succès
                setTimeout(() => {
                    window.location.href = "{{ route('media.index') }}";
                }, 1500);
            });
        });
    </script>

</div>
