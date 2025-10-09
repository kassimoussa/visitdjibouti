<div>
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    {{ $isEditing ? 'Modifier le tour' : 'Ajouter un nouveau tour' }}
                </h5>
            </div>
            <div class="card-body">
                <!-- Sélecteur de langue -->
                <div class="mb-4">
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="fr-tab-btn" data-bs-toggle="tab" data-bs-target="#fr-tab" type="button" role="tab">
                                Français
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="en-tab-btn" data-bs-toggle="tab" data-bs-target="#en-tab" type="button" role="tab">
                                English
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="ar-tab-btn" data-bs-toggle="tab" data-bs-target="#ar-tab" type="button" role="tab">
                                العربية
                            </button>
                        </li>
                    </ul>
                    <div class="form-text mt-2">
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
                                    <h6 class="mb-0">Informations générales</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="tour_operator_id" class="form-label">Opérateur de tour <span class="text-danger">*</span></label>
                                            <select wire:model="tour_operator_id" class="form-select @error('tour_operator_id') is-invalid @enderror" required>
                                                <option value="">Sélectionner un opérateur</option>
                                                @foreach($tourOperators as $operator)
                                                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('tour_operator_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="type" class="form-label">Type de tour <span class="text-danger">*</span></label>
                                            <select wire:model.live="type" class="form-select @error('type') is-invalid @enderror" required>
                                                <option value="poi">Visite POI</option>
                                                <option value="event">Événement</option>
                                                <option value="mixed">Circuit mixte</option>
                                                <option value="cultural">Culturel</option>
                                                <option value="adventure">Aventure</option>
                                                <option value="nature">Nature</option>
                                                <option value="gastronomic">Gastronomique</option>
                                            </select>
                                            @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Traductions -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Contenu multilingue</h6>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content border-0 p-0">
                                        @foreach(['fr', 'en', 'ar'] as $locale)
                                        <div class="tab-pane fade @if($locale === 'fr') show active @endif" id="{{ $locale }}-tab" role="tabpanel">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label">
                                                        Titre @if($locale === 'fr') <span class="text-danger">*</span> @endif
                                                    </label>
                                                    <input wire:model="translations.{{ $locale }}.title"
                                                           type="text"
                                                           class="form-control @error('translations.'.$locale.'.title') is-invalid @enderror"
                                                           @if($locale === 'fr') required @endif>
                                                    @error('translations.'.$locale.'.title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">
                                                        Description @if($locale === 'fr') <span class="text-danger">*</span> @endif
                                                    </label>
                                                    <textarea wire:model="translations.{{ $locale }}.description"
                                                              class="form-control @error('translations.'.$locale.'.description') is-invalid @enderror"
                                                              rows="4" @if($locale === 'fr') required @endif></textarea>
                                                    @error('translations.'.$locale.'.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label">Itinéraire détaillé</label>
                                                    <textarea wire:model="translations.{{ $locale }}.itinerary"
                                                              class="form-control" rows="4"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">Points forts (un par ligne)</label>
                                                    <textarea wire:model="translations.{{ $locale }}.highlights"
                                                              class="form-control" rows="3"
                                                              placeholder="Point fort 1&#10;Point fort 2&#10;Point fort 3"></textarea>
                                                </div>

                                                <div class="col-md-6">
                                                    <label class="form-label">À apporter (un par ligne)</label>
                                                    <textarea wire:model="translations.{{ $locale }}.what_to_bring"
                                                              class="form-control" rows="3"
                                                              placeholder="Chaussures de marche&#10;Bouteille d'eau&#10;Protection solaire"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Point de rendez-vous -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Point de rendez-vous</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Carte interactive pour géolocalisation -->
                                    <div class="mb-3" wire:ignore>
                                        <label class="form-label">Localisation sur la carte</label>
                                        <div class="border rounded" style="height: 300px; width: 100%; position: relative;">
                                            <div id="tourMeetingPointMap" style="height: 300px; width: 100%; min-height: 300px; min-width: 200px;"></div>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i>
                                            Cliquez sur la carte pour définir le point de rendez-vous
                                        </div>
                                        @error('meeting_point_latitude')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                        @error('meeting_point_longitude')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Recherche d'adresse -->
                                    <div class="mb-3">
                                        <label for="tourAddressSearch" class="form-label">Rechercher une adresse</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="tourAddressSearch"
                                                   placeholder="Tapez une adresse pour localiser le point de rendez-vous">
                                            <button type="button" class="btn btn-outline-primary" id="tourSearchButton">
                                                <i class="fas fa-search"></i> Rechercher
                                            </button>
                                        </div>
                                        <div class="form-text">Exemple: "Hôtel Sheraton, Djibouti"</div>
                                    </div>

                                    <!-- Adresse -->
                                    <div class="mb-3">
                                        <label for="meeting_point_address" class="form-label">Adresse du point de rendez-vous</label>
                                        <input wire:model="meeting_point_address" type="text" class="form-control"
                                               id="meeting_point_address"
                                               placeholder="Adresse du point de rendez-vous">
                                    </div>

                                    <!-- Coordonnées (cachées mais toujours accessibles) -->
                                </div>
                            </div>
                        </div>

                        <!-- Colonne droite -->
                        <div class="col-md-4">
                            <!-- Paramètres -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Paramètres</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="difficulty_level" class="form-label">Difficulté</label>
                                            <select wire:model="difficulty_level" class="form-select">
                                                <option value="easy">Facile</option>
                                                <option value="moderate">Modéré</option>
                                                <option value="difficult">Difficile</option>
                                                <option value="expert">Expert</option>
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label for="price" class="form-label">Prix</label>
                                            <div class="input-group">
                                                <input wire:model="price" type="number" step="0.01" min="0"
                                                       class="form-control" value="0">
                                                <span class="input-group-text">DJF</span>
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="min_participants" class="form-label">Min participants</label>
                                            <input wire:model="min_participants" type="number" min="1"
                                                   class="form-control" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="max_participants" class="form-label">Max participants</label>
                                            <input wire:model="max_participants" type="number" min="1"
                                                   class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="start_date" class="form-label">Date de début <span class="text-danger">*</span></label>
                                            <input wire:model="start_date" type="date"
                                                   class="form-control @error('start_date') is-invalid @enderror"
                                                   id="start_date">
                                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>

                                        <div class="col-md-6">
                                            <label for="end_date" class="form-label">Date de fin</label>
                                            <input wire:model="end_date" type="date"
                                                   class="form-control @error('end_date') is-invalid @enderror"
                                                   id="end_date">
                                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            <div class="form-text">Laissez vide si c'est un tour d'une journée</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="duration_hours" class="form-label">Durée (heures)</label>
                                            <input wire:model="duration_hours" type="number" min="1"
                                                   class="form-control">
                                            <div class="form-text">Durée approximative du tour en heures</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="age_restriction_min" class="form-label">Âge min</label>
                                            <input wire:model="age_restriction_min" type="number" min="0"
                                                   class="form-control">
                                        </div>

                                        <div class="col-md-6">
                                            <label for="age_restriction_max" class="form-label">Âge max</label>
                                            <input wire:model="age_restriction_max" type="number" min="0"
                                                   class="form-control">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Options -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Options</h6>
                                </div>
                                <div class="card-body">
                                    <div class="form-check mb-2">
                                        <input wire:model="is_featured" type="checkbox" class="form-check-input" id="is_featured">
                                        <label class="form-check-label" for="is_featured">Mis en avant</label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input wire:model="weather_dependent" type="checkbox" class="form-check-input" id="weather_dependent">
                                        <label class="form-check-label" for="weather_dependent">Dépendant de la météo</label>
                                    </div>
                                    <div class="form-check mb-3">
                                        <input wire:model="is_recurring" type="checkbox" class="form-check-input" id="is_recurring">
                                        <label class="form-check-label" for="is_recurring">Tour récurrent</label>
                                    </div>

                                    <div>
                                        <label for="status" class="form-label">Statut</label>
                                        <select wire:model="status" class="form-select">
                                            <option value="active">Actif</option>
                                            <option value="suspended">Suspendu</option>
                                            <option value="archived">Archivé</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Galerie d'images -->
                            <div class="card mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">
                                        <i class="fas fa-images me-2"></i>Galerie d'images
                                        <span class="badge bg-secondary ms-2">
                                            {{ count($media_ids) }} image(s)
                                        </span>
                                    </h6>
                                    <button type="button" class="btn btn-sm btn-success" wire:click="openGallerySelector">
                                        <i class="fas fa-plus me-1"></i>Ajouter des images
                                    </button>
                                </div>
                                <div class="card-body">
                                    @if (count($media_ids) > 0)
                                        <div class="mb-3">
                                            <div class="alert alert-info alert-sm">
                                                <i class="fas fa-info-circle me-1"></i>
                                                Glissez-déposez les images pour les réorganiser
                                            </div>
                                            <div id="gallery-images" class="row g-3">
                                                @foreach ($media_ids as $index => $mediaId)
                                                    @php
                                                        $mediaItem = \App\Models\Media::find($mediaId);
                                                    @endphp
                                                    @if ($mediaItem)
                                                        <div class="col-4 col-md-3 col-lg-2" data-media-id="{{ $mediaId }}">
                                                            <div class="gallery-item position-relative">
                                                                <div class="image-container" style="cursor: grab;">
                                                                    <img src="{{ $mediaItem->getImageUrl() }}"
                                                                        alt="{{ $mediaItem->original_name }}"
                                                                        class="img-fluid rounded border shadow-sm"
                                                                        style="height: 120px; width: 100%; object-fit: cover;">
                                                                    <div class="image-overlay">
                                                                        <div class="overlay-actions">
                                                                            <button type="button" class="btn btn-sm btn-light btn-icon"
                                                                                    title="Définir comme image principale"
                                                                                    wire:click="selectFeaturedImage({{ $mediaId }})">
                                                                                <i class="fas fa-star {{ $featured_image_id == $mediaId ? 'text-warning' : 'text-muted' }}"></i>
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
                                                                        {{ $mediaItem->original_name }}
                                                                    </small>
                                                                    <span class="badge badge-sm bg-primary">{{ $index + 1 }}</span>
                                                                    @if($featured_image_id == $mediaId)
                                                                        <span class="badge badge-sm bg-warning">Principal</span>
                                                                    @endif
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

                            <!-- Actions -->
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-1"></i>
                                            {{ $isEditing ? 'Mettre à jour' : 'Créer le tour' }}
                                        </button>
                                        <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">
                                            <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal sélecteur de galerie -->
    <!-- Modal de sélection de médias -->
    @livewire('admin.media-selector-modal')

    <style>
        .gallery-item .image-container {
            position: relative;
            overflow: hidden;
        }

        .gallery-item .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .gallery-item:hover .image-overlay {
            opacity: 1;
        }

        .gallery-item .overlay-actions {
            display: flex;
            gap: 8px;
        }

        .gallery-item .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .gallery-item .drag-handle {
            position: absolute;
            top: 8px;
            right: 8px;
            color: white;
            cursor: grab;
        }

        .border-dashed {
            border-style: dashed !important;
        }

        .badge-sm {
            font-size: 0.75rem;
        }

        /* Styles pour la carte - alignés sur les POIs */
        #tourMeetingPointMap {
            z-index: 1;
        }
    </style>
</div>

@push('scripts')
<script>
// Variables globales pour la carte du tour
let globalTourMap = null;
let globalTourMarker = null;

function initTourMeetingPointMap() {
    console.log('Tentative d\'initialisation de la carte tour...');

    // Si la carte existe déjà, ne pas la recréer
    if (globalTourMap) {
        console.log('Carte tour existe déjà, redimensionnement...');
        globalTourMap.invalidateSize();
        return;
    }

    // Vérifier que l'élément existe
    const mapElement = document.getElementById('tourMeetingPointMap');
    if (!mapElement) {
        console.log('Élément carte tour non trouvé');
        return;
    }

    console.log('Élément carte trouvé, initialisation...');

    // Vérifier que Leaflet est chargé
    if (typeof L === 'undefined') {
        console.error('Leaflet n\'est pas chargé');
        return;
    }

    // Coordonnées par défaut (centre de Djibouti)
    const defaultLat = @json($meeting_point_latitude) || 11.5721;
    const defaultLng = @json($meeting_point_longitude) || 43.1456;

    console.log('Coordonnées par défaut:', defaultLat, defaultLng);
    console.log('Leaflet version:', L.version);

    // Initialiser la carte
    try {
        globalTourMap = L.map('tourMeetingPointMap').setView([defaultLat, defaultLng], 12);
        console.log('Carte tour initialisée avec succès');
    } catch (error) {
        console.error('Erreur initialisation carte tour:', error);
        return;
    }

    const map = globalTourMap;

    // Ajouter les tuiles OpenStreetMap
    const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    });

    tileLayer.addTo(map);
    console.log('Tuiles ajoutées à la carte tour');

    // Forcer le redimensionnement après initialisation
    setTimeout(() => {
        map.invalidateSize();
        console.log('Carte tour redimensionnée après initialisation');
    }, 100);

    // Redimensionnement supplémentaire après un délai plus long
    setTimeout(() => {
        map.invalidateSize();
        console.log('Redimensionnement final carte tour');
    }, 500);

    // Marqueur pour la position
    let marker = globalTourMarker;

    // Si des coordonnées existent déjà, placer le marqueur
    if (@json($meeting_point_latitude) && @json($meeting_point_longitude)) {
        marker = L.marker([defaultLat, defaultLng]).addTo(map);
        globalTourMarker = marker;
    }

    // Gestionnaire de clic sur la carte
    map.on('click', function(e) {
        // Supprimer l'ancien marqueur s'il existe
        if (marker) {
            map.removeLayer(marker);
        }

        // Ajouter nouveau marqueur
        marker = L.marker(e.latlng).addTo(map);
        globalTourMarker = marker;

        // Mettre à jour les propriétés Livewire sans déclencher de re-render
        @this.meeting_point_latitude = e.latlng.lat;
        @this.meeting_point_longitude = e.latlng.lng;

        // Optionnel: afficher une notification
        showTourToast('Position mise à jour: ' + e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6));
    });

    // Recherche d'adresse
    const searchButton = document.getElementById('tourSearchButton');
    const searchInput = document.getElementById('tourAddressSearch');

    if (searchButton && searchInput) {
        // Recherche au clic
        searchButton.addEventListener('click', function() {
            searchTourAddress();
        });

        // Recherche à l'appui de Entrée
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchTourAddress();
            }
        });
    }

    function searchTourAddress() {
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
                    globalTourMarker = marker;
                    map.setView([lat, lng], 15);

                    // Mettre à jour les coordonnées
                    @this.meeting_point_latitude = lat;
                    @this.meeting_point_longitude = lng;

                    showTourToast('Adresse trouvée: ' + result.display_name);
                    searchInput.value = ''; // Vider le champ de recherche
                } else {
                    showTourToast('Adresse non trouvée. Essayez une adresse plus précise.', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur de géocodage:', error);
                showTourToast('Erreur lors de la recherche d\'adresse.', 'error');
            })
            .finally(() => {
                // Réactiver le bouton
                searchButton.disabled = false;
                searchButton.innerHTML = '<i class="fas fa-search"></i> Rechercher';
            });
    }

    function showTourToast(message, type = 'success') {
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

// Initialiser la carte dès que le DOM est prêt
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Tour form');
    if (document.getElementById('tourMeetingPointMap')) {
        console.log('Élément carte trouvé, initialisation...');
        initTourMeetingPointMap();
    } else {
        console.log('Élément carte non trouvé au DOM ready');
    }
});

// Alternative : initialisation après navigation Livewire
document.addEventListener('livewire:navigated', function() {
    console.log('Livewire navigated - Tour form');
    if (document.getElementById('tourMeetingPointMap') && !globalTourMap) {
        console.log('Initialisation de la carte après navigation Livewire');
        initTourMeetingPointMap();
    }
});

document.addEventListener('livewire:init', () => {
    // Tab functionality pour les langues
    document.querySelectorAll('[data-bs-toggle="tab"]').forEach(function (tabTrigger) {
        tabTrigger.addEventListener('shown.bs.tab', function (event) {
            // Redimensionner la carte si elle existe déjà lors du changement d'onglet
            if (globalTourMap) {
                setTimeout(() => {
                    globalTourMap.invalidateSize();
                }, 100);
            }
        });
    });

    // Changements de locale - redimensionner la carte
    Livewire.on('locale-changed', () => {
        if (globalTourMap) {
            setTimeout(() => {
                globalTourMap.invalidateSize();
            }, 100);
        }
    });

    // Observer les changements de visibilité du conteneur de carte
    const observer = new MutationObserver(() => {
        const mapElement = document.getElementById('tourMeetingPointMap');
        if (globalTourMap && mapElement && mapElement.offsetParent !== null) {
            setTimeout(() => {
                globalTourMap.invalidateSize();
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
</script>
@endpush