<div>
    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="container-fluid mb-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="container-fluid mb-3">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif

    <!-- En-tête avec boutons d'action et sélecteur de langue -->
    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h1 class="h3 mb-2">{{ $tour->translation($currentLocale)->title }}</h1>
                <div class="d-flex align-items-center gap-2">
                    @php $statusBadge = $this->getStatusBadge(); @endphp
                    <span class="badge bg-{{ $statusBadge['color'] }}">{{ $statusBadge['label'] }}</span>
                    @if($tour->is_featured)
                        <span class="badge bg-warning"><i class="fas fa-star me-1"></i>Mis en avant</span>
                    @endif
                    <small class="text-muted">
                        <i class="fas fa-eye me-1"></i>{{ number_format($tour->views_count) }} vues
                    </small>
                </div>
            </div>
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

                <a href="{{ route('operator.tours.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('operator.tour-reservations.index', ['tour_id' => $tour->id]) }}" class="btn btn-outline-primary me-2">
                    <i class="fas fa-clipboard-list me-1"></i> Réservations
                </a>

                <!-- Bouton pour basculer le statut (active/inactive) -->
                @if(in_array($tour->status, ['active', 'inactive']))
                    <button wire:click="toggleStatus"
                            wire:confirm="Êtes-vous sûr de vouloir {{ $tour->status === 'active' ? 'désactiver' : 'activer' }} ce tour ?"
                            class="btn btn-{{ $tour->status === 'active' ? 'warning' : 'success' }} me-2">
                        <i class="fas fa-{{ $tour->status === 'active' ? 'pause' : 'play' }} me-1"></i>
                        {{ $tour->status === 'active' ? 'Désactiver' : 'Activer' }}
                    </button>
                @endif

                @if(in_array($tour->status, ['draft', 'rejected', 'active']))
                    <a href="{{ route('operator.tours.edit', $tour) }}" class="btn btn-{{ $tour->status === 'rejected' ? 'warning' : 'primary' }} me-2">
                        <i class="fas fa-edit me-1"></i>
                        @if($tour->status === 'rejected')
                            Modifier et resoumettre
                        @else
                            Modifier
                        @endif
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Workflow Info -->
    @if($tour->created_by_operator_user_id)
    <div class="container-fluid mb-3">
        @if($tour->status === 'pending_approval')
            <div class="alert alert-warning border-warning shadow-sm mb-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-clock fa-2x text-warning me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">
                            <i class="fas fa-hourglass-half me-1"></i>En attente d'approbation
                        </h6>
                        <small class="mb-0">
                            Soumis {{ $tour->submitted_at ? $tour->submitted_at->diffForHumans() : 'récemment' }}
                            par {{ $tour->createdBy->first_name ?? '' }} {{ $tour->createdBy->last_name ?? 'N/A' }}
                        </small>
                    </div>
                </div>
            </div>
        @elseif($tour->status === 'approved')
            <div class="alert alert-success border-success shadow-sm mb-0">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-2x text-success me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading mb-1">
                            <i class="fas fa-thumbs-up me-1"></i>Tour approuvé
                        </h6>
                        <small class="mb-0">
                            Approuvé {{ $tour->approved_at ? $tour->approved_at->diffForHumans() : 'récemment' }}
                            @if($tour->approvedBy)
                                par {{ $tour->approvedBy->name }}
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        @elseif($tour->status === 'rejected')
            <div class="alert alert-danger border-danger shadow-sm mb-0">
                <div class="d-flex align-items-start">
                    <i class="fas fa-times-circle fa-2x text-danger me-3"></i>
                    <div class="flex-grow-1">
                        <h6 class="alert-heading text-danger mb-2">
                            <i class="fas fa-ban me-1"></i>Tour rejeté
                        </h6>
                        @if($tour->rejection_reason)
                            <p class="mb-2"><strong>Raison :</strong> {{ $tour->rejection_reason }}</p>
                        @endif
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Cliquez sur "Modifier et resoumettre" pour corriger et resoumettre automatiquement.
                        </small>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-4">
            <!-- Image principale (colonne gauche) -->
            <div class="col-lg-9">
                <div class="position-relative">
                    @if ($tour->featuredImage)
                        <img src="{{ asset($tour->featuredImage->path) }}"
                            alt="{{ $tour->translation($currentLocale)->title }}"
                            class="w-100 rounded shadow" style="max-height: 400px; object-fit: cover;">
                    @else
                        <div class="bg-light text-center py-5 rounded shadow" style="height: 400px;">
                            <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                <i class="fas fa-route fa-4x text-muted mb-3"></i>
                                <p class="text-muted">Aucune image principale</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informations rapides (colonne droite) -->
            <div class="col-lg-3">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title border-bottom pb-3 mb-3">Informations Clés</h5>

                        <div class="mb-3">
                            <small class="text-muted d-block">Prix</small>
                            <strong class="h5 text-primary">{{ number_format($tour->price, 0, ',', ' ') }} {{ $tour->currency ?? 'DJF' }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Difficulté</small>
                            <span class="badge bg-{{ $this->getDifficultyColor($tour->difficulty_level) }}">
                                {{ $this->getDifficultyLabel($tour->difficulty_level) }}
                            </span>
                        </div>

                        @if($tour->max_participants)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted">Participants</small>
                                <button wire:click="refresh" class="btn btn-sm btn-link p-0" title="Actualiser">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            <div>
                                <strong class="text-primary">{{ $tour->current_participants ?? 0 }}</strong>
                                <span class="text-muted">/ {{ $tour->max_participants }}</span>
                            </div>
                            <small class="text-muted">
                                @php
                                    $availableSpots = $tour->max_participants - ($tour->current_participants ?? 0);
                                    $percentFull = $tour->max_participants > 0 ? round((($tour->current_participants ?? 0) / $tour->max_participants) * 100) : 0;
                                @endphp
                                <span class="badge bg-{{ $availableSpots > 5 ? 'success' : ($availableSpots > 0 ? 'warning' : 'danger') }}">
                                    {{ $availableSpots }} places disponibles
                                </span>
                            </small>
                        </div>
                        @endif

                        @if($tour->start_date)
                        <div class="mb-3">
                            <small class="text-muted d-block">Date de début</small>
                            <strong>{{ $tour->start_date->format('d/m/Y') }}</strong>
                        </div>
                        @endif

                        @if($tour->end_date)
                        <div class="mb-3">
                            <small class="text-muted d-block">Date de fin</small>
                            <strong>{{ $tour->end_date->format('d/m/Y') }}</strong>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Colonne principale avec onglets -->
            <div class="col-lg-8">
                <!-- Onglets -->
                <ul class="nav nav-tabs" id="tourTabs" role="tablist">
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

                <div class="tab-content" id="tourTabsContent">
                    <!-- Onglet Détails -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <!-- Description -->
                        <div class="card shadow-sm mb-4 border-0 border-top-0 rounded-top-0">
                            <div class="card-body p-4">
                                <h5 class="card-title border-bottom pb-3 mb-3">Description</h5>
                                <div>
                                    {!! nl2br(e($tour->translation($currentLocale)->description)) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Point de rendez-vous -->
                        @if($tour->meeting_point_address)
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-body p-4">
                                <h5 class="card-title border-bottom pb-3 mb-3">
                                    <i class="fas fa-map-marker-alt text-primary me-2"></i>Point de rendez-vous
                                </h5>
                                <p class="mb-0">{{ $tour->meeting_point_address }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Cible du tour -->
                        @if($tour->target)
                        <div class="card shadow-sm mb-4 border-0">
                            <div class="card-body p-4">
                                <h5 class="card-title border-bottom pb-3 mb-3">
                                    <i class="fas fa-bullseye text-info me-2"></i>Cible du tour
                                </h5>
                                <div>
                                    <h6>{{ $tour->target->title ?? $tour->target->name }}</h6>
                                    <small class="text-muted">Type: {{ class_basename($tour->target_type) }}</small>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Onglet Galerie -->
                    <div class="tab-pane fade" id="gallery" role="tabpanel">
                        @if ($tour->media->isNotEmpty())
                            <div class="card shadow-sm mb-4 border-0 border-top-0 rounded-top-0">
                                <div class="card-body p-4">
                                    <h5 class="card-title border-bottom pb-3 mb-3">Galerie photos ({{ $tour->media->count() }})</h5>

                                    <div class="row g-3">
                                        @foreach ($tour->media as $mediaItem)
                                            <div class="col-md-3 col-6">
                                                <a href="{{ asset($mediaItem->path) }}" class="glightbox"
                                                    data-gallery="tour-gallery">
                                                    <div class="position-relative">
                                                        <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                            alt="{{ $mediaItem->getTranslation($currentLocale)->title ?? $mediaItem->original_name }}"
                                                            class="img-fluid rounded shadow-sm"
                                                            style="height: 150px; width: 100%; object-fit: cover;">
                                                    </div>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="card shadow-sm mb-4 border-0 border-top-0 rounded-top-0">
                                <div class="card-body p-4 text-center text-muted">
                                    <i class="fas fa-images fa-3x mb-3"></i>
                                    <h5>Aucune image</h5>
                                    <p>Ce tour n'a pas encore d'images dans sa galerie.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="col-lg-4">
                <!-- Informations sur la publication -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title border-bottom pb-3 mb-3">Détails de publication</h5>

                        <div class="d-flex flex-column gap-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Créé le</small>
                                    <strong>{{ $tour->created_at->format('d/m/Y à H:i') }}</strong>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-edit text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Dernière mise à jour</small>
                                    <strong>{{ $tour->updated_at->format('d/m/Y à H:i') }}</strong>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="flex-shrink-0 me-3">
                                    <i class="fas fa-eye text-primary"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <small class="text-muted d-block">Vues</small>
                                    <strong>{{ number_format($tour->views_count) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Options -->
                <div class="card shadow-sm mb-4 border-0">
                    <div class="card-body p-4">
                        <h5 class="card-title border-bottom pb-3 mb-3">Options</h5>

                        <div class="d-flex flex-column gap-2">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-{{ $tour->is_featured ? 'check-circle text-success' : 'times-circle text-muted' }} me-2"></i>
                                <span>Tour mis en avant</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-{{ $tour->weather_dependent ? 'check-circle text-success' : 'times-circle text-muted' }} me-2"></i>
                                <span>Dépendant de la météo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
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
            Livewire.on('tour-locale-updated', () => {
                setTimeout(initLightbox, 100);
            });
        });
    </script>
@endpush
