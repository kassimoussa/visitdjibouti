<div>
    <div class="container-fluid">
        <!-- En-tête avec bouton d'ajout et basculement de vue -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <h1 class="h3 mb-0 me-3">Gestion des points d'intérêt</h1>
                <div class="btn-group" role="group">
                    <button type="button" class="btn {{ $view === 'list' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="toggleView('list')">
                        <i class="fas fa-list me-1"></i> Liste
                    </button>
                    <button type="button" class="btn {{ $view === 'map' ? 'btn-primary' : 'btn-outline-primary' }}"
                        wire:click="toggleView('map')">
                        <i class="fas fa-map-marker-alt me-1"></i> Carte
                    </button>
                </div>
            </div>
            <a href="{{ route('pois.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Ajouter un nouveau
            </a>
        </div>

        <!-- Filtres -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-2">
                        <label for="search" class="form-label">Recherche</label>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                            id="search" placeholder="Rechercher...">
                    </div>

                    <div class="col-md-2">
                        <label for="parentCategory" class="form-label">Catégorie principale</label>
                        <select wire:model.live="parentCategory" id="parentCategory" class="form-select">
                            <option value="">Toutes les catégories</option>
                            @foreach ($parentCategories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->translation($currentLocale)?->name ?? $category->translation('fr')?->name ?? 'Sans nom' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="subcategory" class="form-label">Sous-catégorie</label>
                        <select wire:model.live="subcategory" id="subcategory" class="form-select" 
                                {{ empty($parentCategory) ? 'disabled' : '' }}>
                            <option value="">Toutes les sous-catégories</option>
                            @foreach ($subcategories as $subcat)
                                <option value="{{ $subcat->id }}">
                                    {{ $subcat->translation($currentLocale)?->name ?? $subcat->translation('fr')?->name ?? 'Sans nom' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="region" class="form-label">Région</label>
                        <select wire:model.live="region" id="region" class="form-select">
                            <option value="">Toutes les régions</option>
                            @foreach ($regions as $regionKey => $regionName)
                                <option value="{{ $regionKey }}">{{ $regionName }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="status" class="form-label">Statut</label>
                        <select wire:model.live="status" id="status" class="form-select">
                            <option value="">Tous les statuts</option>
                            <option value="published">Publié</option>
                            <option value="draft">Brouillon</option>
                            <option value="archived">Archivé</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label for="locale" class="form-label">Langue</label>
                        <select wire:model.live="currentLocale" id="locale" class="form-select">
                            @foreach ($availableLocales as $locale)
                                <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue liste -->
        @if ($view === 'list')
            <!-- Tableau des POI (code existant) -->
            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Nom</th>
                                    <th>Catégories</th>
                                    <th>Région</th>
                                    <th>Statut</th>
                                    <th width="180">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pois as $poi)
                                    <tr>
                                        <td>{{ $poi->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="poi-image me-3"
                                                    style="width:40px; height:40px; border-radius:6px; overflow:hidden; background-color:#e2e8f0; display:flex; align-items:center; justify-content:center">
                                                    @if ($poi->featuredImage)
                                                        <img src="{{ asset($poi->featuredImage->path) }}"
                                                            alt="{{ $poi->translation($currentLocale)->name ?? '' }}" class="img-fluid"
                                                            style="width:100%; height:100%; object-fit:cover">
                                                    @else
                                                        <i class="fas fa-map-marker-alt text-muted"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">{{ $poi->translation($currentLocale)->name ?? '' }}</div>
                                                    @if ($poi->is_featured)
                                                        <span class="badge bg-info">À la une</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($poi->categories->isNotEmpty())
                                                @foreach ($poi->categories as $category)
                                                    <span class="badge rounded-pill"
                                                        style="background-color: {{ $category->color ?? '#6c757d' }}">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-muted">---</span>
                                            @endif
                                        </td>
                                        <td>{{ $poi->region ?? 'Non spécifié' }}</td>
                                        <td>
                                            @if ($poi->status === 'published')
                                                <span class="badge bg-success">Publié</span>
                                            @elseif($poi->status === 'draft')
                                                <span class="badge bg-warning">Brouillon</span>
                                            @else
                                                <span class="badge bg-secondary">Archivé</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('pois.show', $poi) }}"
                                                    class="btn btn-outline-secondary" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('pois.edit', $poi) }}"
                                                    class="btn btn-outline-primary" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-outline-danger" title="Supprimer"
                                                    wire:click="confirmDelete({{ $poi->id }})">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">Aucun point d'intérêt disponible</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $pois->links() }}
            </div>
        @else
            <!-- Vue carte -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div id="map" style="height: 600px;" wire:ignore></div>
                </div>
            </div>

            <!-- Légende de la carte -->
            <div class="card mt-3">
                <div class="card-body">
                    <h5 class="card-title">Légende</h5>
                    <div class="row">
                        @foreach ($parentCategories as $category)
                            <div class="col-md-3 mb-2">
                                <span class="badge rounded-pill"
                                    style="background-color: {{ $category->color ?? '#6c757d' }}">
                                    <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i> 
                                    {{ $category->translation($currentLocale)?->name ?? $category->translation('fr')?->name ?? 'Sans nom' }}
                                </span>
                            </div>
                        @endforeach
                        @if($parentCategory && $subcategories->isNotEmpty())
                            @foreach ($subcategories as $subcat)
                                <div class="col-md-3 mb-2">
                                    <span class="badge rounded-pill"
                                        style="background-color: {{ $subcat->color ?? '#6c757d' }}">
                                        <i class="{{ $subcat->icon ?? 'fas fa-folder' }}"></i> 
                                        {{ $subcat->translation($currentLocale)?->name ?? $subcat->translation('fr')?->name ?? 'Sans nom' }}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Modal de confirmation de suppression -->
    @if ($deleteModalVisible)
        <div class="modal fade show" tabindex="-1" style="display: block; background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Confirmer la suppression</h5>
                        <button type="button" class="btn-close" wire:click="cancelDelete"></button>
                    </div>
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir supprimer le point d'intérêt
                            <strong>"{{ $poiToDelete ? $poiToDelete->translation($currentLocale)->name : '' }}"</strong> ?</p>
                        <p class="text-danger">Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="cancelDelete">Annuler</button>
                        <button type="button" class="btn btn-danger" wire:click="delete">Supprimer</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @push('scripts')
        <script>
            let map = null;
            let mapMarkers = [];
            
            document.addEventListener('livewire:init', () => {
                Livewire.on('viewChanged', ({
                    viewMode
                }) => {
                    console.log('Vue active :', viewMode);
                    if (viewMode === 'map') {
                        setTimeout(() => {
                            initMap();
                        }, 300); // Attente pour que la carte s'affiche bien
                    }
                });
                
                // Écouter les mises à jour des données de la carte
                Livewire.on('mapDataUpdated', ({pois, locale}) => {
                    if (map) {
                        setTimeout(() => {
                            updateMapMarkers(pois, locale);
                        }, 100);
                    }
                });
            });

            function initMap() {
                const mapDiv = document.getElementById('map');
                mapDiv.innerHTML = '';
                
                // Réinitialiser les marqueurs
                mapMarkers = [];

                map = L.map('map').setView([11.8251, 42.5903], 8);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                // Ajouter les marqueurs avec les données initiales
                const initialPois = @json($poisForMap);
                const initialLocale = @json($currentLocale);
                updateMapMarkers(initialPois, initialLocale);

                setTimeout(() => {
                    map.invalidateSize();
                }, 300);
            }
            
            function updateMapMarkers(pois = null, currentLocale = null) {
                if (!map) return;
                
                // Supprimer les marqueurs existants
                mapMarkers.forEach(marker => {
                    map.removeLayer(marker);
                });
                mapMarkers = [];
                
                // Utiliser les données passées en paramètre ou les données par défaut
                if (!pois) {
                    pois = @json($poisForMap);
                }
                if (!currentLocale) {
                    currentLocale = @json($currentLocale);
                }

                pois.forEach(poi => {
                    if (poi.latitude && poi.longitude) {
                        // Trouver la traduction pour la langue actuelle
                        const translation = poi.translations.find(t => t.locale === currentLocale) 
                            || poi.translations.find(t => t.locale === 'fr') 
                            || (poi.translations.length > 0 ? poi.translations[0] : null);
                        
                        const name = translation ? translation.name : poi.slug;
                        const description = translation ? translation.short_description : '';
                        
                        // Créer le contenu du popup avec plus d'informations
                        let popupContent = `<div class="poi-popup">
                            <h6 class="mb-2"><strong>${name}</strong></h6>`;
                        
                        if (description) {
                            popupContent += `<p class="mb-2">${description}</p>`;
                        }
                        
                        if (poi.region) {
                            popupContent += `<small class="text-muted"><i class="fas fa-map-marker-alt me-1"></i>${poi.region}</small><br>`;
                        }
                        
                        // Afficher les catégories
                        if (poi.categories && poi.categories.length > 0) {
                            popupContent += '<div class="mt-2">';
                            poi.categories.forEach(category => {
                                const categoryColor = category.color || '#6c757d';
                                // Récupérer le nom de la catégorie selon la locale
                                let categoryName = 'Sans nom';
                                if (category.translations && category.translations.length > 0) {
                                    const categoryTranslation = category.translations.find(t => t.locale === currentLocale) 
                                        || category.translations.find(t => t.locale === 'fr') 
                                        || category.translations[0];
                                    categoryName = categoryTranslation ? categoryTranslation.name : category.name || 'Sans nom';
                                } else if (category.name) {
                                    categoryName = category.name;
                                }
                                popupContent += `<span class="badge me-1" style="background-color: ${categoryColor}; color: white;">${categoryName}</span>`;
                            });
                            popupContent += '</div>';
                        }
                        
                        popupContent += `<div class="mt-2">
                            <a href="/pois/${poi.id}" class="btn btn-sm btn-primary" style="text-decoration: none; color: white;">
                                <i class="fas fa-eye me-1"></i>Voir détails
                            </a>
                        </div></div>`;
                        
                        const marker = L.marker([poi.latitude, poi.longitude])
                            .addTo(map)
                            .bindPopup(popupContent, {
                                maxWidth: 300,
                                className: 'custom-popup'
                            });
                        
                        mapMarkers.push(marker);
                    }
                });
                
                // Ne pas changer le zoom automatiquement - laisser l'utilisateur contrôler
            }
        </script>
        
        <style>
            .poi-popup h6 {
                color: #333;
                margin-bottom: 8px;
            }
            
            .poi-popup p {
                font-size: 0.9rem;
                line-height: 1.4;
                margin-bottom: 8px;
            }
            
            .custom-popup .leaflet-popup-content {
                margin: 8px 12px;
                line-height: 1.4;
            }
            
            .custom-popup .btn {
                display: inline-block !important;
                padding: 4px 8px !important;
                font-size: 0.8rem !important;
                font-weight: 500 !important;
                text-align: center !important;
                border-radius: 4px !important;
                border: none !important;
                background-color: #0d6efd !important;
                color: white !important;
                text-decoration: none !important;
            }
            
            .custom-popup .btn:hover {
                background-color: #0b5ed7 !important;
                color: white !important;
            }
            
            .custom-popup .badge {
                font-size: 0.7rem !important;
                padding: 2px 6px !important;
            }
        </style>
    @endpush
</div>