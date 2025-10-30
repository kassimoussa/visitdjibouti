<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-8">
                <!-- Informations Générales -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-language me-2"></i>Informations Générales
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Onglets pour les langues -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-activity-fr" role="tab">
                                    FR
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-activity-en" role="tab">
                                    EN
                                </a>
                            </li>
                        </ul>

                        <!-- Contenu des onglets -->
                        <div class="tab-content">
                            <!-- Français -->
                            <div class="tab-pane fade show active" id="tab-activity-fr" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Titre *</label>
                                    <input type="text" class="form-control @error('translations.fr.title') is-invalid @enderror"
                                           wire:model="translations.fr.title"
                                           placeholder="Titre de l'activité en français">
                                    @error('translations.fr.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description courte</label>
                                    <textarea class="form-control" rows="2"
                                              wire:model="translations.fr.short_description"
                                              placeholder="Résumé en une phrase"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea class="form-control @error('translations.fr.description') is-invalid @enderror"
                                              rows="8"
                                              wire:model="translations.fr.description"
                                              placeholder="Description détaillée de l'activité"></textarea>
                                    @error('translations.fr.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Quoi apporter</label>
                                    <textarea class="form-control" rows="4"
                                              wire:model="translations.fr.what_to_bring"
                                              placeholder="Ce que les participants doivent apporter"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description du point de rendez-vous</label>
                                    <textarea class="form-control" rows="2"
                                              wire:model="translations.fr.meeting_point_description"
                                              placeholder="Instructions pour trouver le point de rendez-vous"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Informations additionnelles</label>
                                    <textarea class="form-control" rows="4"
                                              wire:model="translations.fr.additional_info"
                                              placeholder="Toute autre information importante"></textarea>
                                </div>
                            </div>

                            <!-- English -->
                            <div class="tab-pane fade" id="tab-activity-en" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control"
                                           wire:model="translations.en.title"
                                           placeholder="Activity title in English">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Short Description</label>
                                    <textarea class="form-control" rows="2"
                                              wire:model="translations.en.short_description"
                                              placeholder="Summary in one sentence"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control" rows="8"
                                              wire:model="translations.en.description"
                                              placeholder="Detailed activity description"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">What to bring</label>
                                    <textarea class="form-control" rows="4"
                                              wire:model="translations.en.what_to_bring"
                                              placeholder="What participants need to bring"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Meeting point description</label>
                                    <textarea class="form-control" rows="2"
                                              wire:model="translations.en.meeting_point_description"
                                              placeholder="Instructions to find the meeting point"></textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Additional information</label>
                                    <textarea class="form-control" rows="4"
                                              wire:model="translations.en.additional_info"
                                              placeholder="Any other important information"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Détails de l'activité -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Détails de l'activité</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prix *</label>
                                <div class="input-group">
                                    <input type="number" step="any" class="form-control" wire:model.blur="price">
                                    <span class="input-group-text">{{ $currency }}</span>
                                </div>
                                @error('price') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Durée</label>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input type="number" class="form-control" wire:model="duration_hours" min="0" placeholder="0">
                                            <span class="input-group-text">h</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="input-group">
                                            <input type="number" class="form-control" wire:model="duration_minutes" min="0" max="59" placeholder="0">
                                            <span class="input-group-text">min</span>
                                        </div>
                                    </div>
                                </div>
                                <small class="text-muted">Exemple: 2h 30min</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Niveau de difficulté *</label>
                                <select class="form-select" wire:model="difficulty_level">
                                    <option value="easy">Facile</option>
                                    <option value="moderate">Modéré</option>
                                    <option value="difficult">Difficile</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Région</label>
                                <select class="form-select" wire:model="region">
                                    <option value="">Sélectionner...</option>
                                    <option value="Djibouti">Djibouti</option>
                                    <option value="Ali Sabieh">Ali Sabieh</option>
                                    <option value="Dikhil">Dikhil</option>
                                    <option value="Tadjourah">Tadjourah</option>
                                    <option value="Obock">Obock</option>
                                    <option value="Arta">Arta</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Adresse du lieu</label>
                            <input type="text" class="form-control" wire:model="location_address">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control" wire:model="latitude">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control" wire:model="longitude">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Participants et restrictions -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Participants et restrictions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Participants min. *</label>
                                <input type="number" class="form-control" wire:model="min_participants" min="1">
                                @error('min_participants') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Participants max.</label>
                                <input type="number" class="form-control" wire:model="max_participants" min="1">
                                <small class="text-muted">Laisser vide pour illimité</small>
                            </div>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="has_age_restrictions" wire:model="has_age_restrictions">
                            <label class="form-check-label" for="has_age_restrictions">Restrictions d'âge</label>
                        </div>

                        @if($has_age_restrictions)
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Âge minimum</label>
                                <input type="number" class="form-control" wire:model="min_age" min="1">
                                @error('min_age') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Âge maximum</label>
                                <input type="number" class="form-control" wire:model="max_age" min="1">
                                @error('max_age') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Politique d'annulation</label>
                            <textarea class="form-control" rows="3" wire:model="cancellation_policy"
                                      placeholder="Conditions d'annulation et de remboursement"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Prérequis physiques -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Prérequis physiques</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ajouter un prérequis</label>
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model="newPhysicalRequirement"
                                       placeholder="Ex: Bonne condition physique">
                                <button type="button" class="btn btn-primary" wire:click="addPhysicalRequirement">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        @if(count($physical_requirements) > 0)
                        <ul class="list-group">
                            @foreach($physical_requirements as $index => $requirement)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $requirement }}
                                <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="removePhysicalRequirement({{ $index }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                <!-- Certifications requises -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Certifications requises</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ajouter une certification</label>
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model="newCertification"
                                       placeholder="Ex: Certification de plongée niveau 1">
                                <button type="button" class="btn btn-primary" wire:click="addCertification">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        @if(count($certifications_required) > 0)
                        <ul class="list-group">
                            @foreach($certifications_required as $index => $cert)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $cert }}
                                <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="removeCertification({{ $index }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                <!-- Équipement fourni -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Équipement fourni</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ajouter équipement fourni</label>
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model="newEquipmentProvided"
                                       placeholder="Ex: Masque et tuba">
                                <button type="button" class="btn btn-primary" wire:click="addEquipmentProvided">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        @if(count($equipment_provided) > 0)
                        <ul class="list-group">
                            @foreach($equipment_provided as $index => $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item }}
                                <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="removeEquipmentProvided({{ $index }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                <!-- Équipement requis -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Équipement à apporter</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ajouter équipement à apporter</label>
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model="newEquipmentRequired"
                                       placeholder="Ex: Chaussures de randonnée">
                                <button type="button" class="btn btn-primary" wire:click="addEquipmentRequired">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        @if(count($equipment_required) > 0)
                        <ul class="list-group">
                            @foreach($equipment_required as $index => $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item }}
                                <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="removeEquipmentRequired({{ $index }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>

                <!-- Inclus dans le prix -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Inclus dans le prix</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Ajouter ce qui est inclus</label>
                            <div class="input-group">
                                <input type="text" class="form-control" wire:model="newInclude"
                                       placeholder="Ex: Boissons et snacks">
                                <button type="button" class="btn btn-primary" wire:click="addInclude">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        @if(count($includes) > 0)
                        <ul class="list-group">
                            @foreach($includes as $index => $item)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $item }}
                                <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="removeInclude({{ $index }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Options -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" wire:model="is_featured">
                            <label class="form-check-label" for="is_featured">Mettre en avant</label>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="weather_dependent" wire:model="weather_dependent">
                            <label class="form-check-label" for="weather_dependent">Dépendant de la météo</label>
                        </div>
                    </div>
                </div>

                <!-- Image principale -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-image me-2"></i>Image principale
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary"
                            wire:click="openFeaturedImageSelector">
                            <i class="fas fa-images me-1"></i>Choisir
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($featuredImageId && $allMedia)
                            <div class="text-center">
                                @php $featuredImage = collect($allMedia)->firstWhere('id', $featuredImageId); @endphp
                                @if ($featuredImage)
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ asset($featuredImage->thumbnail_path ?? $featuredImage->path) }}"
                                            alt="{{ $featuredImage->original_name }}"
                                            class="img-fluid rounded border shadow-sm"
                                            style="max-height: 200px; max-width: 100%;">
                                        <button type="button"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                            wire:click="$set('featuredImageId', null)">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4 border rounded" style="border: 2px dashed #dee2e6;">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Aucune image</p>
                                <button type="button" class="btn btn-outline-primary"
                                    wire:click="openFeaturedImageSelector">
                                    <i class="fas fa-plus me-1"></i>Sélectionner
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Galerie d'images -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-images me-2"></i>Galerie ({{ count($selectedMedia) }})
                        </h6>
                        <button type="button" class="btn btn-sm btn-success"
                            wire:click="openGallerySelector">
                            <i class="fas fa-plus me-1"></i>Ajouter
                        </button>
                    </div>
                    <div class="card-body">
                        @if (count($selectedMedia) > 0 && $allMedia)
                            <div class="row g-3">
                                @foreach ($selectedMedia as $index => $mediaId)
                                    @php $mediaItem = collect($allMedia)->firstWhere('id', $mediaId); @endphp
                                    @if ($mediaItem)
                                        <div class="col-6">
                                            <div class="position-relative">
                                                <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                    alt="{{ $mediaItem->original_name }}"
                                                    class="img-fluid rounded border shadow-sm"
                                                    style="height: 120px; width: 100%; object-fit: cover;">
                                                <button type="button"
                                                    class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1"
                                                    wire:click="removeMediaFromGallery({{ $index }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 border rounded" style="border: 2px dashed #dee2e6;">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Aucune image</p>
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="openGallerySelector">
                                    <i class="fas fa-plus me-1"></i>Ajouter
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Sauvegarder
            </button>
            <a href="{{ route('operator.activities.index') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Annuler
            </a>
        </div>
    </form>

    <!-- Modal de sélection de médias -->
    @livewire('admin.media-selector-modal')
</div>
