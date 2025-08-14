<div class="card border-primary">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="fas fa-newspaper me-2"></i>
            Exemple d'intégration - Créer un article
        </h6>
    </div>
    <div class="card-body">
        @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form wire:submit.prevent="saveArticle">
            <!-- Titre de l'article -->
            <div class="mb-3">
                <label for="articleTitle" class="form-label">Titre de l'article</label>
                <input type="text" 
                       class="form-control @error('articleTitle') is-invalid @enderror" 
                       id="articleTitle" 
                       wire:model="articleTitle"
                       placeholder="Saisir le titre de l'article">
                @error('articleTitle')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Image à la une -->
            <div class="mb-4">
                <label class="form-label">Image à la une</label>
                @if($featuredImageId && $this->featuredImage)
                <div class="featured-image-container">
                    <div class="position-relative d-inline-block">
                        <img src="{{ asset($this->featuredImage->thumbnail_path ?? $this->featuredImage->path) }}" 
                             alt="{{ $this->featuredImage->title ?? $this->featuredImage->original_name }}"
                             class="img-thumbnail"
                             style="max-height: 150px;">
                        <button type="button" 
                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                wire:click="removeFeaturedImage"
                                title="Supprimer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="mt-2">
                        <small class="text-muted">{{ $this->featuredImage->title ?? $this->featuredImage->original_name }}</small>
                        <br>
                        <button type="button" class="btn btn-outline-primary btn-sm" wire:click="selectFeaturedImage">
                            <i class="fas fa-edit me-1"></i>Changer
                        </button>
                    </div>
                </div>
                @else
                <div class="featured-image-placeholder">
                    <div class="text-center p-4 border-2 border-dashed border-secondary rounded">
                        <i class="fas fa-image fa-2x text-muted mb-3"></i>
                        <p class="text-muted mb-3">Aucune image à la une sélectionnée</p>
                        <button type="button" class="btn btn-primary" wire:click="selectFeaturedImage">
                            <i class="fas fa-plus me-2"></i>Sélectionner une image à la une
                        </button>
                    </div>
                </div>
                @endif
            </div>

            <!-- Contenu -->
            <div class="mb-4">
                <label for="articleContent" class="form-label">Contenu de l'article</label>
                <textarea class="form-control @error('articleContent') is-invalid @enderror" 
                          id="articleContent" 
                          wire:model="articleContent"
                          rows="5"
                          placeholder="Saisir le contenu de l'article..."></textarea>
                @error('articleContent')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Galerie d'images -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <label class="form-label mb-0">Galerie d'images</label>
                    <div>
                        <button type="button" class="btn btn-success btn-sm" wire:click="selectGalleryImages">
                            <i class="fas fa-images me-1"></i>
                            @if(count($selectedMediaData) > 0)
                                Modifier ({{ count($selectedMediaData) }})
                            @else
                                Ajouter des images
                            @endif
                        </button>
                        @if(count($selectedMediaData) > 0)
                        <button type="button" class="btn btn-outline-danger btn-sm ms-1" wire:click="clearGallery">
                            <i class="fas fa-trash me-1"></i>Vider
                        </button>
                        @endif
                    </div>
                </div>

                @if(count($selectedMediaData) > 0)
                <div class="gallery-grid">
                    @foreach($selectedMediaData as $index => $media)
                    <div class="gallery-item" wire:key="gallery-{{ $media['id'] }}">
                        <div class="position-relative">
                            @if($media['type'] === 'image')
                            <img src="{{ asset($media['thumbnail_url'] ?? $media['url']) }}" 
                                 alt="{{ $media['title'] ?? $media['original_name'] }}"
                                 class="gallery-thumbnail">
                            @elseif($media['type'] === 'video')
                            <div class="gallery-thumbnail video-placeholder">
                                <i class="fas fa-play-circle fa-2x"></i>
                                <span class="video-label">VIDÉO</span>
                            </div>
                            @endif
                            
                            <div class="gallery-overlay">
                                <button type="button" 
                                        class="btn btn-sm btn-danger"
                                        wire:click="removeFromGallery({{ $media['id'] }})"
                                        title="Supprimer">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            
                            <div class="gallery-order">{{ $index + 1 }}</div>
                        </div>
                        
                        <div class="gallery-info">
                            <small class="text-truncate d-block">{{ $media['title'] ?? $media['original_name'] }}</small>
                            <span class="badge badge-sm bg-{{ $media['type'] === 'image' ? 'primary' : 'success' }}">
                                {{ strtoupper($media['type']) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center p-4 border border-dashed rounded">
                    <i class="fas fa-images fa-2x text-muted mb-3"></i>
                    <p class="text-muted mb-3">Aucune image dans la galerie</p>
                    <button type="button" class="btn btn-outline-success" wire:click="selectGalleryImages">
                        <i class="fas fa-plus me-2"></i>Ajouter des images à la galerie
                    </button>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary" wire:click="$refresh">
                    <i class="fas fa-undo me-2"></i>Réinitialiser
                </button>
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-save me-2"></i>Sauvegarder l'article
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde...
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.featured-image-container {
    display: inline-block;
}

.gallery-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 16px;
    padding: 16px;
    background: #f8f9fa;
    border-radius: 8px;
}

.gallery-item {
    text-align: center;
}

.gallery-thumbnail {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 6px;
    border: 2px solid #e9ecef;
    transition: all 0.2s ease;
}

.video-placeholder {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #6c757d;
    color: white;
    font-size: 12px;
}

.video-label {
    font-size: 10px;
    font-weight: 600;
    margin-top: 4px;
}

.gallery-item:hover .gallery-thumbnail {
    border-color: #0d6efd;
    transform: scale(1.02);
}

.gallery-overlay {
    position: absolute;
    top: 4px;
    right: 4px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.gallery-item:hover .gallery-overlay {
    opacity: 1;
}

.gallery-order {
    position: absolute;
    top: 4px;
    left: 4px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    font-weight: 600;
}

.gallery-info {
    margin-top: 8px;
    text-align: center;
}

.badge-sm {
    font-size: 9px;
    padding: 2px 6px;
}

.gallery-item {
    position: relative;
}
</style>