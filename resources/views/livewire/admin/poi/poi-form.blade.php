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
                                    @endif

                                    <!-- Carte interactive pour géolocalisation (toujours visible) -->
                                    <div class="mb-3" wire:ignore>
                                        <label class="form-label">Localisation sur la carte</label>
                                        <div class="border rounded" style="height: 300px; width: 100%; position: relative;">
                                            <div id="poiLocationMap" style="height: 300px; width: 100%; min-height: 300px; min-width: 200px;"></div>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 
                                            Cliquez sur la carte pour définir l'emplacement du POI
                                        </div>
                                        @error('latitude') 
                                            <div class="text-danger small">{{ $message }}</div> 
                                        @enderror
                                        @error('longitude') 
                                            <div class="text-danger small">{{ $message }}</div> 
                                        @enderror
                                    </div>

                                    @if($activeLocale === 'fr')
                                    <!-- Recherche d'adresse -->
                                    <div class="mb-3">
                                        <label for="poiAddressSearch" class="form-label">Rechercher une adresse</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="poiAddressSearch" 
                                                   placeholder="Tapez une adresse pour la localiser sur la carte">
                                            <button type="button" class="btn btn-outline-primary" id="poiSearchButton">
                                                <i class="fas fa-search"></i> Rechercher
                                            </button>
                                        </div>
                                        <div class="form-text">Exemple: "Place Mahmoud Harbi, Djibouti"</div>
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
                                    <h6 class="mb-0">
                                        <i class="fas fa-tags me-2"></i>Catégories
                                        <span class="badge bg-secondary ms-2" id="selectedCount">
                                            {{ count($selectedCategories) }} sélectionnée(s)
                                        </span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3 @error('selectedCategories') is-invalid @enderror">
                                        <div class="accordion" id="categoriesAccordion">
                                            @foreach ($parentCategories as $parentCategory)
                                                <div class="accordion-item">
                                                    <h2 class="accordion-header" id="heading{{ $parentCategory->id }}">
                                                        <button class="accordion-button collapsed" 
                                                                type="button" 
                                                                data-bs-toggle="collapse" 
                                                                data-bs-target="#collapse{{ $parentCategory->id }}" 
                                                                aria-expanded="false" 
                                                                aria-controls="collapse{{ $parentCategory->id }}">
                                                            <span class="badge me-3"
                                                                style="background-color: {{ $parentCategory->color ?? '#6c757d' }}">
                                                                <i class="{{ $parentCategory->icon ?? 'fas fa-folder' }}"></i>
                                                            </span>
                                                            <strong>{{ $parentCategory->translation($activeLocale)?->name ?? $parentCategory->translation('fr')?->name ?? 'Sans nom' }}</strong>
                                                            <span class="ms-auto me-3">
                                                                <small class="text-muted">
                                                                    {{ $parentCategory->children->count() }} sous-catégorie(s)
                                                                </small>
                                                            </span>
                                                        </button>
                                                    </h2>
                                                    <div id="collapse{{ $parentCategory->id }}" 
                                                         class="accordion-collapse collapse" 
                                                         aria-labelledby="heading{{ $parentCategory->id }}" 
                                                         data-bs-parent="#categoriesAccordion">
                                                        <div class="accordion-body">
                                                            @if($parentCategory->children->isNotEmpty())
                                                                <div class="row">
                                                                    @foreach ($parentCategory->children->sortBy(function($child) use ($activeLocale) {
                                                                        $translation = $child->translation($activeLocale);
                                                                        return $translation ? $translation->name : '';
                                                                    }) as $subcategory)
                                                                        <div class="col-12 mb-2">
                                                                            <div class="form-check">
                                                                                <input class="form-check-input" type="checkbox"
                                                                                    id="category-{{ $subcategory->id }}" 
                                                                                    value="{{ $subcategory->id }}"
                                                                                    wire:model="selectedCategories">
                                                                                <label class="form-check-label" 
                                                                                       for="category-{{ $subcategory->id }}">
                                                                                    {{ $subcategory->translation($activeLocale)?->name ?? $subcategory->translation('fr')?->name ?? 'Sans nom' }}
                                                                                </label>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            @else
                                                                <div class="text-muted">
                                                                    <i class="fas fa-info-circle me-2"></i>
                                                                    Aucune sous-catégorie disponible
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        @if(count($selectedCategories) > 0)
                                            <div class="mt-3 p-3 bg-light rounded">
                                                <h6 class="mb-2">
                                                    <i class="fas fa-check-circle text-success me-2"></i>
                                                    Catégories sélectionnées :
                                                </h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @foreach($selectedCategories as $categoryId)
                                                        @php
                                                            $selectedCategory = null;
                                                            foreach($parentCategories as $parent) {
                                                                foreach($parent->children as $child) {
                                                                    if($child->id == $categoryId) {
                                                                        $selectedCategory = $child;
                                                                        break 2;
                                                                    }
                                                                }
                                                            }
                                                        @endphp
                                                        @if($selectedCategory)
                                                            <span class="badge rounded-pill"
                                                                  style="background-color: {{ $selectedCategory->color ?? '#6c757d' }}">
                                                                <i class="{{ $selectedCategory->icon ?? 'fas fa-folder' }} me-1"></i>
                                                                {{ $selectedCategory->translation($activeLocale)?->name ?? $selectedCategory->translation('fr')?->name ?? 'Sans nom' }}
                                                                <button type="button" class="btn-close btn-close-white ms-2" 
                                                                        style="font-size: 0.6em;"
                                                                        wire:click="$set('selectedCategories', {{ json_encode(array_values(array_filter($selectedCategories, fn($id) => $id != $categoryId))) }})"></button>
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    @error('selectedCategories')
                                        <div class="text-danger">
                                            <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                                        </div>
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

                            <!-- Contacts multiples avec modal -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">Contacts ({{ count($contacts) }})</h6>
                                    <button type="button" class="btn btn-sm btn-success" wire:click="openContactModal">
                                        <i class="fas fa-plus me-1"></i>Ajouter un contact
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if(count($contacts) > 0)
                                        <div class="contacts-list">
                                            @foreach($contacts as $index => $contact)
                                                <div class="contact-item border rounded p-3 mb-3 position-relative"
                                                     style="background: linear-gradient(135deg, {{ $this->getContactTypeColor($contact['type'] ?? 'general') }}15, {{ $this->getContactTypeColor($contact['type'] ?? 'general') }}05);">
                                                    
                                                    @if($contact['is_primary'] ?? false)
                                                        <span class="position-absolute top-0 end-0 mt-2 me-2">
                                                            <i class="fas fa-star text-warning" title="Contact principal"></i>
                                                        </span>
                                                    @endif
                                                    
                                                    <div class="d-flex align-items-start">
                                                        <div class="contact-type-badge me-3 d-flex align-items-center justify-content-center rounded-circle" 
                                                             style="background-color: {{ $this->getContactTypeColor($contact['type'] ?? 'general') }}; width: 48px; height: 48px; color: white;">
                                                            <i class="{{ $this->getContactTypeIcon($contact['type'] ?? 'general') }} fa-lg"></i>
                                                        </div>
                                                        
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                                <div>
                                                                    <h6 class="mb-1 fw-bold">{{ $contact['name'] ?: 'Nouveau contact' }}</h6>
                                                                    <span class="badge rounded-pill px-2 py-1" 
                                                                          style="background-color: {{ $this->getContactTypeColor($contact['type'] ?? 'general') }}20; color: {{ $this->getContactTypeColor($contact['type'] ?? 'general') }};">
                                                                        {{ $this->getContactTypes()[$contact['type'] ?? 'general'] ?? 'Type non défini' }}
                                                                    </span>
                                                                </div>
                                                                <div class="btn-group btn-group-sm">
                                                                    <button type="button" 
                                                                            class="btn btn-outline-primary"
                                                                            wire:click="editContact({{ $index }})"
                                                                            title="Modifier">
                                                                        <i class="fas fa-edit"></i>
                                                                    </button>
                                                                    <button type="button" 
                                                                            class="btn btn-outline-danger"
                                                                            wire:click="removeContact({{ $index }})"
                                                                            onclick="return confirm('Supprimer ce contact ?')"
                                                                            title="Supprimer">
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            
                                                            <div class="contact-info small">
                                                                @if(!empty($contact['phone']))
                                                                    <div class="d-flex align-items-center mb-1">
                                                                        <i class="fas fa-phone text-muted me-2" style="width: 16px;"></i>
                                                                        <span>{{ $contact['phone'] }}</span>
                                                                    </div>
                                                                @endif
                                                                @if(!empty($contact['email']))
                                                                    <div class="d-flex align-items-center mb-1">
                                                                        <i class="fas fa-envelope text-muted me-2" style="width: 16px;"></i>
                                                                        <span>{{ $contact['email'] }}</span>
                                                                    </div>
                                                                @endif
                                                                @if(!empty($contact['address']))
                                                                    <div class="d-flex align-items-start mb-1">
                                                                        <i class="fas fa-map-marker-alt text-muted me-2 mt-1" style="width: 16px;"></i>
                                                                        <span>{{ $contact['address'] }}</span>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-5 border-dashed rounded" style="border: 2px dashed #dee2e6;">
                                            <i class="fas fa-address-book fa-4x text-muted mb-3"></i>
                                            <h6 class="text-muted mb-3">Aucun contact ajouté</h6>
                                            <p class="text-muted mb-3">Ajoutez des contacts pour faciliter la prise de contact avec ce POI</p>
                                            <button type="button" class="btn btn-success" wire:click="openContactModal">
                                                <i class="fas fa-plus me-2"></i>Ajouter le premier contact
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tour Operators associés -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Tour Operators Associés ({{ count($selectedTourOperators) }})</h6>
                            <button type="button" class="btn btn-sm btn-success" wire:click="openTourOperatorModal">
                                <i class="fas fa-plus me-1"></i>Associer un tour operator
                            </button>
                        </div>
                        <div class="card-body">
                            @if(count($selectedTourOperators) > 0)
                                <div class="row g-3">
                                    @foreach($selectedTourOperators as $index => $tourOperatorData)
                                        @php
                                            $tourOperator = $tourOperators->firstWhere('id', $tourOperatorData['id']);
                                            $serviceType = $tourOperatorData['service_type'] ?? 'other';
                                            $isPrimary = $tourOperatorData['is_primary'] ?? false;
                                        @endphp
                                        <div class="col-md-6">
                                            <div class="card border-start border-4 tour-operator-item h-100" 
                                                 style="border-color: {{ $this->getServiceTypeColor($serviceType) }} !important;">
                                                <div class="card-body p-3">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div class="d-flex align-items-center">
                                                            <div class="service-type-badge rounded-circle d-flex align-items-center justify-content-center me-3" 
                                                                 style="background-color: {{ $this->getServiceTypeColor($serviceType) }}; width: 40px; height: 40px;">
                                                                <i class="{{ $this->getServiceTypeIcon($serviceType) }} text-white"></i>
                                                            </div>
                                                            <div>
                                                                <h6 class="mb-0">{{ $tourOperator?->getTranslatedName($activeLocale) ?? 'Tour operator introuvable' }}</h6>
                                                                <div class="d-flex align-items-center mt-1">
                                                                    <span class="badge rounded-pill" 
                                                                          style="background-color: {{ $this->getServiceTypeColor($serviceType) }}">
                                                                        {{ $this->getServiceTypes()[$serviceType] ?? $serviceType }}
                                                                    </span>
                                                                    @if($isPrimary)
                                                                        <span class="badge bg-warning text-dark ms-1">
                                                                            <i class="fas fa-star me-1"></i>Principal
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="btn-group" role="group">
                                                            <button type="button" 
                                                                    class="btn btn-outline-primary btn-sm"
                                                                    wire:click="editTourOperator({{ $index }})"
                                                                    title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button type="button" 
                                                                    class="btn btn-outline-danger btn-sm"
                                                                    wire:click="removeTourOperator({{ $index }})"
                                                                    onclick="return confirm('Supprimer cette association ?')"
                                                                    title="Supprimer">
                                                                <i class="fas fa-unlink"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    
                                                    @if($tourOperator)
                                                        <div class="tour-operator-info small text-muted">
                                                            @if($tourOperator->first_phone)
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="fas fa-phone me-2" style="width: 16px;"></i>
                                                                    <span>{{ $tourOperator->first_phone }}</span>
                                                                </div>
                                                            @endif
                                                            @if($tourOperator->first_email)
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="fas fa-envelope me-2" style="width: 16px;"></i>
                                                                    <span>{{ $tourOperator->first_email }}</span>
                                                                </div>
                                                            @endif
                                                            @if($tourOperator->website)
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <i class="fas fa-globe me-2" style="width: 16px;"></i>
                                                                    <a href="{{ $tourOperator->website_url }}" target="_blank" class="text-decoration-none">
                                                                        {{ $tourOperator->website }}
                                                                    </a>
                                                                </div>
                                                            @endif
                                                            @if(!empty($tourOperatorData['notes']))
                                                                <div class="mt-2">
                                                                    <i class="fas fa-sticky-note me-2" style="width: 16px;"></i>
                                                                    <small class="fst-italic">{{ $tourOperatorData['notes'] }}</small>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 border-dashed rounded" style="border: 2px dashed #dee2e6;">
                                    <i class="fas fa-route fa-4x text-muted mb-3"></i>
                                    <h6 class="text-muted mb-3">Aucun tour operator associé</h6>
                                    <p class="text-muted mb-3">Associez des tour operators pour enrichir l'expérience des visiteurs</p>
                                    <button type="button" class="btn btn-success" wire:click="openTourOperatorModal">
                                        <i class="fas fa-plus me-2"></i>Associer le premier tour operator
                                    </button>
                                </div>
                            @endif
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

    <!-- Modal de gestion des contacts -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">
                        @if($editingContactIndex !== null)
                            <i class="fas fa-edit me-2"></i>Modifier le contact
                        @else
                            <i class="fas fa-plus me-2"></i>Ajouter un contact
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="cancelContactEdit" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                        <!-- Messages d'erreur globaux -->
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        <div class="row g-3">
                            <!-- Nom du contact -->
                            <div class="col-md-6">
                                <label class="form-label">Nom <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('modalContact.name') is-invalid @enderror"
                                       wire:model="modalContact.name"
                                       placeholder="Ex: Restaurant Le Palmier"
                                       required>
                                @error('modalContact.name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Type de contact -->
                            <div class="col-md-6">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select class="form-select @error('modalContact.type') is-invalid @enderror"
                                        wire:model="modalContact.type"
                                        required>
                                    <option value="">Sélectionner un type</option>
                                    @foreach($this->getContactTypes() as $typeKey => $typeName)
                                        <option value="{{ $typeKey }}">{{ $typeName }}</option>
                                    @endforeach
                                </select>
                                @error('modalContact.type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Téléphone -->
                            <div class="col-md-6">
                                <label class="form-label">Téléphone</label>
                                <input type="tel" 
                                       class="form-control @error('modalContact.phone') is-invalid @enderror"
                                       wire:model="modalContact.phone"
                                       placeholder="+253 77 XX XX XX">
                                @error('modalContact.phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" 
                                       class="form-control @error('modalContact.email') is-invalid @enderror"
                                       wire:model="modalContact.email"
                                       placeholder="contact@example.com">
                                @error('modalContact.email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Site web -->
                            <div class="col-12">
                                <label class="form-label">Site web</label>
                                <input type="text" 
                                       class="form-control @error('modalContact.website') is-invalid @enderror"
                                       wire:model="modalContact.website"
                                       placeholder="www.example.com ou https://www.example.com">
                                @error('modalContact.website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Adresse -->
                            <div class="col-12">
                                <label class="form-label">Adresse</label>
                                <input type="text" 
                                       class="form-control @error('modalContact.address') is-invalid @enderror"
                                       wire:model="modalContact.address"
                                       placeholder="Ex: Avenue Hassan Gouled, Djibouti">
                                @error('modalContact.address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Description -->
                            <div class="col-12">
                                <label class="form-label">Description</label>
                                <textarea class="form-control @error('modalContact.description') is-invalid @enderror"
                                          wire:model="modalContact.description"
                                          rows="3"
                                          placeholder="Spécialités, services, horaires..."></textarea>
                                @error('modalContact.description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <!-- Contact principal -->
                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           wire:model="modalContact.is_primary"
                                           id="modalContactPrimary">
                                    <label class="form-check-label" for="modalContactPrimary">
                                        <strong>Contact principal</strong>
                                        <div class="form-text">Ce contact sera mis en avant et considéré comme le contact principal du POI</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        @if(!empty($modalContact['type']) && $modalContact['type'])
                            <div class="alert alert-info mt-3">
                                <div class="d-flex align-items-center">
                                    <div class="contact-type-preview me-3 d-flex align-items-center justify-content-center rounded-circle"
                                         style="background-color: {{ $this->getContactTypeColor($modalContact['type']) }}; width: 40px; height: 40px; color: white;">
                                        <i class="{{ $this->getContactTypeIcon($modalContact['type']) }}"></i>
                                    </div>
                                    <div>
                                        <strong>Aperçu du type:</strong> {{ $this->getContactTypes()[$modalContact['type']] ?? 'Type inconnu' }}
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        <!-- Debug temporaire -->
                        @if(config('app.debug'))
                            <div class="alert alert-secondary mt-2">
                                <small>
                                    <strong>Debug:</strong> 
                                    Mode: {{ $editingContactIndex !== null ? 'Edition (index: '.$editingContactIndex.')' : 'Ajout' }} |
                                    Nom: "{{ $modalContact['name'] ?? 'vide' }}" |
                                    Type: "{{ $modalContact['type'] ?? 'vide' }}"
                                </small>
                            </div>
                        @endif
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelContactEdit">
                            <i class="fas fa-times me-1"></i>Annuler
                        </button>
                        
                        
                        <button type="button" class="btn btn-primary" 
                                wire:click="saveContact" 
                                data-editing-index="{{ $editingContactIndex }}"
                                id="saveContactButton">
                            @if($editingContactIndex !== null)
                                <i class="fas fa-save me-1"></i>Mettre à jour
                            @else
                                <i class="fas fa-plus me-1"></i>Ajouter
                            @endif
                        </button>
                    </div>
            </div>
        </div>
    </div>

    <!-- Modal de gestion des tour operators -->
    <div class="modal fade" id="tourOperatorModal" tabindex="-1" aria-labelledby="tourOperatorModalLabel" wire:ignore.self>
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tourOperatorModalLabel">
                        @if($editingTourOperatorIndex !== null)
                            <i class="fas fa-edit me-2"></i>Modifier l'association
                        @else
                            <i class="fas fa-plus me-2"></i>Associer un tour operator
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="cancelTourOperatorEdit" aria-label="Close"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Messages d'erreur globaux -->
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif
                    
                    <div class="row g-3">
                        <!-- Sélection du tour operator -->
                        <div class="col-12">
                            <label class="form-label">Tour Operator <span class="text-danger">*</span></label>
                            <select class="form-select @error('modalTourOperator.tour_operator_id') is-invalid @enderror"
                                    wire:model="modalTourOperator.tour_operator_id"
                                    required>
                                <option value="">Sélectionner un tour operator</option>
                                @foreach($tourOperators as $operator)
                                    <option value="{{ $operator->id }}">
                                        {{ $operator->getTranslatedName($activeLocale) }}
                                        @if($operator->featured)
                                            ⭐
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('modalTourOperator.tour_operator_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            @if($tourOperators->isEmpty())
                                <div class="form-text text-warning">
                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                    Aucun tour operator actif disponible. 
                                    <a href="{{ route('tour-operators.create') }}" target="_blank" class="text-decoration-none">
                                        Créer un tour operator
                                    </a>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Type de service -->
                        <div class="col-md-6">
                            <label class="form-label">Type de service <span class="text-danger">*</span></label>
                            <select class="form-select @error('modalTourOperator.service_type') is-invalid @enderror"
                                    wire:model="modalTourOperator.service_type"
                                    required>
                                @foreach($this->getServiceTypes() as $typeKey => $typeName)
                                    <option value="{{ $typeKey }}">
                                        {{ $typeName }}
                                    </option>
                                @endforeach
                            </select>
                            @error('modalTourOperator.service_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Tour operator principal -->
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       wire:model="modalTourOperator.is_primary"
                                       id="modalTourOperatorPrimary">
                                <label class="form-check-label" for="modalTourOperatorPrimary">
                                    <strong>Tour operator principal</strong>
                                    <div class="form-text">Ce tour operator sera mis en avant pour ce POI</div>
                                </label>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control @error('modalTourOperator.notes') is-invalid @enderror"
                                      wire:model="modalTourOperator.notes"
                                      rows="3"
                                      placeholder="Informations spécifiques sur cette association (horaires, tarifs, conditions particulières...)"></textarea>
                            @error('modalTourOperator.notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Aperçu du type de service sélectionné -->
                        @if(!empty($modalTourOperator['service_type']))
                            <div class="col-12">
                                <div class="alert alert-info d-flex align-items-center">
                                    <div class="service-type-preview rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="background-color: {{ $this->getServiceTypeColor($modalTourOperator['service_type']) }}; width: 36px; height: 36px;">
                                        <i class="{{ $this->getServiceTypeIcon($modalTourOperator['service_type']) }} text-white"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $this->getServiceTypes()[$modalTourOperator['service_type']] ?? $modalTourOperator['service_type'] }}</strong>
                                        <div class="small">Ce tour operator proposera ce type de service pour ce POI</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="cancelTourOperatorEdit">
                        <i class="fas fa-times me-1"></i>Annuler
                    </button>
                    
                    <button type="button" class="btn btn-primary" wire:click="saveTourOperator">
                        @if($editingTourOperatorIndex !== null)
                            <i class="fas fa-save me-1"></i>Mettre à jour
                        @else
                            <i class="fas fa-link me-1"></i>Associer
                        @endif
                    </button>
                </div>
            </div>
        </div>
    </div>

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

        /* Styles pour les contacts */
        .contact-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent !important;
        }

        .contact-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        }

        .contact-type-badge {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .contact-item:hover .contact-type-badge {
            transform: scale(1.1);
        }

        .contact-type-preview {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        /* Animation du modal */
        .modal.fade .modal-dialog {
            transform: scale(0.8);
            transition: transform 0.3s ease;
        }

        .modal.show .modal-dialog {
            transform: scale(1);
        }

        /* Styles pour les alertes de preview */
        .alert-info {
            background: linear-gradient(135deg, #e3f2fd, #f8f9fa);
            border: none;
            border-left: 4px solid #2196f3;
        }

        /* Empty state styles */
        .border-dashed {
            border-style: dashed !important;
            transition: all 0.3s ease;
        }

        .border-dashed:hover {
            border-color: var(--bs-success) !important;
            background-color: rgba(25, 135, 84, 0.05);
        }

        /* Styles pour les tour operators */
        .tour-operator-item {
            transition: all 0.3s ease;
            border-left: 4px solid transparent !important;
        }

        .tour-operator-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
        }

        .service-type-badge {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s ease;
        }

        .tour-operator-item:hover .service-type-badge {
            transform: scale(1.1);
        }

        .service-type-preview {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</div>

@push('scripts')
<script>
    // Gestion du modal de contacts
    document.addEventListener('livewire:init', function () {
        let contactModal = null;
        const modalElement = document.getElementById('contactModal');
        
        // Initialiser le modal Bootstrap
        if (modalElement) {
            contactModal = new bootstrap.Modal(modalElement, {
                backdrop: 'static',
                keyboard: true,
                focus: true
            });
            
            // Gérer la fermeture propre du modal
            modalElement.addEventListener('hidden.bs.modal', function () {
                // S'assurer que les classes et attributs sont correctement nettoyés
                modalElement.removeAttribute('aria-hidden');
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                
                // Nettoyer le backdrop s'il reste
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                
                // Restaurer le scroll du body
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
                
                // Remettre le focus sur un élément approprié
                const addContactBtn = document.querySelector('[wire\\:click="openContactModal"]');
                if (addContactBtn) {
                    addContactBtn.focus();
                }
            });
        }

        // Écouter les événements Livewire pour le modal
        Livewire.on('open-contact-modal', () => {
            if (contactModal) {
                contactModal.show();
            }
        });

        Livewire.on('close-contact-modal', () => {
            if (contactModal) {
                contactModal.hide();
            }
        });
        
        // Empêcher la fermeture automatique lors des mises à jour Livewire
        let modalWasOpen = false;
        
        document.addEventListener('livewire:morph-start', function() {
            // Mémoriser si le modal était ouvert avant la mise à jour
            modalWasOpen = modalElement && modalElement.classList.contains('show');
        });
        
        document.addEventListener('livewire:morph-end', function() {
            // Si le modal était ouvert et qu'il s'est fermé, le rouvrir
            if (modalWasOpen && contactModal && !modalElement.classList.contains('show')) {
                setTimeout(() => {
                    contactModal.show();
                }, 100);
            }
        });
        
        // Feedback visuel pour la sauvegarde des contacts
        Livewire.on('contact-saved', (data) => {
            // Optionnel : affichage discret du succès
            console.log('Contact sauvegardé:', data);
        });

        // Gestion du modal de tour operators
        let tourOperatorModal = null;
        const tourOperatorModalElement = document.getElementById('tourOperatorModal');
        
        // Initialiser le modal Bootstrap pour tour operators
        if (tourOperatorModalElement) {
            tourOperatorModal = new bootstrap.Modal(tourOperatorModalElement, {
                backdrop: 'static',
                keyboard: true,
                focus: true
            });
            
            // Gérer la fermeture propre du modal
            tourOperatorModalElement.addEventListener('hidden.bs.modal', function () {
                // S'assurer que les classes et attributs sont correctement nettoyés
                tourOperatorModalElement.removeAttribute('aria-hidden');
                tourOperatorModalElement.classList.remove('show');
                tourOperatorModalElement.style.display = 'none';
                
                // Nettoyer le backdrop s'il reste
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                
                // Restaurer le scroll du body
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
                
                // Remettre le focus sur un élément approprié
                const addTourOperatorBtn = document.querySelector('[wire\\:click="openTourOperatorModal"]');
                if (addTourOperatorBtn) {
                    addTourOperatorBtn.focus();
                }
            });
        }

        // Écouter les événements Livewire pour le modal tour operators
        Livewire.on('open-tour-operator-modal', () => {
            if (tourOperatorModal) {
                tourOperatorModal.show();
            }
        });

        Livewire.on('close-tour-operator-modal', () => {
            if (tourOperatorModal) {
                tourOperatorModal.hide();
            }
        });
        
        // Empêcher la fermeture automatique lors des mises à jour Livewire (tour operators)
        let tourOperatorModalWasOpen = false;
        
        document.addEventListener('livewire:morph-start', function() {
            // Mémoriser si le modal tour operator était ouvert avant la mise à jour
            tourOperatorModalWasOpen = tourOperatorModalElement && tourOperatorModalElement.classList.contains('show');
        });
        
        document.addEventListener('livewire:morph-end', function() {
            // Si le modal tour operator était ouvert et qu'il s'est fermé, le rouvrir
            if (tourOperatorModalWasOpen && tourOperatorModal && !tourOperatorModalElement.classList.contains('show')) {
                setTimeout(() => {
                    tourOperatorModal.show();
                }, 100);
            }
        });
        
        // Gérer la fermeture avec Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && modalElement.classList.contains('show')) {
                if (contactModal) {
                    contactModal.hide();
                }
            }
        });
        
        // Rendre la fonction accessible globalement
        window.contactModal = contactModal;
    });
    
    // Fonction globale pour fermer le modal
    function closeContactModal() {
        if (window.contactModal) {
            window.contactModal.hide();
        }
        
        // Alternative si le modal ne se ferme pas correctement
        setTimeout(() => {
            const modalElement = document.getElementById('contactModal');
            if (modalElement && modalElement.classList.contains('show')) {
                modalElement.classList.remove('show');
                modalElement.style.display = 'none';
                modalElement.removeAttribute('aria-hidden');
                
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.remove();
                }
                
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }
        }, 100);
    }
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la carte seulement si on est en français (où les coordonnées sont visibles)
    if (document.getElementById('poiLocationMap')) {
        initPoiLocationMap();
    }
});

// Variables globales pour garder la référence de la carte
let globalPoiMap = null;
let globalPoiMarker = null;

function initPoiLocationMap() {
    // Si la carte existe déjà, ne pas la recréer
    if (globalPoiMap) {
        return;
    }

    // Coordonnées par défaut (centre de Djibouti)
    const defaultLat = @json($latitude) || 11.5721;
    const defaultLng = @json($longitude) || 43.1456;
    
    // Initialiser la carte
    globalPoiMap = L.map('poiLocationMap').setView([defaultLat, defaultLng], 12);
    const map = globalPoiMap;
    
    // Ajouter les tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Marqueur pour la position
    let marker = globalPoiMarker;
    
    // Si des coordonnées existent déjà, placer le marqueur
    if (@json($latitude) && @json($longitude)) {
        marker = L.marker([defaultLat, defaultLng]).addTo(map);
        globalPoiMarker = marker;
    }
    
    // Gestionnaire de clic sur la carte
    map.on('click', function(e) {
        // Supprimer l'ancien marqueur s'il existe
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Ajouter nouveau marqueur
        marker = L.marker(e.latlng).addTo(map);
        globalPoiMarker = marker;
        
        // Mettre à jour les propriétés Livewire sans déclencher de re-render
        @this.latitude = e.latlng.lat;
        @this.longitude = e.latlng.lng;
        
        // Optionnel: afficher une notification
        showPoiToast('Position mise à jour: ' + e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6));
    });
    
    // Recherche d'adresse
    const searchButton = document.getElementById('poiSearchButton');
    const searchInput = document.getElementById('poiAddressSearch');
    
    if (searchButton && searchInput) {
        // Recherche au clic
        searchButton.addEventListener('click', function() {
            searchPoiAddress();
        });
        
        // Recherche à l'appui de Entrée
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchPoiAddress();
            }
        });
    }
    
    function searchPoiAddress() {
        const address = searchInput.value.trim();
        if (!address) return;
        
        // Désactiver le bouton pendant la recherche
        searchButton.disabled = true;
        searchButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Recherche...';
        
        // Utiliser l'API Nominatim pour géocoder
        const query = encodeURIComponent(address + ', Djibouti');
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}&limit=1&countrycodes=dj`)
            .then(response => response.json())
            .then(data => {
                if (data && data.length > 0) {
                    const result = data[0];
                    const lat = parseFloat(result.lat);
                    const lng = parseFloat(result.lon);
                    
                    // Supprimer l'ancien marqueur
                    if (marker) {
                        map.removeLayer(marker);
                    }
                    
                    // Ajouter nouveau marqueur et centrer la carte
                    marker = L.marker([lat, lng]).addTo(map);
                    globalPoiMarker = marker;
                    map.setView([lat, lng], 15);
                    
                    // Mettre à jour les coordonnées
                    @this.latitude = lat;
                    @this.longitude = lng;
                    
                    showPoiToast('Adresse trouvée: ' + result.display_name);
                    searchInput.value = ''; // Vider le champ de recherche
                } else {
                    showPoiToast('Adresse non trouvée. Essayez une adresse plus précise.', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur de géocodage:', error);
                showPoiToast('Erreur lors de la recherche d\'adresse.', 'error');
            })
            .finally(() => {
                // Réactiver le bouton
                searchButton.disabled = false;
                searchButton.innerHTML = '<i class="fas fa-search"></i> Rechercher';
            });
    }
    
    function showPoiToast(message, type = 'success') {
        // Créer une notification temporaire
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : 'success'} position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="fas fa-${type === 'error' ? 'exclamation-triangle' : 'check-circle'}"></i> 
            ${message}
            <button type="button" class="btn-close ms-2" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(toast);
        
        // Auto-suppression après 4 secondes
        setTimeout(() => {
            if (toast.parentElement) {
                toast.remove();
            }
        }, 4000);
    }
}

// Écouter les changements et événements pour maintenir la carte
document.addEventListener('livewire:init', () => {
    // Changements de locale - ne plus détruire la carte, juste la redimensionner
    Livewire.on('locale-changed', () => {
        if (globalPoiMap) {
            setTimeout(() => {
                globalPoiMap.invalidateSize();
            }, 100);
        }
    });
    
    // Écouteur pour les modals Bootstrap
    document.addEventListener('shown.bs.modal', function (e) {
        if (globalPoiMap) {
            setTimeout(() => {
                globalPoiMap.invalidateSize();
            }, 200);
        }
    });
    
    document.addEventListener('hidden.bs.modal', function (e) {
        if (globalPoiMap) {
            setTimeout(() => {
                globalPoiMap.invalidateSize();
            }, 200);
        }
    });
    
    // Observer les changements de visibilité du conteneur de carte
    const observer = new MutationObserver(() => {
        const mapElement = document.getElementById('poiLocationMap');
        if (globalPoiMap && mapElement && mapElement.offsetParent !== null) {
            setTimeout(() => {
                globalPoiMap.invalidateSize();
            }, 100);
        }
    });
    
    // Observer les changements dans le document
    observer.observe(document.body, { 
        childList: true, 
        subtree: true, 
        attributes: true, 
        attributeFilter: ['style', 'class'] 
    });
});

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
@endpush
</div>