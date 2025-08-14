<div>
    <!-- Modal -->
    @if($isOpen)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered">
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
                                    <input type="text" class="form-control" placeholder="Rechercher..." wire:model.live.debounce.300ms="search">
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
                                    @if($selectionMode === 'multiple')
                                    <button type="button" class="btn btn-outline-primary btn-sm" wire:click="selectAll">
                                        <i class="fas fa-check-square me-1"></i>Tout
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="deselectAll">
                                        <i class="fas fa-square me-1"></i>Aucun
                                    </button>
                                    @endif
                                    <button type="button" class="btn btn-outline-success btn-sm" wire:click="toggleUploadArea">
                                        <i class="fas fa-upload me-1"></i>Upload
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Zone d'upload -->
                        @if($showUploadArea)
                        <div class="mt-3 p-3 border rounded bg-white">
                            <!-- Messages d'erreur/succès -->
                            @if (session()->has('success'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('success') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            @if (session()->has('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            @if (session()->has('warning'))
                                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                    {{ session('warning') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="upload-zone" style="border: 2px dashed #dee2e6; border-radius: 8px; padding: 2rem; text-align: center; transition: all 0.3s ease;" 
                                         ondrop="dropHandler(event);" ondragover="dragOverHandler(event);" ondragleave="dragLeaveHandler(event);">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                        <p class="mb-2">Glissez-déposez vos images ici ou</p>
                                        <input type="file" class="form-control" wire:model="uploadFiles" multiple accept="image/*" 
                                               style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                                        <button type="button" class="btn btn-outline-primary" onclick="this.previousElementSibling.click()">
                                            Choisir des fichiers
                                        </button>
                                    </div>
                                    <div class="form-text mt-2">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Formats acceptés: JPEG, PNG, GIF, WebP. Taille max: 10 MB par fichier.
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex flex-column justify-content-center">
                                    <button type="button" class="btn btn-success mb-2" wire:click="uploadFiles" 
                                            wire:loading.attr="disabled" wire:target="uploadFiles"
                                            @if(!$uploadFiles || count($uploadFiles) === 0) disabled @endif>
                                        <span wire:loading.remove wire:target="uploadFiles">
                                            <i class="fas fa-upload me-1"></i>Uploader 
                                            @if($uploadFiles && count($uploadFiles) > 0)
                                                ({{ count($uploadFiles) }})
                                            @endif
                                        </span>
                                        <span wire:loading wire:target="uploadFiles">
                                            <i class="fas fa-spinner fa-spin me-1"></i>Upload en cours...
                                        </span>
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" wire:click="toggleUploadArea">
                                        <i class="fas fa-times me-1"></i>Annuler
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm mt-1" wire:click="testUpload">
                                        <i class="fas fa-cog me-1"></i>Test config
                                    </button>
                                    <button type="button" class="btn btn-outline-warning btn-sm mt-1" wire:click="debugUploadFiles">
                                        <i class="fas fa-bug me-1"></i>Debug files
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Debug info -->
                            <div class="mt-2 p-2 bg-light rounded">
                                <small class="text-muted">
                                    <strong>Debug:</strong> 
                                    Files: {{ is_array($uploadFiles) ? count($uploadFiles) : 'N/A' }} | 
                                    Type: {{ gettype($uploadFiles) }} | 
                                    Empty: {{ empty($uploadFiles) ? 'Yes' : 'No' }} |
                                    Button disabled: {{ (!$uploadFiles || count($uploadFiles) === 0) ? 'Yes' : 'No' }}
                                </small>
                            </div>

                            <!-- Preview des fichiers sélectionnés -->
                            @if($uploadFiles && count($uploadFiles) > 0)
                            <div class="mt-3">
                                <div class="row g-2">
                                    @foreach($uploadFiles as $index => $file)
                                    <div class="col-2">
                                        <div class="position-relative">
                                            @if($file->getClientOriginalExtension())
                                            <img src="{{ $file->temporaryUrl() }}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                            @else
                                            <div class="bg-light border rounded d-flex align-items-center justify-content-center" style="height: 80px;">
                                                <i class="fas fa-file"></i>
                                            </div>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0" 
                                                    wire:click="removeUploadFile({{ $index }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                        <small class="text-muted">{{ $file->getClientOriginalName() }}</small>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>

                    <!-- Grille des médias -->
                    <div class="p-3" style="max-height: 500px; overflow-y: auto;">
                        @if($media->count() > 0)
                        <div class="row g-3">
                            @foreach($media as $mediaItem)
                            <div class="col-2">
                                <div class="media-card position-relative" 
                                     style="cursor: pointer; transition: all 0.2s ease;"
                                     wire:click="toggleImage({{ $mediaItem->id }})">
                                    
                                    <!-- Image -->
                                    <div class="media-image position-relative" 
                                         style="border: 3px solid {{ in_array($mediaItem->id, $selectedImages) ? '#0d6efd' : 'transparent' }}; border-radius: 8px; overflow: hidden;">
                                        @if($mediaItem->type === 'images')
                                        <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}" 
                                             alt="{{ $mediaItem->translations->first()?->alt_text ?? $mediaItem->original_name }}"
                                             class="img-fluid" 
                                             style="height: 120px; width: 100%; object-fit: cover;">
                                        @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 120px;">
                                            <i class="fas fa-file-alt fa-2x text-muted"></i>
                                        </div>
                                        @endif
                                        
                                        <!-- Overlay de sélection -->
                                        @if(in_array($mediaItem->id, $selectedImages))
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
                                            <small class="text-muted">{{ $this->formatFileSize($mediaItem->size) }}</small>
                                            <small class="text-muted">{{ $mediaItem->created_at->format('d/m/y') }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $media->links() }}
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <h6 class="text-muted">Aucun média trouvé</h6>
                            <p class="text-muted">Essayez de modifier vos critères de recherche ou uploadez de nouveaux fichiers.</p>
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
                            @if(count($selectedImages) === 0) disabled @endif>
                        <i class="fas fa-check me-2"></i>Confirmer la sélection
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Modal d'aperçu -->
    @if($showPreview && $previewImage)
    <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.8); z-index: 1060;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 bg-transparent">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title text-white">{{ $previewImage->translations->first()?->title ?? $previewImage->original_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closePreview"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="{{ asset($previewImage->path) }}" class="img-fluid rounded" style="max-height: 70vh;">
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
                                    <button class="btn btn-sm btn-primary" wire:click="toggleImage({{ $previewImage->id }})">
                                        @if(in_array($previewImage->id, $selectedImages))
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
    </style>

    @script
    <script>
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
        
        // Fermer le modal en cliquant à l'extérieur
        $wire.on('modal-opened', () => {
            document.querySelector('.modal').addEventListener('click', function(e) {
                if (e.target === this) {
                    @this.call('closeModal');
                }
            });
        });
        
        // Fonctions pour le drag & drop
        function dragOverHandler(ev) {
            ev.preventDefault();
            ev.currentTarget.style.borderColor = '#007bff';
            ev.currentTarget.style.backgroundColor = '#f8f9fa';
        }
        
        function dragLeaveHandler(ev) {
            ev.currentTarget.style.borderColor = '#dee2e6';
            ev.currentTarget.style.backgroundColor = 'white';
        }
        
        function dropHandler(ev) {
            ev.preventDefault();
            ev.currentTarget.style.borderColor = '#dee2e6';
            ev.currentTarget.style.backgroundColor = 'white';
            
            const files = ev.dataTransfer.files;
            if (files.length > 0) {
                // Créer un objet FileList compatible
                const fileInput = ev.currentTarget.querySelector('input[type="file"]');
                
                // Méthode alternative pour assigner les fichiers
                const dt = new DataTransfer();
                for (let i = 0; i < files.length; i++) {
                    dt.items.add(files[i]);
                }
                fileInput.files = dt.files;
                
                // Déclencher l'événement change pour Livewire
                const event = new Event('change', { bubbles: true });
                fileInput.dispatchEvent(event);
                
                // Forcer la mise à jour de Livewire
                setTimeout(() => {
                    @this.$refresh();
                }, 100);
            }
        }
        
        // Améliorer le style de la zone d'upload
        document.addEventListener('livewire:initialized', () => {
            updateUploadZoneStyle();
        });
        
        document.addEventListener('livewire:updated', () => {
            updateUploadZoneStyle();
        });
        
        function updateUploadZoneStyle() {
            const uploadZone = document.querySelector('.upload-zone');
            if (uploadZone) {
                uploadZone.addEventListener('mouseenter', function() {
                    this.style.borderColor = '#007bff';
                });
                uploadZone.addEventListener('mouseleave', function() {
                    this.style.borderColor = '#dee2e6';
                });
            }
        }
    </script>
    @endscript
</div>

@php
if (!function_exists('formatFileSize')) {
    function formatFileSize($size) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $unit = 0;
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        return round($size, 1) . ' ' . $units[$unit];
    }
}
@endphp

@script
<script>
    // Méthode pour formater la taille des fichiers
    window.formatFileSize = function(size) {
        const units = ['B', 'KB', 'MB', 'GB'];
        let unit = 0;
        while (size >= 1024 && unit < units.length - 1) {
            size /= 1024;
            unit++;
        }
        return Math.round(size * 10) / 10 + ' ' + units[unit];
    };
    
    // Rendre la fonction disponible pour Livewire
    Livewire.directive('format-file-size', (el, { expression }, { evaluateLater }) => {
        const evaluate = evaluateLater(expression);
        evaluate(size => {
            el.textContent = window.formatFileSize(size);
        });
    });
</script>
@endscript