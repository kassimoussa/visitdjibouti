<div class="modern-event-view">
    <!-- En-tête avec boutons d'action et sélecteur de langue -->
    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3">{{ $event->translation($currentLocale)->title }}</h1>
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

                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row mb-4">
            <!-- Image principale (colonne gauche) -->
            <div class="col-lg-9">
                <div class="position-relative">
                    @if ($event->featuredImage)
                        <img src="{{ asset($event->featuredImage->path) }}"
                            alt="{{ $event->featuredImage->getTranslation($currentLocale)->alt_text ?? $event->translation($currentLocale)->title }}"
                            class="w-100" style="max-height: 400px; object-fit: cover;">
                    @else
                        <div class="bg-light text-center py-5" style="height: 400px;">
                            <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                                <p class="text-muted">Aucune image principale</p>
                            </div>
                        </div>
                    @endif

                    <!-- Statut et badges (positionnés au bas de l'image) -->
                    <div class="mev-badge-container my-3">
                        @if ($event->status === 'published')
                            <span class="mev-badge bg-success rounded-pill px-3 py-2 me-2">Publié</span>
                        @elseif($event->status === 'draft')
                            <span class="mev-badge bg-warning rounded-pill px-3 py-2 me-2">Brouillon</span>
                        @else
                            <span class="mev-badge bg-secondary rounded-pill px-3 py-2 me-2">Archivé</span>
                        @endif

                        <span class="mev-badge {{ $eventStatus['class'] }} rounded-pill px-3 py-2 me-2">
                            <i class="{{ $eventStatus['icon'] }} me-1"></i>{{ $eventStatus['label'] }}
                        </span>

                        @if ($event->is_featured)
                            <span class="mev-badge bg-info rounded-pill px-3 py-2 me-2">À la une</span>
                        @endif

                        @if ($eventStats['is_sold_out'])
                            <span class="mev-badge bg-danger rounded-pill px-3 py-2 me-2">Complet</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations rapides (colonne droite) -->
            <div class="col-lg-3">
                <div class="mev-card shadow-sm h-100 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Informations rapides</h4>
                        
                        <!-- Dates -->
                        <div class="mev-info-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 mev-info-icon">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-medium">Dates</div>
                                    <div>{{ $formattedDateRange }}</div>
                                    @if ($startTime || $endTime)
                                        <div class="text-muted small">
                                            {{ $startTime ? $startTime : '' }}{{ $endTime ? ' - ' . $endTime : '' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div class="mev-info-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 mev-info-icon">
                                    <i class="fas fa-tag text-success"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-medium">Prix</div>
                                    <div class="text-success fw-bold">{{ $formattedPrice }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Participants -->
                        @if ($event->max_participants)
                            <div class="mev-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-users text-info"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Participants</div>
                                        <div>{{ $eventStats['total_participants'] }} / {{ $event->max_participants }}</div>
                                        @if ($eventStats['available_spots'] > 0)
                                            <div class="text-success small">{{ $eventStats['available_spots'] }} places disponibles</div>
                                        @else
                                            <div class="text-danger small">Complet</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mev-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-users text-info"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Participants</div>
                                        <div>{{ $eventStats['total_participants'] }}</div>
                                        <div class="text-muted small">Nombre illimité</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Organisateur -->
                        @if ($event->organizer)
                            <div class="mev-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-user-tie text-warning"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Organisateur</div>
                                        <div>{{ $event->organizer }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Catégories -->
                        <div class="mev-info-item">
                            <h6 class="fw-medium mb-2">Catégories</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @if ($event->categories->isNotEmpty())
                                    @foreach ($event->categories as $category)
                                        <span class="badge rounded-pill" 
                                            style="background-color: {{ $category->color ?? '#6c757d' }}; color: white;">
                                            <i class="{{ $category->icon ?? 'fas fa-folder' }} me-1"></i>
                                            {{ $category->translation($currentLocale)?->name ?: $category->translation('fr')->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Aucune catégorie</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Colonne principale -->
            <div class="col-lg-8">
                <!-- Description -->
                <div class="mev-card shadow-sm mb-4 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Description</h4>

                        @if ($event->translation($currentLocale)->short_description)
                            <div class="alert alert-light border-start border-4 border-primary mb-4">
                                <div class="fw-medium">{{ $event->translation($currentLocale)->short_description }}</div>
                            </div>
                        @endif

                        <div class="mev-description">
                            {!! nl2br(e($event->translation($currentLocale)->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Programme -->
                @if ($event->translation($currentLocale)->program)
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">
                                <i class="fas fa-list-ul text-primary me-2"></i>Programme
                            </h4>
                            <div class="mev-program-content">
                                {!! nl2br(e($event->translation($currentLocale)->program)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Prérequis -->
                @if ($event->translation($currentLocale)->requirements)
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Prérequis
                            </h4>
                            <div class="mev-requirements-content">
                                {!! nl2br(e($event->translation($currentLocale)->requirements)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Galerie d'images -->
                @if ($event->media->isNotEmpty())
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">Galerie photos</h4>

                            <div class="row g-3 mev-gallery">
                                @foreach ($event->media as $mediaItem)
                                    <div class="col-md-3 col-6">
                                        <a href="{{ asset($mediaItem->path) }}" class="glightbox"
                                            data-gallery="event-gallery">
                                            <div class="mev-gallery-item">
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

                <!-- Informations additionnelles -->
                @if ($event->translation($currentLocale)->additional_info)
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">
                                <i class="fas fa-info-circle text-info me-2"></i>Informations additionnelles
                            </h4>
                            <div class="mev-additional-info-content">
                                {!! nl2br(e($event->translation($currentLocale)->additional_info)) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Colonne latérale -->
            <div class="col-lg-4">
                <!-- Statistiques -->
                <div class="mev-card shadow-sm mb-4 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Statistiques</h4>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-1 text-primary">{{ $eventStats['confirmed_registrations'] }}</div>
                                    <div class="small text-muted">Inscriptions confirmées</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-1 text-warning">{{ $eventStats['pending_registrations'] }}</div>
                                    <div class="small text-muted">En attente</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-1 text-success">{{ $eventStats['approved_reviews'] }}</div>
                                    <div class="small text-muted">Avis approuvés</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    @if ($eventStats['average_rating'])
                                        <div class="h4 mb-1 text-info">{{ $eventStats['average_rating'] }}/5</div>
                                        <div class="small text-muted">Note moyenne</div>
                                    @else
                                        <div class="h4 mb-1 text-muted">-</div>
                                        <div class="small text-muted">Pas d'avis</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Localisation -->
                @if ($event->location || ($event->latitude && $event->longitude))
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">Localisation</h4>

                            <div class="mev-info-list mb-3">
                                @if ($event->location)
                                    <div class="mev-info-item mb-2">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 mev-info-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <div class="fw-medium">Lieu</div>
                                                <div>{{ $event->location }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($event->translation($currentLocale)->location_details)
                                    <div class="mev-info-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 mev-info-icon">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <div class="fw-medium">Détails</div>
                                                <div>{{ $event->translation($currentLocale)->location_details }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($event->latitude && $event->longitude)
                                <div wire:ignore>
                                    <div id="mev-map" class="rounded" style="height: 250px;"></div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <small class="text-muted">
                                        Latitude: {{ $event->latitude }}
                                    </small>
                                    <small class="text-muted">
                                        Longitude: {{ $event->longitude }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Contact -->
                <div class="mev-card shadow-sm mb-4 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Contact et liens</h4>

                        <div class="mev-info-list">
                            @if ($event->contact_email)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Email</div>
                                            <div><a href="mailto:{{ $event->contact_email }}" class="text-decoration-none">{{ $event->contact_email }}</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->contact_phone)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Téléphone</div>
                                            <div><a href="tel:{{ $event->contact_phone }}" class="text-decoration-none">{{ $event->contact_phone }}</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->website_url)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Site web</div>
                                            <div><a href="{{ $event->website_url }}" target="_blank" class="text-decoration-none">{{ $event->website_url }}</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->ticket_url)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Billetterie</div>
                                            <div><a href="{{ $event->ticket_url }}" target="_blank" class="btn btn-sm btn-primary">Acheter des billets</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations sur la publication -->
                <div class="mev-card shadow-sm mb-4 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Détails de publication</h4>

                        <div class="mev-info-list">
                            <div class="mev-info-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Créé le</div>
                                        <div>{{ $event->created_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mev-info-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Dernière mise à jour</div>
                                        <div>{{ $event->updated_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            @if ($event->creator)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Créé par</div>
                                            <div>{{ $event->creator->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->views_count)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Vues</div>
                                            <div>{{ number_format($event->views_count) }}</div>
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
    @if ($event->latitude && $event->longitude)
        <script>
            document.addEventListener('livewire:init', function() {
                // Déclaration des variables en portée globale
                let map = null;
                let marker = null;

                function createMap() {
                    // Coordonnées de l'événement
                    const lat = {{ $event->latitude }};
                    const lng = {{ $event->longitude }};
                    const name = "{{ $event->translation($currentLocale)->title }}";

                    // Détruire la carte existante si nécessaire
                    if (map) {
                        map.remove();
                        map = null;
                    }

                    // Créer une nouvelle carte
                    map = L.map('mev-map').setView([lat, lng], 12);

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
                Livewire.on('event-locale-updated', () => {
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
            Livewire.on('event-locale-updated', () => {
                setTimeout(initLightbox, 100);
            });
        });
    </script>
@endpush