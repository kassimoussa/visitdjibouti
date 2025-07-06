<div>
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    {{ $isEditMode ? 'Modifier le point d\'intérêt' : 'Ajouter un nouveau point d\'intérêt' }}
                </h5>
            </div>
            <div class="card-body">
                <!-- Sélecteur de langue -->
                <div class="mb-4">
                    <div class="btn-group" role="group">
                        @foreach($availableLocales as $locale)
                            <button type="button"
                                class="btn {{ $activeLocale === $locale ? 'btn-primary' : 'btn-outline-primary' }}"
                                wire:click="changeLocale('{{ $locale }}')">
                                {{ strtoupper($locale) }}
                            </button>
                        @endforeach
                    </div>
                    <div class="form-text">
                        <i class="fas fa-info-circle me-1"></i>
                        Les champs avec <span class="text-danger">*</span> sont obligatoires en français.
                    </div>
                </div>

                <form wire:submit.prevent="save">
                    <div class="row">
                        <!-- Colonne gauche -->
                        <div class="col-md-8">
                            <!-- Informations de base -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations de base</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Nom -->
                                    <div class="mb-3">
                                        <label for="name_{{ $activeLocale }}" class="form-label">
                                            Nom 
                                            @if($activeLocale === 'fr')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="text" 
                                            class="form-control @error('translations.'.$activeLocale.'.name') is-invalid @enderror"
                                            id="name_{{ $activeLocale }}" 
                                            wire:model="translations.{{ $activeLocale }}.name" 
                                            @if($activeLocale === 'fr') required @endif>
                                        @error('translations.'.$activeLocale.'.name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Slug (commun à toutes les langues) -->
                                    @if($activeLocale === 'fr')
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                            id="slug" wire:model="slug">
                                        <div class="form-text">Laissez vide pour générer automatiquement à partir du
                                            nom en français.</div>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endif

                                    <!-- Description courte -->
                                    <div class="mb-3">
                                        <label for="short_description_{{ $activeLocale }}" class="form-label">
                                            Description courte
                                        </label>
                                        <textarea 
                                            class="form-control @error('translations.'.$activeLocale.'.short_description') is-invalid @enderror" 
                                            id="short_description_{{ $activeLocale }}"
                                            wire:model="translations.{{ $activeLocale }}.short_description" 
                                            rows="2"></textarea>
                                        <div class="form-text">Résumé bref qui apparaîtra dans les listes et les
                                            résultats de recherche.</div>
                                        @error('translations.'.$activeLocale.'.short_description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Description complète -->
                                    <div class="mb-3">
                                        <label for="description_{{ $activeLocale }}" class="form-label">
                                            Description complète
                                            @if($activeLocale === 'fr')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <textarea 
                                            class="form-control @error('translations.'.$activeLocale.'.description') is-invalid @enderror" 
                                            id="description_{{ $activeLocale }}"
                                            wire:model="translations.{{ $activeLocale }}.description" 
                                            rows="6"
                                            @if($activeLocale === 'fr') required @endif></textarea>
                                        @error('translations.'.$activeLocale.'.description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Localisation -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Localisation</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Région (commune à toutes les langues) -->
                                    @if($activeLocale === 'fr')
                                    <div class="mb-3">
                                        <label for="region" class="form-label">Région</label>
                                        <select class="form-select @error('region') is-invalid @enderror" id="region"
                                            wire:model="region">
                                            <option value="">Sélectionner une région</option>
                                            @foreach ($regions as $regionKey => $regionName)
                                                <option value="{{ $regionKey }}">{{ $regionName }}</option>
                                            @endforeach
                                        </select>
                                        @error('region')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Coordonnées GPS (communes à toutes les langues) -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="latitude" class="form-label">Latitude</label>
                                                <input type="number" step="any"
                                                    class="form-control @error('latitude') is-invalid @enderror"
                                                    id="latitude" wire:model="latitude">
                                                @error('latitude')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="longitude" class="form-label">Longitude</label>
                                                <input type="number" step="any"
                                                    class="form-control @error('longitude') is-invalid @enderror"
                                                    id="longitude" wire:model="longitude">
                                                @error('longitude')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Adresse (spécifique à chaque langue) -->
                                    <div class="mb-3">
                                        <label for="address_{{ $activeLocale }}" class="form-label">Adresse</label>
                                        <input type="text"
                                            class="form-control @error('translations.'.$activeLocale.'.address') is-invalid @enderror"
                                            id="address_{{ $activeLocale }}" 
                                            wire:model="translations.{{ $activeLocale }}.address">
                                        @error('translations.'.$activeLocale.'.address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Informations complémentaires -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations complémentaires</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Horaires d'ouverture (spécifique à chaque langue) -->
                                            <div class="mb-3">
                                                <label for="opening_hours_{{ $activeLocale }}" class="form-label">Horaires
                                                    d'ouverture</label>
                                                <textarea 
                                                    class="form-control @error('translations.'.$activeLocale.'.opening_hours') is-invalid @enderror" 
                                                    id="opening_hours_{{ $activeLocale }}"
                                                    wire:model="translations.{{ $activeLocale }}.opening_hours" 
                                                    rows="3"></textarea>
                                                @error('translations.'.$activeLocale.'.opening_hours')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Prix d'entrée (spécifique à chaque langue) -->
                                            <div class="mb-3">
                                                <label for="entry_fee_{{ $activeLocale }}" class="form-label">Prix d'entrée</label>
                                                <input type="text"
                                                    class="form-control @error('translations.'.$activeLocale.'.entry_fee') is-invalid @enderror"
                                                    id="entry_fee_{{ $activeLocale }}" 
                                                    wire:model="translations.{{ $activeLocale }}.entry_fee">
                                                <div class="form-text">Ex: 1000 DJF par personne</div>
                                                @error('translations.'.$activeLocale.'.entry_fee')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    @if($activeLocale === 'fr')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Contact (commun à toutes les langues) -->
                                            <div class="mb-3">
                                                <label for="contact" class="form-label">Contact</label>
                                                <input type="text"
                                                    class="form-control @error('contact') is-invalid @enderror"
                                                    id="contact" wire:model="contact">
                                                <div class="form-text">Numéro de téléphone ou email</div>
                                                @error('contact')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <!-- Site web (commun à toutes les langues) -->
                                            <div class="mb-3">
                                                <label for="website" class="form-label">Site web</label>
                                                <input type="url"
                                                    class="form-control @error('website') is-invalid @enderror"
                                                    id="website" wire:model="website">
                                                @error('website')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    <!-- Conseils aux visiteurs (spécifique à chaque langue) -->
                                    <div class="mb-3">
                                        <label for="tips_{{ $activeLocale }}" class="form-label">Conseils aux visiteurs</label>
                                        <textarea 
                                            class="form-control @error('translations.'.$activeLocale.'.tips') is-invalid @enderror" 
                                            id="tips_{{ $activeLocale }}" 
                                            wire:model="translations.{{ $activeLocale }}.tips" 
                                            rows="3"></textarea>
                                        <div class="form-text">Equipement nécessaire, meilleure période pour visiter,
                                            etc.</div>
                                        @error('translations.'.$activeLocale.'.tips')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne droite -->
                        <div class="col-md-4">
                            <!-- Options -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Options de publication</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Statut -->
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Statut</label>
                                        <select class="form-select @error('status') is-invalid @enderror"
                                            id="status" wire:model="status">
                                            <option value="draft">Brouillon</option>
                                            <option value="published">Publié</option>
                                            <option value="archived">Archivé</option>
                                        </select>
                                        @error('status')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Options -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="is_featured"
                                                wire:model="is_featured">
                                            <label class="form-check-label" for="is_featured">Mettre en avant sur la
                                                page d'accueil</label>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="allow_reservations"
                                                wire:model="allow_reservations">
                                            <label class="form-check-label" for="allow_reservations">Activer les
                                                réservations</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Catégories -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Catégories</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 @error('selectedCategories') is-invalid @enderror">
                                        @foreach ($categories as $category)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox"
                                                    id="category-{{ $category->id }}" value="{{ $category->id }}"
                                                    wire:model="selectedCategories">
                                                <label class="form-check-label" for="category-{{ $category->id }}">
                                                    <span class="badge"
                                                        style="background-color: {{ $category->color ?? '#6c757d' }}">
                                                        <i class="{{ $category->icon }}"></i>
                                                    </span>
                                                    {{ $category->translation($activeLocale)?->name ?: $category->translation('fr')->name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('selectedCategories')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Image mise en avant -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Image principale</h6>
                                    <button type="button" class="btn btn-sm btn-primary" wire:click="openFeaturedImageSelector">
                                        <i class="fas fa-images me-1"></i>Choisir une image
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if ($featuredImageId)
                                        <div class="text-center">
                                            @php $featuredImage = $media->firstWhere('id', $featuredImageId); @endphp
                                            @if ($featuredImage)
                                                <div class="position-relative d-inline-block">
                                                    <img src="{{ asset($featuredImage->thumbnail_path ?? $featuredImage->path) }}"
                                                        alt="{{ $featuredImage->getTranslation($activeLocale)->title ?? $featuredImage->original_name }}"
                                                        class="img-fluid rounded border shadow-sm"
                                                        style="max-height: 200px; max-width: 100%;">
                                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                                            wire:click="selectFeaturedImage(null)"
                                                            title="Supprimer l'image">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="mt-2">
                                                    <small class="text-muted">
                                                        {{ $featuredImage->getTranslation($activeLocale)->title ?? $featuredImage->original_name }}
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="text-center py-4 border-dashed rounded" style="border: 2px dashed #dee2e6;">
                                            <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-2">Aucune image sélectionnée</p>
                                            <button type="button" class="btn btn-outline-primary" wire:click="openFeaturedImageSelector">
                                                <i class="fas fa-plus me-1"></i>Sélectionner une image
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Galerie d'images -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Galerie d'images ({{ count($selectedMedia) }})</h6>
                                    <button type="button" class="btn btn-sm btn-success" wire:click="openGallerySelector">
                                        <i class="fas fa-plus me-1"></i>Ajouter des images
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if (count($selectedMedia) > 0)
                                        <div class="mb-3">
                                            <div class="alert alert-info alert-sm">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Glissez-déposez les images pour les réorganiser
                                            </div>
                                            <div id="gallery-images" class="row g-3">
                                                @foreach ($selectedMedia as $index => $mediaId)
                                                    @php $mediaItem = $media->firstWhere('id', $mediaId); @endphp
                                                    @if ($mediaItem)
                                                        <div class="col-4 col-md-3 col-lg-2" data-media-id="{{ $mediaId }}">
                                                            <div class="gallery-item position-relative">
                                                                <div class="image-container" style="cursor: grab;">
                                                                    <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                                        alt="{{ $mediaItem->getTranslation($activeLocale)->alt_text ?? $mediaItem->original_name }}"
                                                                        class="img-fluid rounded border shadow-sm"
                                                                        style="height: 120px; width: 100%; object-fit: cover;">
                                                                    <div class="image-overlay">
                                                                        <div class="overlay-actions">
                                                                            <button type="button" class="btn btn-sm btn-light btn-icon" 
                                                                                    title="Définir comme image principale"
                                                                                    wire:click="selectFeaturedImage({{ $mediaId }})">
                                                                                <i class="fas fa-star {{ $featuredImageId == $mediaId ? 'text-warning' : 'text-muted' }}"></i>
                                                                            </button>
                                                                            <button type="button" class="btn btn-sm btn-danger btn-icon"
                                                                                    title="Supprimer de la galerie"
                                                                                    wire:click="removeFromGallery({{ $mediaId }})">
                                                                                <i class="fas fa-times"></i>
                                                                            </button>
                                                                        </div>
                                                                        <div class="drag-handle">
                                                                            <i class="fas fa-grip-vertical"></i>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="mt-2 text-center">
                                                                    <small class="text-muted d-block text-truncate">
                                                                        {{ $mediaItem->getTranslation($activeLocale)->title ?? $mediaItem->original_name }}
                                                                    </small>
                                                                    <span class="badge badge-sm bg-primary">{{ $index + 1 }}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center py-4 border-dashed rounded" style="border: 2px dashed #dee2e6;">
                                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                            <p class="text-muted mb-2">Aucune image dans la galerie</p>
                                            <button type="button" class="btn btn-outline-success" wire:click="openGallerySelector">
                                                <i class="fas fa-plus me-1"></i>Ajouter des images
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('pois.index') }}" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditMode ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de sélection de médias -->
    @livewire('admin.media-selector-modal')

    <!-- Styles CSS pour l'amélioration de l'interface -->
    <style>
        .border-dashed {
            border-style: dashed !important;
        }
        
        .gallery-item {
            transition: transform 0.2s ease;
        }
        
        .gallery-item:hover {
            transform: translateY(-2px);
        }
        
        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }
        
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 8px;
        }
        
        .image-container:hover .image-overlay {
            opacity: 1;
        }
        
        .overlay-actions {
            display: flex;
            justify-content: flex-end;
            gap: 4px;
        }
        
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .drag-handle {
            align-self: center;
            color: white;
            font-size: 18px;
            cursor: grab;
        }
        
        .drag-handle:active {
            cursor: grabbing;
        }
        
        .sortable-ghost {
            opacity: 0.5;
        }
        
        .sortable-chosen {
            transform: scale(1.05);
        }
        
        .badge-sm {
            font-size: 0.7rem;
        }
        
        .alert-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    </style>
</div>

@script
<script>
    // Initialiser SortableJS pour le drag & drop de la galerie
    document.addEventListener('livewire:initialized', () => {
        initializeGallerySorting();
    });
    
    // Réinitialiser après mise à jour Livewire
    document.addEventListener('livewire:updated', () => {
        initializeGallerySorting();
    });
    
    function initializeGallerySorting() {
        const galleryContainer = document.getElementById('gallery-images');
        if (galleryContainer && typeof Sortable !== 'undefined') {
            new Sortable(galleryContainer, {
                animation: 150,
                ghostClass: 'sortable-ghost',
                chosenClass: 'sortable-chosen',
                handle: '.drag-handle',
                onEnd: function(evt) {
                    if (evt.oldIndex !== evt.newIndex) {
                        @this.call('reorderGallery', evt.oldIndex, evt.newIndex);
                    }
                }
            });
        }
    }
</script>
@endscript