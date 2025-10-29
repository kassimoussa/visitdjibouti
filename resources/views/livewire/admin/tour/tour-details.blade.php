<div>
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

                <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
                <a href="{{ route('tours.edit', $tour->id) }}" class="btn btn-primary me-2">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                @if($tour->status === 'pending_approval')
                    <a href="{{ route('tours.approvals') }}" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Gérer l'Approbation
                    </a>
                @endif
            </div>
        </div>
    </div>

    <!-- Workflow Info if operator-created -->
    @if($tour->created_by_operator_user_id)
    <div class="container-fluid mb-4">
        <div class="alert alert-{{ $tour->status === 'approved' ? 'success' : ($tour->status === 'rejected' ? 'danger' : 'warning') }}">
            <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informations du Workflow d'Approbation</h5>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <strong>Créé par:</strong><br>
                    {{ $tour->createdBy->name ?? 'N/A' }}<br>
                    <small class="text-muted">{{ $tour->createdBy->email ?? '' }}</small>
                </div>
                <div class="col-md-3">
                    <strong>Tour Operator:</strong><br>
                    {{ $tour->tourOperator->name ?? 'N/A' }}
                </div>
                @if($tour->submitted_at)
                <div class="col-md-3">
                    <strong>Soumis le:</strong><br>
                    {{ $tour->submitted_at->format('d/m/Y à H:i') }}
                </div>
                @endif
                @if($tour->approved_at)
                <div class="col-md-3">
                    <strong>Approuvé le:</strong><br>
                    {{ $tour->approved_at->format('d/m/Y à H:i') }}<br>
                    <small class="text-muted">Par: {{ $tour->approvedBy->name ?? 'N/A' }}</small>
                </div>
                @endif
            </div>
            @if($tour->rejection_reason)
                <hr>
                <strong>Raison du rejet:</strong>
                <p class="mb-0">{{ $tour->rejection_reason }}</p>
            @endif
        </div>
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
                            class="w-100" style="max-height: 400px; object-fit: cover;">
                    @else
                        <div class="bg-light text-center py-5" style="height: 400px;">
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
                            <small class="text-muted d-block">Opérateur</small>
                            <strong>{{ $tour->tourOperator->name }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Prix</small>
                            <strong class="h5">{{ number_format($tour->price, 0, ',', ' ') }} {{ $tour->currency ?? 'DJF' }}</strong>
                        </div>

                        <div class="mb-3">
                            <small class="text-muted d-block">Difficulté</small>
                            <span class="badge bg-{{ $this->getDifficultyColor($tour->difficulty_level) }}">
                                {{ $this->getDifficultyLabel($tour->difficulty_level) }}
                            </span>
                        </div>

                        @if($tour->max_participants)
                        <div class="mb-3">
                            <small class="text-muted d-block">Participants max</small>
                            <strong>{{ $tour->max_participants }} personnes</strong>
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
