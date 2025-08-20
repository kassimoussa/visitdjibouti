<div class="modern-poi-view">
    <!-- En-tête avec boutons d'action et sélecteur de langue -->
    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3">{{ $poi->translation($currentLocale)->name }}</h1>
            <div class="d-flex">
                <!-- Sélecteur de langue -->
                <div class="btn-group me-3" role="group">
                    @foreach ($availableLocales as $locale)
                        <button type="button"
                            class="btn {{ $currentLocale === $locale ? 'btn-primary' : 'btn-outline-primary' }}"
                            wire:click="changeLocale('{{ $locale }}')">
                            {{ strtoupper($locale) }}
                        </button>
                    @endforeach
                </div>

                <a href="{{ route('pois.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                <a href="{{ route('pois.edit', $poi->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mb-4">
            <!-- Image (colonne gauche) -->
            <div class="col-lg-9">
                <div class="position-relative">
                        @if ($poi->featuredImage)
                            <img src="{{ asset($poi->featuredImage->path) }}"
                                alt="{{ $poi->featuredImage->getTranslation($currentLocale)->alt_text ?? $poi->translation($currentLocale)->name }}"
                                class="w-100" style="max-height: 400px; object-fit: cover;">
                        @else
                            <div class="bg-light text-center py-5" style="height: 400px;">
                                <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                    <i class="fas fa-image fa-4x text-muted mb-3"></i>
                                    <p class="text-muted">Aucune image principale</p>
                                </div>
                            </div>
                        @endif

                        <!-- Statut et badges (positionnés au bas de l'image) -->
                        <div class="mpv-badge-container my-3">
                            @if ($poi->status === 'published')
                                <span class="mpv-badge bg-success rounded-pill px-3 py-2 me-2">Publié</span>
                            @elseif($poi->status === 'draft')
                                <span class="mpv-badge bg-warning rounded-pill px-3 py-2 me-2">Brouillon</span>
                            @else
                                <span class="mpv-badge bg-secondary rounded-pill px-3 py-2 me-2">Archivé</span>
                            @endif

                            @if ($poi->is_featured)
                                <span class="mpv-badge bg-info rounded-pill px-3 py-2 me-2">À la une</span>
                            @endif

                            @if ($poi->allow_reservations)
                                <span class="mpv-badge bg-primary rounded-pill px-3 py-2 me-2">Réservation</span>
                            @endif
                        </div>
                    </div>
            </div>

            <!-- Catégories (colonne droite) -->
            <div class="col-lg-3">
                <div class="mpv-card shadow-sm h-100 border-0">
                    <div class="mpv-card-body p-4">
                        <h4 class="mpv-card-title border-bottom pb-3 mb-3">Catégories</h4>
                        <div class="d-flex flex-column gap-2">
                            @if ($poi->categories->isNotEmpty())
                                @foreach ($poi->categories as $category)
                                    <div class="mpv-category-badge p-2 rounded d-flex align-items-center"
                                        style="background-color: {{ $category->color ?? '#f8f9fa' }}; color: {{ $category->color ? '#ffffff' : '#333333' }}">
                                        <i class="{{ $category->icon ?? 'fas fa-folder' }} me-2"></i>
                                        <span
                                            class="fw-medium">{{ $category->translation($currentLocale)?->name ?: $category->translation('fr')->name }}</span>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted">Aucune catégorie associée</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Image principale (colonne droite) -->
            <div class="col-lg-9">
                <div class="mpv-card shadow-sm overflow-hidden border-0">
                    
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Colonne principale -->
            <div class="col-lg-8">
                <!-- Description -->
                <div class="mpv-card shadow-sm mb-4 border-0">
                    <div class="mpv-card-body p-4">
                        <h4 class="mpv-card-title border-bottom pb-3 mb-3">Description</h4>

                        @if ($poi->translation($currentLocale)->short_description)
                            <div class="alert alert-light border-start border-4 border-primary mb-4">
                                <div class="fw-medium">{{ $poi->translation($currentLocale)->short_description }}</div>
                            </div>
                        @endif

                        <div class="mpv-description">
                            {!! nl2br(e($poi->translation($currentLocale)->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Galerie d'images -->
                @if ($poi->media->isNotEmpty())
                    <div class="mpv-card shadow-sm mb-4 border-0">
                        <div class="mpv-card-body p-4">
                            <h4 class="mpv-card-title border-bottom pb-3 mb-3">Galerie photos</h4>

                            <div class="row g-3 mpv-gallery">
                                @foreach ($poi->media as $mediaItem)
                                    <div class="col-md-3 col-6">
                                        <a href="{{ asset($mediaItem->path) }}" class="glightbox"
                                            data-gallery="poi-gallery">
                                            <div class="mpv-gallery-item">
                                                <img src="{{ asset($mediaItem->path) }}"
                                                    alt="{{ $mediaItem->getTranslation($currentLocale)->title ?? $mediaItem->original_name }}"
                                                    class="img-fluid">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Conseils aux visiteurs -->
                @if ($poi->translation($currentLocale)->tips)
                    <div class="mpv-card shadow-sm mb-4 border-0">
                        <div class="mpv-card-body p-4">
                            <h4 class="mpv-card-title border-bottom pb-3 mb-3">
                                <i class="fas fa-lightbulb text-warning me-2"></i>Conseils aux visiteurs
                            </h4>

                            <div class="mpv-tips-content">
                                {!! nl2br(e($poi->translation($currentLocale)->tips)) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Colonne latérale -->
            <div class="col-lg-4">
                <!-- Informations pratiques -->
                <div class="mpv-card shadow-sm mb-4 border-0">
                    <div class="mpv-card-body p-4">
                        <h4 class="mpv-card-title border-bottom pb-3 mb-3">Informations pratiques</h4>

                        <div class="mpv-info-list">
                            @if ($poi->region)
                                <div class="mpv-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mpv-info-icon">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Région</div>
                                            <div>{{ $poi->region }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($poi->translation($currentLocale)->address)
                                <div class="mpv-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mpv-info-icon">
                                            <i class="fas fa-location-arrow"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Adresse</div>
                                            <div>{{ $poi->translation($currentLocale)->address }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($poi->translation($currentLocale)->opening_hours)
                                <div class="mpv-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mpv-info-icon">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Horaires d'ouverture</div>
                                            <div>{!! nl2br(e($poi->translation($currentLocale)->opening_hours)) !!}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($poi->translation($currentLocale)->entry_fee)
                                <div class="mpv-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mpv-info-icon">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Prix d'entrée</div>
                                            <div>{{ $poi->translation($currentLocale)->entry_fee }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($poi->contact)
                                <div class="mpv-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mpv-info-icon">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Contact</div>
                                            <div>{!! nl2br(e($poi->contact)) !!}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($poi->website)
                                <div class="mpv-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mpv-info-icon">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Site web</div>
                                            <div><a href="{{ $poi->website }}" target="_blank"
                                                    class="text-decoration-none">{{ $poi->website }}</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Localisation sur la carte -->
                @if ($poi->latitude && $poi->longitude)
                    <div class="mpv-card shadow-sm mb-4 border-0">
                        <div class="mpv-card-body p-4">
                            <h4 class="mpv-card-title border-bottom pb-3 mb-3">Localisation</h4>

                            <div wire:ignore>
                                <div id="mpv-map" class="rounded" style="height: 300px;"></div>
                            </div>

                            <div class="d-flex justify-content-between mt-3">
                                <small class="text-muted">
                                    Latitude: {{ $poi->latitude }}
                                </small>
                                <small class="text-muted">
                                    Longitude: {{ $poi->longitude }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informations sur la publication -->
                <div class="mpv-card shadow-sm mb-4 border-0">
                    <div class="mpv-card-body p-4">
                        <h4 class="mpv-card-title border-bottom pb-3 mb-3">Détails de publication</h4>

                        <div class="mpv-info-list">
                            <div class="mpv-info-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mpv-info-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Créé le</div>
                                        <div>{{ $poi->created_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mpv-info-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mpv-info-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Dernière mise à jour</div>
                                        <div>{{ $poi->updated_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            @if ($poi->creator)
                                <div class="mpv-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mpv-info-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Créé par</div>
                                            <div>{{ $poi->creator->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @if ($poi->latitude && $poi->longitude)
        <script>
            document.addEventListener('livewire:init', function() {
                // Déclaration des variables en portée globale
                let map = null;
                let marker = null;

                function createMap() {
                    // Coordonnées du POI
                    const lat = {{ $poi->latitude }};
                    const lng = {{ $poi->longitude }};
                    const name = "{{ $poi->translation($currentLocale)->name }}";

                    // Détruire la carte existante si nécessaire
                    if (map) {
                        map.remove();
                        map = null;
                    }

                    // Créer une nouvelle carte
                    map = L.map('mpv-map').setView([lat, lng], 10);

                    // Ajouter les tuiles OpenStreetMap
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // Ajouter un marqueur
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup(`<strong>${name}</strong>`)
                        .openPopup();

                    // S'assurer que la carte s'affiche correctement
                    setTimeout(() => map.invalidateSize(), 100);
                }

                // Initialiser la carte au chargement
                createMap();

                // Recréer la carte lorsque Livewire termine une mise à jour
                Livewire.on('poi-locale-updated', () => {
                    setTimeout(createMap, 100);
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('livewire:init', function() {
            // Variable pour stocker l'instance de GLightbox
            let lightbox = null;

            function initLightbox() {
                // Détruire l'instance existante si elle existe
                if (lightbox) {
                    lightbox.destroy();
                    lightbox = null;
                }

                // Créer une nouvelle instance
                lightbox = GLightbox({
                    selector: '.glightbox',
                    touchNavigation: true,
                    loop: true,
                    preload: false,
                    zoomable: true,
                    draggable: true,
                    openEffect: 'zoom',
                    closeEffect: 'fade',
                    showTitle: false
                });
            }

            // Initialiser GLightbox au chargement
            initLightbox();

            // Réinitialiser GLightbox lorsque Livewire termine une mise à jour
            Livewire.on('poi-locale-updated', () => {
                setTimeout(initLightbox, 100);
            });
        });
    </script>
@endpush
