<div>
    <div class="container-fluid">
        <!-- En-tête avec bouton d'ajout et basculement de vue -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <h1 class="h3 mb-0 me-3">Liste des points d'intérêt</h1>
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
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                            id="search" placeholder="Rechercher...">
                    </div>

                    <div class="col-md-3">
                        <label for="category" class="form-label">Catégorie</label>
                        <select wire:model.live="category" id="category" class="form-select">
                            <option value="">Toutes les catégories</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
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

                    <div class="col-md-1">
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
                        @foreach ($categories as $category)
                            <div class="col-md-3 mb-2">
                                <span class="badge rounded-pill"
                                    style="background-color: {{ $category->color ?? '#6c757d' }}">
                                    <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i> {{ $category->name }}
                                </span>
                            </div>
                        @endforeach
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
            });

            function initMap() {
                const pois = @json($pois->items());
                const currentLocale = @json($currentLocale);
                const mapDiv = document.getElementById('map');
                mapDiv.innerHTML = '';

                const map = L.map('map').setView([11.8251, 42.5903], 8);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);

                pois.forEach(poi => {
                    if (poi.latitude && poi.longitude) {
                        // Trouver la traduction pour la langue actuelle
                        const translation = poi.translations.find(t => t.locale === currentLocale) 
                            || poi.translations.find(t => t.locale === 'fr') 
                            || (poi.translations.length > 0 ? poi.translations[0] : null);
                        
                        const name = translation ? translation.name : poi.slug;
                        const description = translation ? translation.short_description : '';
                        
                        L.marker([poi.latitude, poi.longitude])
                            .addTo(map)
                            .bindPopup(`<strong>${name}</strong><br>${description}`);
                    }
                });

                setTimeout(() => {
                    map.invalidateSize();
                }, 300);
            }
        </script>
    @endpush
</div>