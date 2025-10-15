<div>
    <!-- Modal -->
    @if ($isOpen)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-fullscreen modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <!-- Header -->
                    <div class="modal-header bg-gradient-primary text-white border-0">
                        <div class="d-flex align-items-center">
                            <div class="feature-icon-container me-3">
                                <i class="fas fa-images"></i>
                            </div>
                            <div>
                                <h5 class="modal-title mb-0">
                                    Sélectionner {{ $selectionMode === 'single' ? 'une image' : 'des images' }}
                                </h5>
                                <small class="opacity-75">
                                    {{ count($selectedImages) }} image(s) sélectionnée(s)
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>

                    <!-- Body -->
                    <div class="modal-body p-0">
                        <!-- Barre d'outils -->
                        <div class="border-bottom p-3 bg-light">
                            <div class="row align-items-center">
                                <div class="col-md-4">
                                    <!-- Recherche -->
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" class="form-control" placeholder="Rechercher..."
                                            wire:model.live.debounce.300ms="search">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <!-- Filtre par type -->
                                    <select class="form-select" wire:model.live="typeFilter">
                                        <option value="images">Images ({{ $stats['images'] }})</option>
                                        <option value="documents">Documents ({{ $stats['documents'] }})</option>
                                        <option value="videos">Vidéos ({{ $stats['videos'] }})</option>
                                        <option value="all">Tous ({{ $stats['total'] }})</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <!-- Tri -->
                                    <select class="form-select" wire:change="sortBy($event.target.value)">
                                        <option value="created_at">Plus récent</option>
                                        <option value="original_name">Nom</option>
                                        <option value="size">Taille</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <!-- Actions -->
                                    <div class="btn-group w-100">
                                        @if ($selectionMode === 'multiple')
                                            <button type="button" class="btn btn-outline-primary btn-sm"
                                                wire:click="selectAll">
                                                <i class="fas fa-check-square me-1"></i>Tout
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                                wire:click="deselectAll">
                                                <i class="fas fa-square me-1"></i>Aucun
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Grille des médias -->
                        <div class="p-3 media-scroll-container" style="max-height: 500px; overflow-y: auto;" id="mediaScrollContainer">
                            @if ($media->count() > 0)
                                <div class="row g-3">
                                    @foreach ($media as $mediaItem)
                                        <div class="col-2">
                                            <div class="media-card position-relative"
                                                style="cursor: pointer; transition: all 0.2s ease;"
                                                wire:click="toggleImage({{ $mediaItem->id }})">

                                                <!-- Image -->
                                                <div class="media-image position-relative"
                                                    style="border: 3px solid {{ in_array($mediaItem->id, $selectedImages) ? '#0d6efd' : 'transparent' }}; border-radius: 8px; overflow: hidden;">
                                                    @if ($mediaItem->type === 'images')
                                                        <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                            alt="{{ $mediaItem->translations->first()?->alt_text ?? $mediaItem->original_name }}"
                                                            class="img-fluid"
                                                            style="height: 120px; width: 100%; object-fit: cover;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                                            style="height: 120px;">
                                                            <i class="fas fa-file-alt fa-2x text-muted"></i>
                                                        </div>
                                                    @endif

                                                    <!-- Overlay de sélection -->
                                                    @if (in_array($mediaItem->id, $selectedImages))
                                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
                                                            style="background-color: rgba(13, 110, 253, 0.7);">
                                                            <i class="fas fa-check fa-2x text-white"></i>
                                                        </div>
                                                    @endif

                                                    <!-- Actions -->
                                                    <div class="position-absolute top-0 end-0 p-2">
                                                        <button type="button" class="btn btn-sm btn-light btn-action"
                                                            wire:click.stop="showImagePreview({{ $mediaItem->id }})"
                                                            title="Aperçu">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Métadonnées -->
                                                <div class="mt-2">
                                                    <h6 class="mb-1 text-truncate" style="font-size: 0.8rem;">
                                                        {{ $mediaItem->translations->first()?->title ?? $mediaItem->original_name }}
                                                    </h6>
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small
                                                            class="text-muted">{{ $this->formatFileSize($mediaItem->size) }}</small>
                                                        <small
                                                            class="text-muted">{{ $mediaItem->created_at->format('d/m/y') }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Loader et indicateur de chargement -->
                                @if ($hasMore)
                                    <div class="text-center py-4" id="loadMoreTrigger">
                                        <div class="spinner-border text-primary" role="status" wire:loading wire:target="loadMore">
                                            <span class="visually-hidden">Chargement...</span>
                                        </div>
                                        <div wire:loading.remove wire:target="loadMore">
                                            <small class="text-muted">
                                                <i class="fas fa-arrow-down me-1"></i>Scroll pour charger plus
                                            </small>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-3">
                                        <small class="text-muted">
                                            <i class="fas fa-check-circle me-1"></i>Tous les médias chargés ({{ $media->count() }} / {{ $totalCount }})
                                        </small>
                                    </div>
                                @endif
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                    <h6 class="text-muted">Aucun média trouvé</h6>
                                    <p class="text-muted">Essayez de modifier vos critères de recherche ou uploadez de
                                        nouveaux fichiers.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer bg-light border-0">
                        <div class="me-auto">
                            <small class="text-muted">
                                {{ count($selectedImages) }} image(s) sélectionnée(s)
                            </small>
                        </div>
                        <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <button type="button" class="btn btn-primary" wire:click="confirmSelection"
                            @if (count($selectedImages) === 0) disabled @endif>
                            <i class="fas fa-check me-2"></i>Confirmer la sélection
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal d'aperçu -->
    @if ($showPreview && $previewImage)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.8); z-index: 1060;">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 bg-transparent">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title text-white">
                            {{ $previewImage->translations->first()?->title ?? $previewImage->original_name }}</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closePreview"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset($previewImage->path) }}" class="img-fluid rounded"
                            style="max-height: 70vh;">
                        <div class="mt-3 text-white">
                            <div class="row text-center">
                                <div class="col-3">
                                    <small class="text-muted">Taille</small>
                                    <div>{{ $this->formatFileSize($previewImage->size) }}</div>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted">Type</small>
                                    <div>{{ strtoupper($previewImage->mime_type) }}</div>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted">Créé</small>
                                    <div>{{ $previewImage->created_at->format('d/m/Y') }}</div>
                                </div>
                                <div class="col-3">
                                    <small class="text-muted">Actions</small>
                                    <div>
                                        <button class="btn btn-sm btn-primary"
                                            wire:click="toggleImage({{ $previewImage->id }})">
                                            @if (in_array($previewImage->id, $selectedImages))
                                                <i class="fas fa-check"></i> Sélectionné
                                            @else
                                                <i class="fas fa-plus"></i> Sélectionner
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Styles CSS -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .feature-icon-container {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .media-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .media-card:hover .media-image {
            border-color: #0d6efd !important;
        }

        .btn-action {
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .media-card:hover .btn-action {
            opacity: 1;
        }

        .modal {
            backdrop-filter: blur(3px);
        }

        .media-scroll-container {
            scroll-behavior: smooth;
        }

        .media-scroll-container::-webkit-scrollbar {
            width: 8px;
        }

        .media-scroll-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .media-scroll-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .media-scroll-container::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>

    @script
        <script>
            let observer = null;

            // Gestion des raccourcis clavier
            document.addEventListener('keydown', function(e) {
                if (@this.isOpen) {
                    if (e.key === 'Escape') {
                        @this.call('closeModal');
                    }
                    if (e.key === 'Enter' && @this.selectedImages.length > 0) {
                        @this.call('confirmSelection');
                    }
                }
            });

            // Initialiser l'Intersection Observer pour le lazy loading
            function initIntersectionObserver() {
                // Détruire l'observer précédent s'il existe
                if (observer) {
                    observer.disconnect();
                }

                const loadMoreTrigger = document.getElementById('loadMoreTrigger');
                if (!loadMoreTrigger) return;

                // Créer un nouvel observer
                observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            // L'élément trigger est visible, charger plus de médias
                            @this.call('loadMore');
                        }
                    });
                }, {
                    root: document.getElementById('mediaScrollContainer'),
                    rootMargin: '100px', // Commencer à charger 100px avant d'atteindre le trigger
                    threshold: 0.1
                });

                observer.observe(loadMoreTrigger);
            }

            // Fermer le modal en cliquant à l'extérieur et initialiser l'observer
            $wire.on('modal-opened', () => {
                document.querySelector('.modal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        @this.call('closeModal');
                    }
                });

                // Initialiser l'observer après un court délai pour s'assurer que le DOM est prêt
                setTimeout(() => {
                    initIntersectionObserver();
                }, 100);
            });

            // Réinitialiser l'observer après chaque mise à jour Livewire
            Livewire.hook('morph.updated', ({ component }) => {
                if (component.id === @this.id && @this.isOpen) {
                    setTimeout(() => {
                        initIntersectionObserver();
                    }, 100);
                }
            });

            // Nettoyer l'observer quand le modal se ferme
            $wire.on('modal-closed', () => {
                if (observer) {
                    observer.disconnect();
                    observer = null;
                }
            });
        </script>
    @endscript
</div>
