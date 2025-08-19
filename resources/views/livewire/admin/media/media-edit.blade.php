<div>
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Modifier le média</h1>
            <a href="{{ route('media.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>

        @if(session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations du média</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="updateMediaDetails">
                            <div class="mb-3">
                                <label for="media-title" class="form-label">Titre</label>
                                <input wire:model="title" type="text" id="media-title" class="form-control">
                                @error('title') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="media-alt" class="form-label">Texte alternatif</label>
                                <input wire:model="altText" type="text" id="media-alt" class="form-control">
                                <div class="form-text">Utilisé pour l'accessibilité et le SEO (pour les images)</div>
                                @error('altText') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="media-description" class="form-label">Description</label>
                                <textarea wire:model="description" id="media-description" class="form-control" rows="4"></textarea>
                                @error('description') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Aperçu</h5>
                    </div>
                    <div class="card-body text-center">
                        @if($media->type == 'images')
                            <img src="{{ asset($media->path) }}" class="img-fluid mb-3" alt="{{ $media->alt_text }}" style="max-height: 300px;">
                        @elseif($media->type == 'documents')
                            <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                        @elseif($media->type == 'videos')
                            <i class="fas fa-file-video fa-5x text-primary mb-3"></i>
                        @else
                            <i class="fas fa-file fa-5x text-secondary mb-3"></i>
                        @endif
                        
                        <div class="d-grid gap-2">
                            <a href="{{ asset($media->path) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt me-1"></i> Ouvrir dans un nouvel onglet
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Détails techniques</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Nom du fichier</span>
                                <span class="text-muted">{{ $media->filename }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Type</span>
                                <span class="text-muted">{{ ucfirst($media->type) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Taille</span>
                                <span class="text-muted">{{ round($media->size / 1024) }} KB</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>MIME type</span>
                                <span class="text-muted">{{ $media->mime_type }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>Date d'ajout</span>
                                <span class="text-muted">{{ $media->created_at->format('d/m/Y H:i') }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>