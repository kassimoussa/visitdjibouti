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
                                <div class="card-header">
                                    <h6 class="mb-0">Image principale</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <!-- Affichage de l'image sélectionnée si elle existe -->
                                        @if ($featuredImageId)
                                            <div class="mb-3">
                                                @php $featuredImage = $media->firstWhere('id', $featuredImageId); @endphp
                                                @if ($featuredImage)
                                                    <img src="{{ asset($featuredImage->thumbnail_path ?? $featuredImage->path) }}"
                                                        alt="{{ $featuredImage->getTranslation($activeLocale)->title ?? $featuredImage->original_name }}"
                                                        class="img-fluid rounded border mb-3"
                                                        style="max-height: 150px;">
                                                @endif
                                            </div>
                                        @endif

                                        <!-- Sélecteur d'images avec miniatures -->
                                        <div class="mb-3">
                                            <label for="featuredImageId" class="form-label">Sélectionner une
                                                image</label>
                                            <select class="form-select @error('featuredImageId') is-invalid @enderror"
                                                id="featuredImageId" wire:model.live="featuredImageId">
                                                <option value="">Aucune image</option>
                                                @foreach ($media as $mediaItem)
                                                    <option value="{{ $mediaItem->id }}">
                                                        {{ $mediaItem->getTranslation($activeLocale)->title ?? $mediaItem->original_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('featuredImageId')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Galerie miniature pour sélection rapide -->
                                        <div class="row g-2 mt-3">
                                            <div class="col-12 mb-2">
                                                <label class="form-label">Sélection rapide</label>
                                            </div>
                                            @foreach ($media->take(8) as $mediaItem)
                                                <div class="col-3 col-md-3 mb-2">
                                                    <div class="image-thumbnail-selector"
                                                        wire:click="selectFeaturedImage({{ $mediaItem->id }})"
                                                        style="cursor: pointer; border: 2px solid {{ $featuredImageId == $mediaItem->id ? '#2563eb' : 'transparent' }};">
                                                        <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                            alt="{{ $mediaItem->getTranslation($activeLocale)->alt_text ?? $mediaItem->original_name }}"
                                                            class="img-fluid rounded"
                                                            style="height: 80px; width: 100%; object-fit: cover;">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        <div class="mt-3">
                                            <a href="{{ route('media.index') }}" target="_blank"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-upload me-1"></i> Gérer les médias
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Galerie d'images -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Galerie d'images</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <!-- Images actuellement sélectionnées -->
                                        @if (count($selectedMedia) > 0)
                                            <div class="mb-3">
                                                <label class="form-label">Images sélectionnées</label>
                                                <div class="row g-2">
                                                    @foreach ($selectedMedia as $mediaId)
                                                        @php $mediaItem = $media->firstWhere('id', $mediaId); @endphp
                                                        @if ($mediaItem)
                                                            <div class="col-3 col-md-3 mb-2">
                                                                <div class="position-relative">
                                                                    <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                                        alt="{{ $mediaItem->getTranslation($activeLocale)->alt_text ?? $mediaItem->original_name }}"
                                                                        class="img-fluid rounded border"
                                                                        style="height: 80px; width: 100%; object-fit: cover;">
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-danger position-absolute top-0 end-0"
                                                                        wire:click="removeFromGallery({{ $mediaId }})">
                                                                        <i class="fas fa-times"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        <!-- Galerie pour sélection -->
                                        <div class="mb-3">
                                            <label class="form-label">Ajouter des images à la galerie</label>
                                            <div class="row g-2">
                                                @foreach ($media as $mediaItem)
                                                    @if (!in_array($mediaItem->id, $selectedMedia))
                                                        <div class="col-3 col-md-3 mb-2">
                                                            <div class="image-thumbnail-selector"
                                                                wire:click="addToGallery({{ $mediaItem->id }})"
                                                                style="cursor: pointer; border: 1px solid #e2e8f0;">
                                                                <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                                    alt="{{ $mediaItem->getTranslation($activeLocale)->alt_text ?? $mediaItem->original_name }}"
                                                                    class="img-fluid rounded"
                                                                    style="height: 80px; width: 100%; object-fit: cover;">
                                                            </div>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
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
</div>