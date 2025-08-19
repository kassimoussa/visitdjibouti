<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Opérateurs de Tour</h1>
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="fas fa-plus"></i> Nouvel opérateur
        </button>
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

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" wire:model.live="search" placeholder="Nom, description, services...">
                </div>
                <div class="col-md-2">
                    <label for="filterCertification" class="form-label">Certification</label>
                    <select class="form-select" id="filterCertification" wire:model.live="filterCertification">
                        <option value="">Toutes</option>
                        @foreach($certificationTypes as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterPriceRange" class="form-label">Gamme de prix</label>
                    <select class="form-select" id="filterPriceRange" wire:model.live="filterPriceRange">
                        <option value="">Toutes</option>
                        @foreach($priceRanges as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterFeatured" class="form-label">Mis en avant</label>
                    <select class="form-select" id="filterFeatured" wire:model.live="filterFeatured">
                        <option value="">Tous</option>
                        <option value="1">Oui</option>
                        <option value="0">Non</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterLocale" class="form-label">Langue</label>
                    <select class="form-select" id="filterLocale" wire:model.live="filterLocale">
                        @foreach($availableLocales as $locale)
                            <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des opérateurs -->
    <div class="card">
        <div class="card-body">
            @if($tourOperators->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Nom</th>
                                <th>Certification</th>
                                <th>Prix</th>
                                <th>Contact</th>
                                <th>Services</th>
                                <th>Note</th>
                                <th>Statut</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tourOperators as $operator)
                                @php
                                    $translation = $operator->translations->firstWhere('locale', $filterLocale) 
                                        ?? $operator->translations->firstWhere('locale', 'fr') 
                                        ?? $operator->translations->first();
                                @endphp
                                <tr>
                                    <td>
                                        @if($operator->logo)
                                            <img src="{{ $operator->logo->thumbnail_url ?: $operator->logo->url }}" 
                                                 alt="Logo" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $translation->name ?? 'Sans nom' }}</strong>
                                            @if($operator->featured)
                                                <span class="badge bg-warning text-dark ms-1">
                                                    <i class="fas fa-star"></i> Mis en avant
                                                </span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $operator->license_number }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $operator->certification_label }}</span>
                                    </td>
                                    <td>
                                        @if($operator->min_price || $operator->max_price)
                                            <div>
                                                @if($operator->min_price && $operator->max_price)
                                                    {{ $operator->min_price }} - {{ $operator->max_price }} {{ $operator->currency }}
                                                @elseif($operator->min_price)
                                                    À partir de {{ $operator->min_price }} {{ $operator->currency }}
                                                @else
                                                    Jusqu'à {{ $operator->max_price }} {{ $operator->currency }}
                                                @endif
                                            </div>
                                        @endif
                                        <small class="text-muted">{{ $operator->price_range_label }}</small>
                                    </td>
                                    <td>
                                        @if($operator->phone)
                                            <div><i class="fas fa-phone"></i> {{ $operator->phone }}</div>
                                        @endif
                                        @if($operator->email)
                                            <div><i class="fas fa-envelope"></i> {{ $operator->email }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($operator->services->count() > 0)
                                            @foreach($operator->services->take(2) as $service)
                                                <span class="badge bg-secondary me-1">{{ $service->service_label }}</span>
                                            @endforeach
                                            @if($operator->services->count() > 2)
                                                <span class="text-muted">+{{ $operator->services->count() - 2 }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @if($operator->rating)
                                            <div class="d-flex align-items-center">
                                                <span class="me-1">{{ number_format($operator->rating, 1) }}</span>
                                                <div class="text-warning">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $operator->rating)
                                                            <i class="fas fa-star"></i>
                                                        @elseif($i - 0.5 <= $operator->rating)
                                                            <i class="fas fa-star-half-alt"></i>
                                                        @else
                                                            <i class="far fa-star"></i>
                                                        @endif
                                                    @endfor
                                                </div>
                                            </div>
                                            <small class="text-muted">({{ $operator->reviews_count }} avis)</small>
                                        @else
                                            <span class="text-muted">Pas d'avis</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($operator->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    wire:click="openEditModal({{ $operator->id }})" 
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-{{ $operator->is_active ? 'warning' : 'success' }}" 
                                                    wire:click="toggleStatus({{ $operator->id }})" 
                                                    title="{{ $operator->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $operator->is_active ? 'eye-slash' : 'eye' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-{{ $operator->featured ? 'secondary' : 'warning' }}" 
                                                    wire:click="toggleFeatured({{ $operator->id }})" 
                                                    title="{{ $operator->featured ? 'Retirer de la mise en avant' : 'Mettre en avant' }}">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    wire:click="delete({{ $operator->id }})" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet opérateur ?')" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $tourOperators->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5>Aucun opérateur trouvé</h5>
                    <p class="text-muted">Aucun opérateur ne correspond à vos critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Modal de création/modification -->
    @if($showModal)
        <div class="modal show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $modalTitle }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit="save">
                            <div class="row">
                                <!-- Colonne gauche -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Informations générales</h6>
                                    
                                    <div class="mb-3">
                                        <label for="certification_type" class="form-label">Type de certification *</label>
                                        <select class="form-select @error('certification_type') is-invalid @enderror" 
                                                id="certification_type" wire:model="certification_type">
                                            @foreach($certificationTypes as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('certification_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="license_number" class="form-label">Numéro de licence</label>
                                        <input type="text" class="form-control @error('license_number') is-invalid @enderror" 
                                               id="license_number" wire:model="license_number">
                                        @error('license_number') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="phone" class="form-label">Téléphone</label>
                                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                                       id="phone" wire:model="phone">
                                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="fax" class="form-label">Fax</label>
                                                <input type="text" class="form-control @error('fax') is-invalid @enderror" 
                                                       id="fax" wire:model="fax">
                                                @error('fax') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" wire:model="email">
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="website" class="form-label">Site web</label>
                                        <input type="text" class="form-control @error('website') is-invalid @enderror" 
                                               id="website" wire:model="website">
                                        @error('website') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="languages_spoken" class="form-label">Langues parlées</label>
                                        <input type="text" class="form-control @error('languages_spoken') is-invalid @enderror" 
                                               id="languages_spoken" wire:model="languages_spoken" 
                                               placeholder="Français|Anglais|Arabe">
                                        @error('languages_spoken') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        <div class="form-text">Séparez les langues par le caractère |</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="address" class="form-label">Adresse</label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" wire:model="address" rows="2"></textarea>
                                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="latitude" class="form-label">Latitude</label>
                                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                                       id="latitude" wire:model="latitude">
                                                @error('latitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="longitude" class="form-label">Longitude</label>
                                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                                       id="longitude" wire:model="longitude">
                                                @error('longitude') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Logo -->
                                    <div class="mb-3">
                                        <label class="form-label">Logo</label>
                                        <div class="d-flex align-items-center gap-3">
                                            @if($logo_id)
                                                @php
                                                    $logo = \App\Models\Media::find($logo_id);
                                                @endphp
                                                @if($logo)
                                                    <img src="{{ $logo->thumbnail_url ?: $logo->url }}" alt="Logo" 
                                                         class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                                @endif
                                            @endif
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

                                <!-- Colonne droite -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold mb-3">Prix et services</h6>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="min_price" class="form-label">Prix min</label>
                                                <input type="number" step="0.01" class="form-control @error('min_price') is-invalid @enderror" 
                                                       id="min_price" wire:model="min_price">
                                                @error('min_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="max_price" class="form-label">Prix max</label>
                                                <input type="number" step="0.01" class="form-control @error('max_price') is-invalid @enderror" 
                                                       id="max_price" wire:model="max_price">
                                                @error('max_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="currency" class="form-label">Devise</label>
                                                <select class="form-select @error('currency') is-invalid @enderror" 
                                                        id="currency" wire:model="currency">
                                                    <option value="USD">USD</option>
                                                    <option value="EUR">EUR</option>
                                                    <option value="DJF">DJF</option>
                                                </select>
                                                @error('currency') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="price_range" class="form-label">Gamme de prix</label>
                                        <select class="form-select @error('price_range') is-invalid @enderror" 
                                                id="price_range" wire:model="price_range">
                                            @foreach($priceRanges as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('price_range') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="years_experience" class="form-label">Années d'expérience</label>
                                                <input type="number" class="form-control @error('years_experience') is-invalid @enderror" 
                                                       id="years_experience" wire:model="years_experience">
                                                @error('years_experience') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="max_group_size" class="form-label">Taille max du groupe</label>
                                                <input type="number" class="form-control @error('max_group_size') is-invalid @enderror" 
                                                       id="max_group_size" wire:model="max_group_size">
                                                @error('max_group_size') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="opening_hours" class="form-label">Horaires d'ouverture</label>
                                        <textarea class="form-control @error('opening_hours') is-invalid @enderror" 
                                                  id="opening_hours" wire:model="opening_hours" rows="2"></textarea>
                                        @error('opening_hours') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <!-- Services -->
                                    <div class="mb-3">
                                        <label class="form-label">Services proposés</label>
                                        <div class="row">
                                            @foreach($serviceTypes as $key => $label)
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="service_{{ $key }}" 
                                                               wire:model="selectedServices" 
                                                               value="{{ $key }}">
                                                        <label class="form-check-label" for="service_{{ $key }}">
                                                            {{ $label }}
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Options -->
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" wire:model="is_active">
                                            <label class="form-check-label" for="is_active">
                                                Actif
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="featured" wire:model="featured">
                                            <label class="form-check-label" for="featured">
                                                Mis en avant
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="emergency_contact_available" wire:model="emergency_contact_available">
                                            <label class="form-check-label" for="emergency_contact_available">
                                                Contact d'urgence disponible
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Traductions -->
                            <hr>
                            <h6 class="fw-bold mb-3">Traductions</h6>
                            
                            <!-- Onglets de langues -->
                            <ul class="nav nav-tabs mb-3">
                                @foreach($availableLocales as $locale)
                                    <li class="nav-item">
                                        <button type="button" class="nav-link {{ $currentLocale === $locale ? 'active' : '' }}" 
                                                wire:click="switchLocale('{{ $locale }}')">
                                            {{ strtoupper($locale) }}
                                            @if($locale === 'fr')
                                                <span class="text-danger">*</span>
                                            @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Formulaires de traduction -->
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name_{{ $currentLocale }}" class="form-label">
                                            Nom {{ $currentLocale === 'fr' ? '*' : '' }}
                                        </label>
                                        <input type="text" class="form-control @error('translations.'.$currentLocale.'.name') is-invalid @enderror" 
                                               id="name_{{ $currentLocale }}" wire:model="translations.{{ $currentLocale }}.name">
                                        @error('translations.'.$currentLocale.'.name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="address_translated_{{ $currentLocale }}" class="form-label">Adresse traduite</label>
                                        <textarea class="form-control @error('translations.'.$currentLocale.'.address_translated') is-invalid @enderror" 
                                                  id="address_translated_{{ $currentLocale }}" 
                                                  wire:model="translations.{{ $currentLocale }}.address_translated" rows="2"></textarea>
                                        @error('translations.'.$currentLocale.'.address_translated') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="services_{{ $currentLocale }}" class="form-label">Description des services</label>
                                        <textarea class="form-control @error('translations.'.$currentLocale.'.services') is-invalid @enderror" 
                                                  id="services_{{ $currentLocale }}" 
                                                  wire:model="translations.{{ $currentLocale }}.services" rows="3"></textarea>
                                        @error('translations.'.$currentLocale.'.services') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="specialties_{{ $currentLocale }}" class="form-label">Spécialités</label>
                                        <textarea class="form-control @error('translations.'.$currentLocale.'.specialties') is-invalid @enderror" 
                                                  id="specialties_{{ $currentLocale }}" 
                                                  wire:model="translations.{{ $currentLocale }}.specialties" rows="3"></textarea>
                                        @error('translations.'.$currentLocale.'.specialties') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="description_{{ $currentLocale }}" class="form-label">Description</label>
                                        <textarea class="form-control @error('translations.'.$currentLocale.'.description') is-invalid @enderror" 
                                                  id="description_{{ $currentLocale }}" 
                                                  wire:model="translations.{{ $currentLocale }}.description" rows="4"></textarea>
                                        @error('translations.'.$currentLocale.'.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="about_text_{{ $currentLocale }}" class="form-label">À propos</label>
                                        <textarea class="form-control @error('translations.'.$currentLocale.'.about_text') is-invalid @enderror" 
                                                  id="about_text_{{ $currentLocale }}" 
                                                  wire:model="translations.{{ $currentLocale }}.about_text" rows="4"></textarea>
                                        @error('translations.'.$currentLocale.'.about_text') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="booking_conditions_{{ $currentLocale }}" class="form-label">Conditions de réservation</label>
                                        <textarea class="form-control @error('translations.'.$currentLocale.'.booking_conditions') is-invalid @enderror" 
                                                  id="booking_conditions_{{ $currentLocale }}" 
                                                  wire:model="translations.{{ $currentLocale }}.booking_conditions" rows="4"></textarea>
                                        @error('translations.'.$currentLocale.'.booking_conditions') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">Annuler</button>
                        <button type="button" class="btn btn-primary" wire:click="save">
                            {{ $modalMode === 'create' ? 'Créer' : 'Modifier' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Composant de sélection de média -->
    <livewire:admin.media-selector-modal />
</div>