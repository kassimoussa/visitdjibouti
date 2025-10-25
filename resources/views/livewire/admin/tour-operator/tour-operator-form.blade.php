<div>
    <div class="container-fluid">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        @if($isOperatorMode)
                            Modifier mon entreprise
                        @else
                            {{ $isEditing ? 'Modifier l\'opérateur de tour' : 'Ajouter un nouvel opérateur de tour' }}
                        @endif
                    </h5>
                    @if(!$isOperatorMode)
                        <a href="{{ route('tour-operators.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Retour à la liste
                        </a>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <!-- Messages de succès/erreur -->
                @if (session()->has('message'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('message') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

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

                <form wire:submit="save">
                    <div class="row">
                        <!-- Colonne gauche - Informations traduites -->
                        <div class="col-md-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations de base - {{ strtoupper($activeLocale) }}</h6>
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

                                    <!-- Description -->
                                    <div class="mb-3">
                                        <label for="description_{{ $activeLocale }}" class="form-label">Description</label>
                                        <textarea class="form-control @error('translations.'.$activeLocale.'.description') is-invalid @enderror" 
                                                  id="description_{{ $activeLocale }}" 
                                                  wire:model="translations.{{ $activeLocale }}.description" 
                                                  rows="4"></textarea>
                                        @error('translations.'.$activeLocale.'.description')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Adresse traduite (seulement pour les autres langues) -->
                                    @if($activeLocale !== 'fr')
                                    <div class="mb-3">
                                        <label for="address_translated_{{ $activeLocale }}" class="form-label">Adresse traduite</label>
                                        <textarea class="form-control @error('translations.'.$activeLocale.'.address_translated') is-invalid @enderror" 
                                                  id="address_translated_{{ $activeLocale }}" 
                                                  wire:model="translations.{{ $activeLocale }}.address_translated" rows="3"></textarea>
                                        @error('translations.'.$activeLocale.'.address_translated')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-text">Traduction de l'adresse pour cette langue</div>
                                    </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Informations techniques (uniquement en français) -->
                            @if($activeLocale === 'fr')
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Informations de contact et localisation</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Contact -->
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phones" class="form-label">Téléphones</label>
                                                <input type="text" class="form-control @error('phones') is-invalid @enderror" 
                                                       id="phones" wire:model="phones" 
                                                       placeholder="+253 21 12 34 56|+253 77 12 34 56">
                                                @error('phones') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                <div class="form-text">Séparez plusieurs numéros par le caractère |</div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="emails" class="form-label">Emails</label>
                                                <input type="text" class="form-control @error('emails') is-invalid @enderror" 
                                                       id="emails" wire:model="emails" 
                                                       placeholder="contact@exemple.com|info@exemple.com">
                                                @error('emails') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                                <div class="form-text">Séparez plusieurs emails par le caractère |</div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="website" class="form-label">Site web</label>
                                        <input type="text" class="form-control @error('website') is-invalid @enderror" 
                                               id="website" wire:model="website" 
                                               placeholder="www.exemple.com">
                                        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Adresse et géolocalisation -->
                                    <div class="mb-3">
                                        <label for="address" class="form-label">Adresse</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" wire:model="address" rows="3"></textarea>
                                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Carte interactive pour géolocalisation -->
                                    <div class="mb-3">
                                        <label class="form-label">Localisation sur la carte</label>
                                        <div class="border rounded" style="height: 300px; position: relative;">
                                            <div id="locationMap" style="height: 100%; width: 100%;"></div>
                                        </div>
                                        <div class="form-text">
                                            <i class="fas fa-info-circle"></i> 
                                            Cliquez sur la carte pour définir l'emplacement de l'opérateur
                                            @if($latitude && $longitude)
                                                - Coordonnées actuelles : {{ number_format($latitude, 6) }}, {{ number_format($longitude, 6) }}
                                            @endif
                                        </div>
                                        @error('latitude') <div class="text-danger small">{{ $message }}</div> @enderror
                                        @error('longitude') <div class="text-danger small">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Recherche d'adresse -->
                                    <div class="mb-3">
                                        <label for="addressSearch" class="form-label">Rechercher une adresse</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="addressSearch" 
                                                   placeholder="Tapez une adresse pour la localiser sur la carte">
                                            <button type="button" class="btn btn-outline-primary" id="searchButton">
                                                <i class="fas fa-search"></i> Rechercher
                                            </button>
                                        </div>
                                        <div class="form-text">Exemple: "Avenue Hassan Gouled Aptidon, Djibouti"</div>
                                    </div>

                                    <!-- Options (admin seulement) -->
                                    @if(!$isOperatorMode)
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                                                <label class="form-check-label" for="is_active">
                                                    Actif
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="featured" wire:model="featured">
                                                <label class="form-check-label" for="featured">
                                                    Mis en avant
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Colonne droite - Médias (uniquement en français) -->
                        @if($activeLocale === 'fr')
                        <div class="col-md-4">
                            <!-- Logo -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Logo</h6>
                                </div>
                                <div class="card-body">
                                    @if($logo_id)
                                        @php
                                            $logo = \App\Models\Media::find($logo_id);
                                        @endphp
                                        @if($logo)
                                            <div class="text-center mb-3">
                                                <img src="{{ $logo->thumbnail_url ?: $logo->url }}" alt="Logo" 
                                                     class="rounded" style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                            </div>
                                        @endif
                                    @endif
                                    
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary" wire:click="openMediaSelector">
                                            <i class="fas fa-image"></i> {{ $logo_id ? 'Changer' : 'Sélectionner' }} le logo
                                        </button>
                                        @if($logo_id)
                                            <button type="button" class="btn btn-outline-danger" wire:click="$set('logo_id', null)">
                                                <i class="fas fa-times"></i> Supprimer
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Galerie d'images -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0">Galerie d'images</h6>
                                </div>
                                <div class="card-body">
                                    @if(!empty($selectedMedia))
                                        <div class="row">
                                            @foreach($selectedMedia as $mediaId)
                                                @php
                                                    $media = \App\Models\Media::find($mediaId);
                                                @endphp
                                                @if($media)
                                                    <div class="col-6 mb-2">
                                                        <img src="{{ $media->thumbnail_url ?: $media->url }}" 
                                                             alt="Galerie" class="img-fluid rounded" 
                                                             style="width: 100%; height: 80px; object-fit: cover;">
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                        <hr>
                                    @endif
                                    
                                    <div class="d-grid gap-2">
                                        <button type="button" class="btn btn-outline-primary" wire:click="openGallerySelector">
                                            <i class="fas fa-images"></i> {{ !empty($selectedMedia) ? 'Modifier' : 'Sélectionner' }} la galerie
                                        </button>
                                        @if(!empty($selectedMedia))
                                            <button type="button" class="btn btn-outline-danger" wire:click="$set('selectedMedia', [])">
                                                <i class="fas fa-times"></i> Vider la galerie
                                            </button>
                                        @endif
                                    </div>
                                    <div class="form-text mt-2">
                                        {{ count($selectedMedia) }} image(s) sélectionnée(s)
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Boutons de sauvegarde -->
                    <div class="d-flex justify-content-between mt-4">
                        <a href="{{ route('tour-operators.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> {{ $isEditing ? 'Mettre à jour' : 'Créer' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Composant de sélection de média -->
    <livewire:admin.media-selector-modal />
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser la carte seulement si on est en français (où les coordonnées sont visibles)
    if (document.getElementById('locationMap')) {
        initLocationMap();
    }
});

function initLocationMap() {
    // Si la carte existe déjà, ne pas la recréer
    if (globalMap) {
        return;
    }

    // Coordonnées par défaut (centre de Djibouti)
    const defaultLat = @json($latitude) || 11.5721;
    const defaultLng = @json($longitude) || 43.1456;
    
    // Initialiser la carte
    globalMap = L.map('locationMap').setView([defaultLat, defaultLng], 12);
    const map = globalMap;
    
    // Ajouter les tuiles OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Marqueur pour la position
    let marker = globalMarker;
    
    // Si des coordonnées existent déjà, placer le marqueur
    if (@json($latitude) && @json($longitude)) {
        marker = L.marker([defaultLat, defaultLng]).addTo(map);
        globalMarker = marker;
    }
    
    // Gestionnaire de clic sur la carte
    map.on('click', function(e) {
        // Supprimer l'ancien marqueur s'il existe
        if (marker) {
            map.removeLayer(marker);
        }
        
        // Ajouter nouveau marqueur
        marker = L.marker(e.latlng).addTo(map);
        globalMarker = marker;
        
        // Mettre à jour les propriétés Livewire sans déclencher de re-render
        @this.latitude = e.latlng.lat;
        @this.longitude = e.latlng.lng;
        
        // Optionnel: afficher une notification
        showToast('Position mise à jour: ' + e.latlng.lat.toFixed(6) + ', ' + e.latlng.lng.toFixed(6));
    });
    
    // Recherche d'adresse
    const searchButton = document.getElementById('searchButton');
    const searchInput = document.getElementById('addressSearch');
    
    if (searchButton && searchInput) {
        // Recherche au clic
        searchButton.addEventListener('click', function() {
            searchAddress();
        });
        
        // Recherche à l'appui de Entrée
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchAddress();
            }
        });
    }
    
    function searchAddress() {
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
                    globalMarker = marker;
                    map.setView([lat, lng], 15);
                    
                    // Mettre à jour les coordonnées
                    @this.latitude = lat;
                    @this.longitude = lng;
                    
                    showToast('Adresse trouvée: ' + result.display_name);
                    searchInput.value = ''; // Vider le champ de recherche
                } else {
                    showToast('Adresse non trouvée. Essayez une adresse plus précise.', 'error');
                }
            })
            .catch(error => {
                console.error('Erreur de géocodage:', error);
                showToast('Erreur lors de la recherche d\'adresse.', 'error');
            })
            .finally(() => {
                // Réactiver le bouton
                searchButton.disabled = false;
                searchButton.innerHTML = '<i class="fas fa-search"></i> Rechercher';
            });
    }
    
    function showToast(message, type = 'success') {
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

// Variable globale pour garder la référence de la carte
let globalMap = null;
let globalMarker = null;

// Écouter les changements de locale pour réinitialiser la carte si nécessaire
document.addEventListener('livewire:init', () => {
    Livewire.on('locale-changed', () => {
        // Réinitialiser les variables globales pour forcer la recréation
        if (globalMap) {
            globalMap.remove();
            globalMap = null;
            globalMarker = null;
        }
        
        // Petite pause pour laisser le DOM se mettre à jour
        setTimeout(() => {
            if (document.getElementById('locationMap')) {
                initLocationMap();
            }
        }, 100);
    });
});
</script>
@endpush