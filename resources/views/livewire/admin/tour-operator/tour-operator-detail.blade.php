<div class="modern-operator-view">
    <!-- En-tête avec boutons d'action et sélecteur de langue -->
    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3">{{ $this->getTranslation()->name ?: 'Tour Operator' }}</h1>
            <div class="d-flex">
                <!-- Sélecteur de langue -->
                <div class="btn-group me-3" role="group">
                    @foreach($availableLocales as $locale)
                        <button type="button"
                            class="btn {{ $activeLocale === $locale ? 'btn-primary' : 'btn-outline-primary' }}"
                            wire:click="setActiveLocale('{{ $locale }}')">
                            {{ strtoupper($locale) }}
                        </button>
                    @endforeach
                </div>

                <a href="{{ route('tour-operators.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                <button type="button" class="btn btn-primary" wire:click="redirectToEdit">
                    <i class="fas fa-edit me-1"></i> Modifier
                </button>
            </div>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if (session()->has('message'))
        <div class="container-fluid">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="container-fluid">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <div class="container-fluid">
        <div class="row mb-4">
            <!-- Badges de statut -->
            <div class="col-12 mb-3">
                <div class="mov-badge-container">
                    <span class="mov-badge bg-{{ $tourOperator->is_active ? 'success' : 'secondary' }} rounded-pill px-3 py-2 me-2">
                        {{ $tourOperator->is_active ? 'Actif' : 'Inactif' }}
                    </span>

                    @if($tourOperator->featured)
                        <span class="mov-badge bg-warning text-dark rounded-pill px-3 py-2 me-2">
                            <i class="fas fa-star"></i> Mis en avant
                        </span>
                    @endif

                    @if($tourOperator->certification_level)
                        <span class="mov-badge bg-info rounded-pill px-3 py-2 me-2">
                            <i class="fas fa-certificate me-1"></i>{{ ucfirst($tourOperator->certification_level) }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <!-- Colonne principale -->
            <div class="col-lg-8">
                <!-- Description -->
                @if($this->getTranslation()->description)
                <div class="mov-card shadow-sm border-0 mb-4">
                    <div class="mov-card-body p-4">
                        <h4 class="mov-card-title border-bottom pb-3 mb-3">
                            <i class="fas fa-align-left text-primary me-2"></i>
                            Description
                        </h4>
                        <div class="mov-description">
                            {{ $this->getTranslation()->description }}
                        </div>
                    </div>
                </div>
                @endif

                <!-- Localisation -->
                @if($tourOperator->latitude && $tourOperator->longitude)
                <div class="mov-card shadow-sm border-0 mb-4">
                    <div class="mov-card-body p-4">
                        <h4 class="mov-card-title border-bottom pb-3 mb-3">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            Localisation
                        </h4>

                        @if($tourOperator->address || $this->getTranslation()->address_translated)
                            <div class="mov-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mov-info-icon">
                                        <i class="fas fa-map-marker-alt text-primary"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Adresse</div>
                                        <div>
                                            @if($this->getTranslation()->address_translated)
                                                {{ $this->getTranslation()->address_translated }}
                                            @else
                                                {{ $tourOperator->address }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Carte Leaflet -->
                        <div id="operator-map" style="height: 300px; border-radius: 8px;" class="mb-3"></div>

                        <div class="text-center">
                            <a href="https://maps.google.com/?q={{ $tourOperator->latitude }},{{ $tourOperator->longitude }}"
                               target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> Ouvrir dans Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Initialiser la carte
                        const map = L.map('operator-map').setView([{{ $tourOperator->latitude }}, {{ $tourOperator->longitude }}], 13);

                        // Ajouter le layer de tuiles OpenStreetMap
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                        }).addTo(map);

                        // Ajouter le marqueur
                        const marker = L.marker([{{ $tourOperator->latitude }}, {{ $tourOperator->longitude }}]).addTo(map);
                        marker.bindPopup('<b>{{ $this->getTranslation()->name }}</b>').openPopup();
                    });
                </script>
                @endpush
                @endif

            <!-- POIs desservis -->
            @if($tourOperator->pois->count() > 0)
                <div class="mov-card shadow-sm border-0 mb-4">
                    <div class="mov-card-body p-4">
                        <h4 class="mov-card-title border-bottom pb-3 mb-3">
                            <i class="fas fa-map-signs text-primary me-2"></i>
                            Points d'intérêt desservis ({{ $this->getServedPoisCount() }})
                        </h4>
                        <div class="row">
                            @foreach($tourOperator->pois->take(6) as $poi)
                                @php
                                    $poiTranslation = $poi->translations->firstWhere('locale', $activeLocale)
                                        ?? $poi->translations->firstWhere('locale', 'fr')
                                        ?? $poi->translations->first();
                                    $pivot = $poi->pivot;
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100 bg-light">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1">
                                                    <a href="{{ route('pois.show', $poi->id) }}" class="text-decoration-none">
                                                        {{ $poiTranslation->name ?? 'Sans nom' }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">{{ $poi->region }}</small>
                                            </div>
                                            @if($pivot->is_primary)
                                                <span class="badge bg-primary">Principal</span>
                                            @endif
                                        </div>

                                        @if($pivot->service_type)
                                            <div class="mt-2">
                                                <span class="badge bg-info">{{ ucfirst($pivot->service_type) }}</span>
                                            </div>
                                        @endif

                                        @if($pivot->notes)
                                            <small class="text-muted d-block mt-2">{{ $pivot->notes }}</small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($tourOperator->pois->count() > 6)
                            <div class="text-center mt-3">
                                <span class="text-muted">Et {{ $tourOperator->pois->count() - 6 }} autre(s) POI(s)...</span>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Tours proposés -->
            @if($tourOperator->tours->count() > 0)
                <div class="mov-card shadow-sm border-0 mb-4">
                    <div class="mov-card-body p-4">
                        <h4 class="mov-card-title border-bottom pb-3 mb-3">
                            <i class="fas fa-map-signs text-primary me-2"></i>
                            Tours proposés ({{ $this->getToursCount() }})
                        </h4>
                        <div class="row">
                            @foreach($tourOperator->tours as $tour)
                                @php
                                    $tourTranslation = $tour->translations->firstWhere('locale', $activeLocale)
                                        ?? $tour->translations->firstWhere('locale', 'fr')
                                        ?? $tour->translations->first();
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        @if($tour->featuredImage)
                                            <img src="{{ asset($tour->featuredImage->path) }}"
                                                 class="card-img-top"
                                                 style="height: 150px; object-fit: cover;"
                                                 alt="{{ $tourTranslation->title ?? 'Tour' }}">
                                        @endif
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">
                                                    <a href="{{ route('tours.show', $tour->id) }}" class="text-decoration-none">
                                                        {{ $tourTranslation->title ?? 'Sans titre' }}
                                                    </a>
                                                </h6>
                                            </div>

                                            <div class="mb-2">
                                                @if($tour->status === 'active')
                                                    <span class="badge bg-success">Actif</span>
                                                @elseif($tour->status === 'pending_approval')
                                                    <span class="badge bg-warning">En attente</span>
                                                @elseif($tour->status === 'approved')
                                                    <span class="badge bg-info">Approuvé</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ ucfirst($tour->status) }}</span>
                                                @endif

                                                @if($tour->is_featured)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                @endif
                                            </div>

                                            @if($tourTranslation->description)
                                                <p class="text-muted small mb-2">{{ Str::limit($tourTranslation->description, 100) }}</p>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-primary fw-bold">{{ number_format($tour->price, 0, ',', ' ') }} {{ $tour->currency }}</span>
                                                @if($tour->max_participants)
                                                    <small class="text-muted">
                                                        <i class="fas fa-users"></i> Max: {{ $tour->max_participants }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activités proposées -->
            @if($tourOperator->activities->count() > 0)
                <div class="mov-card shadow-sm border-0 mb-4">
                    <div class="mov-card-body p-4">
                        <h4 class="mov-card-title border-bottom pb-3 mb-3">
                            <i class="fas fa-running text-primary me-2"></i>
                            Activités proposées ({{ $this->getActivitiesCount() }})
                        </h4>
                        <div class="row">
                            @foreach($tourOperator->activities as $activity)
                                @php
                                    $activityTranslation = $activity->translations->firstWhere('locale', $activeLocale)
                                        ?? $activity->translations->firstWhere('locale', 'fr')
                                        ?? $activity->translations->first();
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100">
                                        @if($activity->featuredImage)
                                            <img src="{{ asset($activity->featuredImage->path) }}"
                                                 class="card-img-top"
                                                 style="height: 150px; object-fit: cover;"
                                                 alt="{{ $activityTranslation->title ?? 'Activité' }}">
                                        @endif
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="mb-0">
                                                    <a href="{{ route('activities.show', $activity->id) }}" class="text-decoration-none">
                                                        {{ $activityTranslation->title ?? 'Sans titre' }}
                                                    </a>
                                                </h6>
                                            </div>

                                            <div class="mb-2">
                                                @if($activity->status === 'active')
                                                    <span class="badge bg-success">Active</span>
                                                @elseif($activity->status === 'draft')
                                                    <span class="badge bg-secondary">Brouillon</span>
                                                @else
                                                    <span class="badge bg-danger">Inactive</span>
                                                @endif

                                                @if($activity->is_featured)
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-star"></i>
                                                    </span>
                                                @endif
                                            </div>

                                            @if($activityTranslation->description)
                                                <p class="text-muted small mb-2">{{ Str::limit($activityTranslation->description, 100) }}</p>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="text-primary fw-bold">{{ number_format($activity->price, 0, ',', ' ') }} {{ $activity->currency }}</span>
                                                @if($activity->max_participants)
                                                    <small class="text-muted">
                                                        <i class="fas fa-users"></i> {{ $activity->current_participants }}/{{ $activity->max_participants }}
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            </div>

            <!-- Informations rapides (colonne droite) -->
            <div class="col-lg-4">
                <div class="mov-card shadow-sm h-100 border-0">
                    <div class="mov-card-body p-4">
                        <h4 class="mov-card-title border-bottom pb-3 mb-3">Informations rapides</h4>

                        <!-- Contact -->
                        @if($this->getPhonesArray())
                            <div class="mov-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mov-info-icon">
                                        <i class="fas fa-phone text-primary"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Téléphone</div>
                                        @foreach($this->getPhonesArray() as $phone)
                                            <div class="small"><a href="tel:{{ $phone }}" class="text-decoration-none">{{ $phone }}</a></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($this->getEmailsArray())
                            <div class="mov-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mov-info-icon">
                                        <i class="fas fa-envelope text-primary"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Email</div>
                                        @foreach($this->getEmailsArray() as $email)
                                            <div class="small"><a href="mailto:{{ $email }}" class="text-decoration-none">{{ $email }}</a></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($tourOperator->website)
                            <div class="mov-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mov-info-icon">
                                        <i class="fas fa-globe text-primary"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Site web</div>
                                        <div class="small">
                                            <a href="{{ $tourOperator->website_url }}" target="_blank" class="text-decoration-none">
                                                {{ $tourOperator->website }}
                                                <i class="fas fa-external-link-alt ms-1"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Statistiques -->
                        <div class="mov-info-item mb-3 border-top pt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Tours</span>
                                <span class="badge bg-primary">{{ $this->getToursCount() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Activités</span>
                                <span class="badge bg-success">{{ $this->getActivitiesCount() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">POIs desservis</span>
                                <span class="badge bg-info">{{ $this->getServedPoisCount() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small">Images</span>
                                <span class="badge bg-secondary">{{ $this->getMediaCount() }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small">Traductions</span>
                                <span class="badge bg-dark">{{ $tourOperator->translations->count() }}</span>
                            </div>
                        </div>

                        <!-- Actions rapides -->
                        <div class="mov-info-item border-top pt-3">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-sm btn-outline-{{ $tourOperator->featured ? 'secondary' : 'warning' }}"
                                        wire:click="toggleFeatured">
                                    <i class="fas fa-star"></i>
                                    {{ $tourOperator->featured ? 'Retirer mise en avant' : 'Mettre en avant' }}
                                </button>

                                <button type="button" class="btn btn-sm btn-outline-{{ $tourOperator->is_active ? 'warning' : 'success' }}"
                                        wire:click="toggleStatus">
                                    <i class="fas fa-{{ $tourOperator->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    {{ $tourOperator->is_active ? 'Désactiver' : 'Activer' }}
                                </button>

                                @if($tourOperator->latitude && $tourOperator->longitude)
                                    <a href="https://maps.google.com/?q={{ $tourOperator->latitude }},{{ $tourOperator->longitude }}"
                                       target="_blank" class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-map-marker-alt"></i>
                                        Localiser
                                    </a>
                                @endif

                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        wire:click="delete"
                                        onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet opérateur ?')">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Utilisateurs -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="mov-card shadow-sm border-0">
                <div class="mov-card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="mov-card-title mb-0">
                            <i class="fas fa-users me-2"></i>
                            Utilisateurs du Tour Operator
                        </h4>
                        <button type="button"
                                class="btn btn-primary btn-sm"
                                data-bs-toggle="modal"
                                data-bs-target="#addUserModal">
                            <i class="fas fa-plus me-1"></i>
                            Ajouter un utilisateur
                        </button>
                    </div>
                    @livewire('admin.tour-operator.user-list', ['tourOperatorId' => $tourOperator->id], key('user-list-'.$tourOperator->id))
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'ajout d'utilisateur -->
    @livewire('admin.tour-operator.user-form-modal', ['tourOperatorId' => $tourOperator->id])
</div>