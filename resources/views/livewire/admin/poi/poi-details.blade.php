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
            <!-- Colonne principale avec onglets -->
            <div class="col-lg-8">
                <!-- Onglets -->
                <ul class="nav nav-tabs" id="poiTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" 
                                data-bs-target="#details" type="button" role="tab">
                            <i class="fas fa-info-circle me-1"></i>Détails
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="gallery-tab" data-bs-toggle="tab" 
                                data-bs-target="#gallery" type="button" role="tab">
                            <i class="fas fa-images me-1"></i>Galerie
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="poiTabsContent">
                    <!-- Onglet Détails -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <!-- Description -->
                        <div class="mpv-card shadow-sm mb-4 border-0 border-top-0 rounded-top-0">
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

                    <!-- Onglet Galerie -->
                    <div class="tab-pane fade" id="gallery" role="tabpanel">
                        @if ($poi->media->isNotEmpty())
                            <div class="mpv-card shadow-sm mb-4 border-0 border-top-0 rounded-top-0">
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
                        @else
                            <div class="mpv-card shadow-sm mb-4 border-0 border-top-0 rounded-top-0">
                                <div class="mpv-card-body p-4 text-center text-muted">
                                    <i class="fas fa-images fa-3x mb-3"></i>
                                    <h5>Aucune image</h5>
                                    <p>Ce POI n'a pas encore d'images dans sa galerie.</p>
                                </div>
                            </div>
                        @endif
                    </div>

                </div>
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

                <!-- Contacts -->
                @if($poi->hasContacts())
                    <div class="mpv-card shadow-sm mb-4 border-0">
                        <div class="mpv-card-body p-4">
                            <h4 class="mpv-card-title border-bottom pb-3 mb-3">
                                <i class="fas fa-address-book me-2"></i>Contacts ({{ count($poi->contacts) }})
                            </h4>

                            <div class="row g-3">
                                @foreach($poi->contacts as $index => $contact)
                                    <div class="col-12 {{ count($poi->contacts) > 1 ? 'col-lg-6' : '' }}">
                                        <div class="contact-card border rounded p-3 h-100 position-relative"
                                             style="background: linear-gradient(135deg, {{ $this->getContactTypeColor($contact['type']) }}15, {{ $this->getContactTypeColor($contact['type']) }}05);">
                                            
                                            @if($contact['is_primary'] ?? false)
                                                <span class="position-absolute top-0 end-0 mt-2 me-2">
                                                    <i class="fas fa-star text-warning" title="Contact principal"></i>
                                                </span>
                                            @endif
                                            
                                            <!-- En-tête du contact -->
                                            <div class="d-flex align-items-start mb-3">
                                                <div class="contact-type-badge me-3 d-flex align-items-center justify-content-center rounded-circle" 
                                                     style="background-color: {{ $this->getContactTypeColor($contact['type']) }}; width: 48px; height: 48px; color: white;">
                                                    <i class="{{ $this->getContactTypeIcon($contact['type']) }} fa-lg"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="contact-name mb-1 fw-bold">{{ $contact['name'] }}</h5>
                                                    <span class="badge rounded-pill px-3 py-1" 
                                                          style="background-color: {{ $this->getContactTypeColor($contact['type']) }}20; color: {{ $this->getContactTypeColor($contact['type']) }};">
                                                        {{ $this->getContactTypeName($contact['type']) }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <!-- Informations de contact -->
                                            <div class="contact-info">
                                                @if(!empty($contact['phone']))
                                                    <div class="contact-item d-flex align-items-center mb-2">
                                                        <div class="contact-icon me-3">
                                                            <i class="fas fa-phone text-muted"></i>
                                                        </div>
                                                        <div class="contact-value">
                                                            <a href="tel:{{ $contact['phone'] }}" class="text-decoration-none fw-medium">
                                                                {{ $contact['phone'] }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if(!empty($contact['email']))
                                                    <div class="contact-item d-flex align-items-center mb-2">
                                                        <div class="contact-icon me-3">
                                                            <i class="fas fa-envelope text-muted"></i>
                                                        </div>
                                                        <div class="contact-value">
                                                            <a href="mailto:{{ $contact['email'] }}" class="text-decoration-none fw-medium">
                                                                {{ $contact['email'] }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if(!empty($contact['website']))
                                                    <div class="contact-item d-flex align-items-center mb-2">
                                                        <div class="contact-icon me-3">
                                                            <i class="fas fa-globe text-muted"></i>
                                                        </div>
                                                        <div class="contact-value">
                                                            <a href="{{ $contact['website'] }}" target="_blank" class="text-decoration-none fw-medium">
                                                                <span class="text-truncate d-inline-block" style="max-width: 150px;">
                                                                    {{ str_replace(['http://', 'https://'], '', $contact['website']) }}
                                                                </span>
                                                                <i class="fas fa-external-link-alt fa-xs ms-1"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if(!empty($contact['address']))
                                                    <div class="contact-item d-flex align-items-start mb-2">
                                                        <div class="contact-icon me-3 mt-1">
                                                            <i class="fas fa-map-marker-alt text-muted"></i>
                                                        </div>
                                                        <div class="contact-value">
                                                            <span class="fw-medium">{{ $contact['address'] }}</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if(!empty($contact['description']))
                                                    <div class="contact-description mt-3 pt-3 border-top">
                                                        <small class="text-muted fst-italic">
                                                            <i class="fas fa-quote-left fa-xs me-1"></i>
                                                            {{ $contact['description'] }}
                                                        </small>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

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

@push('styles')
<style>
.contact-card {
    transition: all 0.3s ease;
    border-left: 4px solid transparent !important;
}

.contact-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.contact-type-badge {
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.contact-item {
    transition: all 0.2s ease;
}

.contact-item:hover {
    transform: translateX(5px);
}

.contact-icon {
    width: 24px;
    text-align: center;
}

.contact-value a:hover {
    color: var(--bs-primary) !important;
}

.contact-description {
    background-color: rgba(255,255,255,0.7);
    border-radius: 8px;
    padding: 12px;
}
</style>
@endpush

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
