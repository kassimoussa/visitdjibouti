<div>
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    {{ $isEditMode ? 'Modifier l\'événement' : 'Ajouter un nouvel événement' }}
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
                                    <!-- Titre -->
                                    <div class="mb-3">
                                        <label for="title_{{ $activeLocale }}" class="form-label">
                                            Titre de l'événement
                                            @if($activeLocale === 'fr')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </label>
                                        <input type="text" 
                                            class="form-control @error('translations.'.$activeLocale.'.title') is-invalid @enderror"
                                            id="title_{{ $activeLocale }}" 
                                            wire:model="translations.{{ $activeLocale }}.title" 
                                            @if($activeLocale === 'fr') required @endif>
                                        @error('translations.'.$activeLocale.'.title')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Slug (commun à toutes les langues) -->
                                    @if($activeLocale === 'fr')
                                    <div class="mb-3">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" class="form-control @error('slug') is-invalid @enderror"
                                            id="slug" wire:model="slug">
                                        <div class="form-text">Laissez vide pour générer automatiquement à partir du titre en français.</div>
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
                                        <div class="form-text">Résumé bref qui apparaîtra dans les listes et les résultats de recherche.</div>
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

                            <!-- Dates et horaires -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Dates et horaires</h6>
                                </div>
                                <div class="card-body">
                                    @if($activeLocale === 'fr')
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                                                <input type="date" 
                                                    class="form-control @error('start_date') is-invalid @enderror"
                                                    id="start_date" 
                                                    wire:model="start_date" 
                                                    required>
                                                @error('start_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_date" class="form-label">Date de fin <span class="text-danger">*</span></label>
                                                <input type="date" 
                                                    class="form-control @error('end_date') is-invalid @enderror"
                                                    id="end_date" 
                                                    wire:model="end_date" 
                                                    required>
                                                @error('end_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="start_time" class="form-label">Heure de début</label>
                                                <input type="time" 
                                                    class="form-control @error('start_time') is-invalid @enderror"
                                                    id="start_time" 
                                                    wire:model="start_time">
                                                @error('start_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="end_time" class="form-label">Heure de fin</label>
                                                <input type="time" 
                                                    class="form-control @error('end_time') is-invalid @enderror"
                                                    id="end_time" 
                                                    wire:model="end_time">
                                                @error('end_time')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Localisation -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Localisation</h6>
                                </div>
                                <div class="card-body">
                                    @if($activeLocale === 'fr')
                                    <!-- Lieu général (commun à toutes les langues) -->
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Lieu</label>
                                        <input type="text" 
                                            class="form-control @error('location') is-invalid @enderror"
                                            id="location" 
                                            wire:model="location"
                                            placeholder="Ex: Palais du Peuple, Djibouti">
                                        @error('location')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Carte interactive pour géolocalisation -->
                                    <div class="mb-3">
                                        <label class="form-label">Localisation sur la carte</label>
                                        <div class="border rounded" style="height: 300px; width: 100%; position: relative;">
                                            <div id="eventLocationMap" style="height: 300px; width: 100%; min-height: 300px; min-width: 200px;"></div>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 
                                            Cliquez sur la carte pour définir l'emplacement de l'événement
                                        </div>
                                        @error('latitude') 
                                            <div class="text-danger small">{{ $message }}</div> 
                                        @enderror
                                        @error('longitude') 
                                            <div class="text-danger small">{{ $message }}</div> 
                                        @enderror
                                    </div>

                                    <!-- Recherche d'adresse -->
                                    <div class="mb-3">
                                        <label for="eventAddressSearch" class="form-label">Rechercher une adresse</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="eventAddressSearch" 
                                                   placeholder="Tapez une adresse pour la localiser sur la carte">
                                            <button type="button" class="btn btn-outline-primary" id="eventSearchButton">
                                                <i class="fas fa-search"></i> Rechercher
                                            </button>
                                        </div>
                                        <div class="form-text">Exemple: "Palais du Peuple, Djibouti"</div>
                                    </div>
                                    @endif

                                    <!-- Détails du lieu (spécifique à chaque langue) -->
                                    <div class="mb-3">
                                        <label for="location_details_{{ $activeLocale }}" class="form-label">Détails du lieu</label>
                                        <input type="text"
                                            class="form-control @error('translations.'.$activeLocale.'.location_details') is-invalid @enderror"
                                            id="location_details_{{ $activeLocale }}" 
                                            wire:model="translations.{{ $activeLocale }}.location_details"
                                            placeholder="Ex: Salle de conférence, 2ème étage">
                                        @error('translations.'.$activeLocale.'.location_details')
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
                                    <!-- Programme (spécifique à chaque langue) -->
                                    <div class="mb-3">
                                        <label for="program_{{ $activeLocale }}" class="form-label">Programme</label>
                                        <textarea 
                                            class="form-control @error('translations.'.$activeLocale.'.program') is-invalid @enderror" 
                                            id="program_{{ $activeLocale }}"
                                            wire:model="translations.{{ $activeLocale }}.program" 
                                            rows="4"
                                            placeholder="Détaillez le déroulement de l'événement"></textarea>
                                        @error('translations.'.$activeLocale.'.program')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Prérequis (spécifique à chaque langue) -->
                                    <div class="mb-3">
                                        <label for="requirements_{{ $activeLocale }}" class="form-label">Prérequis</label>
                                        <textarea 
                                            class="form-control @error('translations.'.$activeLocale.'.requirements') is-invalid @enderror" 
                                            id="requirements_{{ $activeLocale }}"
                                            wire:model="translations.{{ $activeLocale }}.requirements" 
                                            rows="3"
                                            placeholder="Équipement nécessaire, niveau requis, etc."></textarea>
                                        @error('translations.'.$activeLocale.'.requirements')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Informations additionnelles (spécifique à chaque langue) -->
                                    <div class="mb-3">
                                        <label for="additional_info_{{ $activeLocale }}" class="form-label">Informations additionnelles</label>
                                        <textarea 
                                            class="form-control @error('translations.'.$activeLocale.'.additional_info') is-invalid @enderror" 
                                            id="additional_info_{{ $activeLocale }}" 
                                            wire:model="translations.{{ $activeLocale }}.additional_info" 
                                            rows="3"
                                            placeholder="Autres informations utiles"></textarea>
                                        @error('translations.'.$activeLocale.'.additional_info')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Colonne droite -->
                        <div class="col-md-4">
                            <!-- Options de publication -->
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

                                    <!-- Organisateur -->
                                    <div class="mb-3">
                                        <label for="organizer" class="form-label">Organisateur</label>
                                        <input type="text" 
                                            class="form-control @error('organizer') is-invalid @enderror"
                                            id="organizer" 
                                            wire:model="organizer"
                                            placeholder="Nom de l'organisateur">
                                        @error('organizer')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Options -->
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="is_featured"
                                                wire:model="is_featured">
                                            <label class="form-check-label" for="is_featured">Mettre en avant sur la page d'accueil</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tarification et participants -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Tarification et participants</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Prix -->
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Prix (DJF)</label>
                                        <input type="number" step="0.01" min="0"
                                            class="form-control @error('price') is-invalid @enderror"
                                            id="price" wire:model="price"
                                            placeholder="0 pour gratuit">
                                        <div class="form-text">Laissez vide ou 0 pour un événement gratuit</div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nombre maximum de participants -->
                                    <div class="mb-3">
                                        <label for="max_participants" class="form-label">Nombre maximum de participants</label>
                                        <input type="number" min="1"
                                            class="form-control @error('max_participants') is-invalid @enderror"
                                            id="max_participants" wire:model="max_participants"
                                            placeholder="Laissez vide pour illimité">
                                        @error('max_participants')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if($isEditMode)
                                    <!-- Participants actuels (en mode édition) -->
                                    <div class="mb-3">
                                        <label for="current_participants" class="form-label">Participants actuels</label>
                                        <input type="number" min="0"
                                            class="form-control @error('current_participants') is-invalid @enderror"
                                            id="current_participants" wire:model="current_participants">
                                        @error('current_participants')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Contact et liens -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Contact et liens</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Email de contact -->
                                    <div class="mb-3">
                                        <label for="contact_email" class="form-label">Email de contact</label>
                                        <input type="email"
                                            class="form-control @error('contact_email') is-invalid @enderror"
                                            id="contact_email" wire:model="contact_email">
                                        @error('contact_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Téléphone de contact -->
                                    <div class="mb-3">
                                        <label for="contact_phone" class="form-label">Téléphone de contact</label>
                                        <input type="tel"
                                            class="form-control @error('contact_phone') is-invalid @enderror"
                                            id="contact_phone" wire:model="contact_phone">
                                        @error('contact_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Site web -->
                                    <div class="mb-3">
                                        <label for="website_url" class="form-label">Site web</label>
                                        <input type="url"
                                            class="form-control @error('website_url') is-invalid @enderror"
                                            id="website_url" wire:model="website_url">
                                        @error('website_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Lien de billetterie -->
                                    <div class="mb-3">
                                        <label for="ticket_url" class="form-label">Lien de billetterie</label>
                                        <input type="url"
                                            class="form-control @error('ticket_url') is-invalid @enderror"
                                            id="ticket_url" wire:model="ticket_url">
                                        @error('ticket_url')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
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
                        </div>
                    </div>

                    <!-- Boutons d'action -->
                    <div class="d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('events.index') }}" class="btn btn-secondary">Annuler</a>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la carte seulement si on est en français (où les coordonnées sont visibles)
    if (document.getElementById('eventLocationMap')) {
        initEventLocationMap();
    }
});

// Variables globales pour garder la référence de la carte
let globalEventMap = null;
let globalEventMarker = null;

function initEventLocationMap() {
    // Si la carte existe déjà, ne pas la recréer
    if (globalEventMap) {
        return;
    }

    // Coordonnées par défaut (centre de Djibouti)
    const defaultLat = @json($latitude) || 11.5721;
    const defaultLng = @json($longitude) || 43.1456;
    
    // Initialiser la carte
    globalEventMap = L.map('eventLocationMap').setView([defaultLat, defaultLng], 12);
    const map = globalEventMap;
    
    // Ajouter les tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Marqueur pour la position
    let marker = globalEventMarker;
    
    // Si des coordonnées existent déjà, placer le marqueur
    if (@json($latitude) && @json($longitude)) {
        marker = L.marker([defaultLat, defaultLng]).addTo(map);
        globalEventMarker = marker;
    }
    
    // Gestionnaire de clic sur la carte
    map.on('click', function(e) {
        // Supprimer l'ancien marqueur s'il existe
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Ajouter nouveau marqueur
        marker = L.marker(e.latlng).addTo(map);
        globalEventMarker = marker;
        
        // Mettre à jour les propriétés Livewire sans déclencher de re-render
        @this.latitude = e.latlng.lat;
        @this.longitude = e.latlng.lng;
        
        // Optionnel: afficher une notification
        showEventToast('Position mise à jour: ' + e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6));
    });
    
    // Recherche d'adresse
    const searchButton = document.getElementById('eventSearchButton');
    const searchInput = document.getElementById('eventAddressSearch');
    
    if (searchButton && searchInput) {
        // Recherche au clic
        searchButton.addEventListener('click', function() {
            searchEventAddress();
        });
        
        // Recherche à l'appui de Entrée
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchEventAddress();
            }
        });
    }
    
    function searchEventAddress() {
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
                    globalEventMarker = marker;
                    map.setView([lat, lng], 15);
                    
                    // Mettre à jour les coordonnées
                    @this.latitude = lat;
                    @this.longitude = lng;
                    
                    showEventToast('Adresse trouvée: ' + result.display_name);
                    searchInput.value = ''; // Vider le champ de recherche
                } else {
                    showEventToast('Adresse non trouvée. Essayez une adresse plus précise.', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur de géocodage:', error);
                showEventToast('Erreur lors de la recherche d\'adresse.', 'error');
            })
            .finally(() => {
                // Réactiver le bouton
                searchButton.disabled = false;
                searchButton.innerHTML = '<i class="fas fa-search"></i> Rechercher';
            });
    }
    
    function showEventToast(message, type = 'success') {
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

// Écouter les changements de locale pour réinitialiser la carte si nécessaire
document.addEventListener('livewire:init', () => {
    Livewire.on('locale-changed', () => {
        // Réinitialiser les variables globales pour forcer la recréation
        if (globalEventMap) {
            globalEventMap.remove();
            globalEventMap = null;
            globalEventMarker = null;
        }
        
        // Petite pause pour laisser le DOM se mettre à jour
        setTimeout(() => {
            if (document.getElementById('eventLocationMap')) {
                initEventLocationMap();
            }
        }, 100);
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