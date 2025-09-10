<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tour-operators.index') }}">Opérateurs de tour</a></li>
                    <li class="breadcrumb-item active">{{ $this->getTranslation()->name ?: 'Détails' }}</li>
                </ol>
            </nav>
            <h1>{{ $this->getTranslation()->name ?: 'Tour Operator' }}</h1>
        </div>
        <div>
            <button type="button" class="btn btn-outline-primary me-2" wire:click="redirectToEdit">
                <i class="fas fa-edit"></i> Modifier
            </button>
            <button type="button" class="btn btn-outline-{{ $tourOperator->is_active ? 'warning' : 'success' }}" 
                    wire:click="toggleStatus" class="me-2">
                <i class="fas fa-{{ $tourOperator->is_active ? 'eye-slash' : 'eye' }}"></i>
                {{ $tourOperator->is_active ? 'Désactiver' : 'Activer' }}
            </button>
            <button type="button" class="btn btn-outline-danger" 
                    wire:click="delete" 
                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet opérateur ? Cette action est irréversible.')">
                <i class="fas fa-trash"></i> Supprimer
            </button>
        </div>
    </div>

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
    <div class="card mb-4">
        <div class="card-body py-2">
            <div class="d-flex align-items-center">
                <span class="me-3"><strong>Langue d'affichage:</strong></span>
                @foreach($availableLocales as $locale)
                    <button type="button" 
                            class="btn btn-{{ $activeLocale === $locale ? 'primary' : 'outline-primary' }} btn-sm me-2"
                            wire:click="setActiveLocale('{{ $locale }}')">
                        {{ strtoupper($locale) }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-lg-8">
            <!-- Informations générales -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle text-primary"></i>
                        Informations générales
                    </h5>
                    <div>
                        @if($tourOperator->featured)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star"></i> Mis en avant
                            </span>
                        @endif
                        <span class="badge bg-{{ $tourOperator->is_active ? 'success' : 'secondary' }}">
                            {{ $tourOperator->is_active ? 'Actif' : 'Inactif' }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    @php $translation = $this->getTranslation(); @endphp
                    
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Nom:</strong></div>
                        <div class="col-sm-9">{{ $translation->name ?: 'Non renseigné' }}</div>
                    </div>

                    @if($translation->description)
                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Description:</strong></div>
                        <div class="col-sm-9">{{ $translation->description }}</div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Slug:</strong></div>
                        <div class="col-sm-9">
                            <code>{{ $tourOperator->slug }}</code>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-3"><strong>Créé le:</strong></div>
                        <div class="col-sm-9">{{ $tourOperator->created_at->format('d/m/Y à H:i') }}</div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3"><strong>Modifié le:</strong></div>
                        <div class="col-sm-9">{{ $tourOperator->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Contact et adresse -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-address-book text-primary"></i>
                        Contact et localisation
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Téléphones -->
                    @if($this->getPhonesArray())
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Téléphones:</strong></div>
                            <div class="col-sm-9">
                                @foreach($this->getPhonesArray() as $phone)
                                    <div class="mb-1">
                                        <i class="fas fa-phone text-primary me-2"></i>
                                        <a href="tel:{{ $phone }}" class="text-decoration-none">{{ $phone }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Emails -->
                    @if($this->getEmailsArray())
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Emails:</strong></div>
                            <div class="col-sm-9">
                                @foreach($this->getEmailsArray() as $email)
                                    <div class="mb-1">
                                        <i class="fas fa-envelope text-primary me-2"></i>
                                        <a href="mailto:{{ $email }}" class="text-decoration-none">{{ $email }}</a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Site web -->
                    @if($tourOperator->website)
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Site web:</strong></div>
                            <div class="col-sm-9">
                                <i class="fas fa-globe text-primary me-2"></i>
                                <a href="{{ $tourOperator->website_url }}" target="_blank" class="text-decoration-none">
                                    {{ $tourOperator->website }}
                                    <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Adresse -->
                    @if($tourOperator->address || $translation->address_translated)
                        <div class="row mb-3">
                            <div class="col-sm-3"><strong>Adresse:</strong></div>
                            <div class="col-sm-9">
                                @if($translation->address_translated)
                                    {{ $translation->address_translated }}
                                @else
                                    {{ $tourOperator->address }}
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Coordonnées GPS -->
                    @if($tourOperator->latitude && $tourOperator->longitude)
                        <div class="row">
                            <div class="col-sm-3"><strong>Coordonnées GPS:</strong></div>
                            <div class="col-sm-9">
                                <i class="fas fa-map-marker-alt text-success me-2"></i>
                                {{ number_format($tourOperator->latitude, 6) }}, {{ number_format($tourOperator->longitude, 6) }}
                                <a href="https://maps.google.com/?q={{ $tourOperator->latitude }},{{ $tourOperator->longitude }}" 
                                   target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-map"></i> Voir sur la carte
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- POIs desservis -->
            @if($tourOperator->pois->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-map-signs text-primary"></i>
                            Points d'intérêt desservis ({{ $this->getServedPoisCount() }})
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($tourOperator->pois->take(6) as $poi)
                                @php 
                                    $poiTranslation = $poi->translations->firstWhere('locale', $activeLocale) 
                                        ?? $poi->translations->firstWhere('locale', 'fr') 
                                        ?? $poi->translations->first();
                                    $pivot = $poi->pivot;
                                @endphp
                                <div class="col-md-6 mb-3">
                                    <div class="border rounded p-3 h-100">
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
        </div>

        <!-- Colonne latérale -->
        <div class="col-lg-4">
            <!-- Logo -->
            @if($tourOperator->logo)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-image text-primary"></i>
                            Logo
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $tourOperator->logo->url }}" 
                             alt="Logo {{ $this->getTranslation()->name }}" 
                             class="img-fluid rounded"
                             style="max-height: 200px;">
                    </div>
                </div>
            @endif

            <!-- Statistiques -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-bar text-primary"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span>POIs desservis</span>
                        <span class="badge bg-primary">{{ $this->getServedPoisCount() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span>Images dans la galerie</span>
                        <span class="badge bg-info">{{ $this->getMediaCount() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <span>Traductions disponibles</span>
                        <span class="badge bg-success">{{ $tourOperator->translations->count() }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Mis en avant</span>
                        <span class="badge bg-{{ $tourOperator->featured ? 'warning' : 'secondary' }}">
                            {{ $tourOperator->featured ? 'Oui' : 'Non' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-tools text-primary"></i>
                        Actions rapides
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-outline-{{ $tourOperator->featured ? 'secondary' : 'warning' }}" 
                                wire:click="toggleFeatured">
                            <i class="fas fa-star"></i>
                            {{ $tourOperator->featured ? 'Retirer de la mise en avant' : 'Mettre en avant' }}
                        </button>
                        
                        @if($tourOperator->website)
                            <a href="{{ $tourOperator->website_url }}" target="_blank" class="btn btn-outline-info">
                                <i class="fas fa-external-link-alt"></i>
                                Visiter le site web
                            </a>
                        @endif
                        
                        @if($tourOperator->latitude && $tourOperator->longitude)
                            <a href="https://maps.google.com/?q={{ $tourOperator->latitude }},{{ $tourOperator->longitude }}" 
                               target="_blank" class="btn btn-outline-success">
                                <i class="fas fa-map-marker-alt"></i>
                                Localiser sur la carte
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>