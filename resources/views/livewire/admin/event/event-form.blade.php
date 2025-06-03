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

                                    <!-- Coordonnées GPS -->
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

                                        <!-- Sélecteur d'images -->
                                        <div class="mb-3">
                                            <label for="featuredImageId" class="form-label">Sélectionner une image</label>
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
                        <a href="{{ route('events.index') }}" class="btn btn-secondary">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            {{ $isEditMode ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>