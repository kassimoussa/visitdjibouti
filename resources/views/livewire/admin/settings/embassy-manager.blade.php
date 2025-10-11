<div>
    <!-- Messages de feedback -->
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

    <!-- Header avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Ambassades</h4>
            <p class="text-muted mb-0">Gérer les ambassades étrangères et djiboutiennes</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="fas fa-plus me-2"></i>
            Nouvelle ambassade
        </button>
    </div>

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="search" class="form-label">Recherche</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Rechercher une ambassade..."
                               wire:model.live="search" id="search">
                        @if($search)
                            <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="filterType" class="form-label">Type</label>
                    <select class="form-select" wire:model.live="filterType" id="filterType">
                        <option value="">Tous les types</option>
                        @foreach($types as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="filterLocale" class="form-label">Langue</label>
                    <select class="form-select" wire:model.live="filterLocale" id="filterLocale">
                        @foreach($availableLocales as $locale)
                            <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des ambassades -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-building me-2"></i>
                Liste des ambassades ({{ $embassies->total() }})
            </h6>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ambassade</th>
                        <th>Type</th>
                        <th>Contact</th>
                        <th>Statut</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($embassies as $embassy)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-flag fa-lg text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $embassy->getTranslatedName($filterLocale) }}</h6>
                                        @if($embassy->getTranslatedAmbassadorName($filterLocale))
                                            <small class="text-muted">{{ $embassy->getTranslatedAmbassadorName($filterLocale) }}</small>
                                        @endif
                                        @if($embassy->country_code)
                                            <span class="badge bg-secondary ms-1">{{ $embassy->country_code }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $embassy->type === 'foreign_in_djibouti' ? 'info' : 'success' }}">
                                    {{ $embassy->type_label }}
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    @if($embassy->phones)
                                        <div><i class="fas fa-phone me-1"></i>{{ $embassy->phones }}</div>
                                    @endif
                                    @if($embassy->emails)
                                        <div><i class="fas fa-envelope me-1"></i>{{ Str::limit($embassy->emails, 30) }}</div>
                                    @endif
                                    @if($embassy->website)
                                        <div>
                                            <i class="fas fa-globe me-1"></i>
                                            <a href="{{ $embassy->website_url }}" target="_blank" class="text-decoration-none">
                                                Site web
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           {{ $embassy->is_active ? 'checked' : '' }}
                                           wire:click="toggleStatus({{ $embassy->id }})">
                                    <label class="form-check-label">
                                        <span class="badge bg-{{ $embassy->is_active ? 'success' : 'secondary' }}">
                                            {{ $embassy->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary"
                                            wire:click="openEditModal({{ $embassy->id }})"
                                            title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $embassy->is_active ? 'warning' : 'success' }}"
                                            wire:click="toggleStatus({{ $embassy->id }})"
                                            title="{{ $embassy->is_active ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $embassy->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                            wire:click="delete({{ $embassy->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer cette ambassade ?"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-building fa-3x mb-3 d-block"></i>
                                    @if($search || $filterType)
                                        <h6>Aucune ambassade trouvée</h6>
                                        <p class="mb-0">Essayez de modifier vos filtres de recherche</p>
                                    @else
                                        <h6>Aucune ambassade</h6>
                                        <p class="mb-0">
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    wire:click="openCreateModal">
                                                Créer la première ambassade
                                            </button>
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($embassies->hasPages())
            <div class="card-footer">
                {{ $embassies->links() }}
            </div>
        @endif
    </div>

    <!-- Modal pour créer/éditer une ambassade -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);" wire:ignore.self>
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $modalTitle }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <!-- Onglets de langue -->
                            <ul class="nav nav-tabs mb-3" id="languageTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if($currentLocale === 'fr') active @endif" 
                                            id="fr-tab" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#fr-content" 
                                            type="button" 
                                            role="tab"
                                            wire:click="switchLocale('fr')">
                                        FR <span class="text-danger">*</span>
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link @if($currentLocale === 'en') active @endif"
                                            id="en-tab"
                                            data-bs-toggle="tab"
                                            data-bs-target="#en-content"
                                            type="button"
                                            role="tab"
                                            wire:click="switchLocale('en')">
                                        EN
                                    </button>
                                </li>
                            </ul>

                            <!-- Contenu des onglets -->
                            <div class="tab-content">
                                <!-- Onglet Français -->
                                <div class="tab-pane fade @if($currentLocale === 'fr') show active @endif" 
                                     id="fr-content" 
                                     role="tabpanel" 
                                     aria-labelledby="fr-tab">
                                    <div class="mb-4">
                                        <h6 class="text-primary">Informations en Français <span class="text-danger">*</span></h6>
                                        
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Nom de l'ambassade <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       class="form-control @error('translations.fr.name') is-invalid @enderror"
                                                       wire:model="translations.fr.name"
                                                       placeholder="Ex: Ambassade de l'État de Palestine">
                                                @error('translations.fr.name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Nom de l'ambassadeur</label>
                                                <input type="text" 
                                                       class="form-control @error('translations.fr.ambassador_name') is-invalid @enderror"
                                                       wire:model="translations.fr.ambassador_name"
                                                       placeholder="Ex: Kamil Abdallah Gazaz, Ambassadeur">
                                                @error('translations.fr.ambassador_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-8 mb-3">
                                                <label class="form-label">Adresse</label>
                                                <textarea class="form-control @error('translations.fr.address') is-invalid @enderror"
                                                          wire:model="translations.fr.address"
                                                          rows="3"
                                                          placeholder="Adresse complète de l'ambassade"></textarea>
                                                @error('translations.fr.address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Boîte postale</label>
                                                <input type="text" 
                                                       class="form-control @error('translations.fr.postal_box') is-invalid @enderror"
                                                       wire:model="translations.fr.postal_box"
                                                       placeholder="Ex: BP 1234">
                                                @error('translations.fr.postal_box')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Onglet Anglais -->
                                <div class="tab-pane fade @if($currentLocale === 'en') show active @endif" 
                                     id="en-content" 
                                     role="tabpanel" 
                                     aria-labelledby="en-tab">
                                    <div class="mb-4">
                                        <h6 class="text-primary">Informations en Anglais</h6>
                                        
                                        <div class="row">
                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Embassy name</label>
                                                <input type="text" 
                                                       class="form-control @error('translations.en.name') is-invalid @enderror"
                                                       wire:model="translations.en.name"
                                                       placeholder="Ex: Embassy of the State of Palestine">
                                                @error('translations.en.name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-12 mb-3">
                                                <label class="form-label">Ambassador name</label>
                                                <input type="text" 
                                                       class="form-control @error('translations.en.ambassador_name') is-invalid @enderror"
                                                       wire:model="translations.en.ambassador_name"
                                                       placeholder="Ex: Kamil Abdallah Gazaz, Ambassador">
                                                @error('translations.en.ambassador_name')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-8 mb-3">
                                                <label class="form-label">Address</label>
                                                <textarea class="form-control @error('translations.en.address') is-invalid @enderror"
                                                          wire:model="translations.en.address"
                                                          rows="3"
                                                          placeholder="Complete embassy address"></textarea>
                                                @error('translations.en.address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Postal box</label>
                                                <input type="text" 
                                                       class="form-control @error('translations.en.postal_box') is-invalid @enderror"
                                                       wire:model="translations.en.postal_box"
                                                       placeholder="Ex: PO Box 1234">
                                                @error('translations.en.postal_box')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations générales (affichées seulement pour le français) -->
                            @if($currentLocale === 'fr')
                                <hr>
                                <h6 class="text-primary mb-3">Informations générales</h6>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Type d'ambassade <span class="text-danger">*</span></label>
                                        <select class="form-select @error('type') is-invalid @enderror"
                                                wire:model="type">
                                            @foreach($types as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Code pays</label>
                                        <input type="text" 
                                               class="form-control @error('country_code') is-invalid @enderror"
                                               wire:model="country_code"
                                               placeholder="Ex: PAL, USA, FRA">
                                        @error('country_code')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Téléphones</label>
                                        <input type="text" 
                                               class="form-control @error('phones') is-invalid @enderror"
                                               wire:model="phones"
                                               placeholder="21 35 49 23|21 35 82 05">
                                        <small class="text-muted">Séparer plusieurs numéros par |</small>
                                        @error('phones')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Emails</label>
                                        <input type="text" 
                                               class="form-control @error('emails') is-invalid @enderror"
                                               wire:model="emails"
                                               placeholder="contact@embassy.dj|info@embassy.dj">
                                        <small class="text-muted">Séparer plusieurs emails par |</small>
                                        @error('emails')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Fax</label>
                                        <input type="text" 
                                               class="form-control @error('fax') is-invalid @enderror"
                                               wire:model="fax"
                                               placeholder="21 35 82 05">
                                        @error('fax')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Site web</label>
                                        <input type="text" 
                                               class="form-control @error('website') is-invalid @enderror"
                                               wire:model="website"
                                               placeholder="www.djibouti.diplo.de ou https://embassy.example.com">
                                        @error('website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">L.D</label>
                                        <input type="text" 
                                               class="form-control @error('ld') is-invalid @enderror"
                                               wire:model="ld"
                                               placeholder="21 35.82.05|21 35 27 52">
                                        <small class="text-muted">Séparer plusieurs L.D par |</small>
                                        @error('ld')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Latitude</label>
                                        <input type="number" 
                                               class="form-control @error('latitude') is-invalid @enderror"
                                               wire:model="latitude"
                                               step="0.00000001"
                                               placeholder="11.5889">
                                        @error('latitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Longitude</label>
                                        <input type="number" 
                                               class="form-control @error('longitude') is-invalid @enderror"
                                               wire:model="longitude"
                                               step="0.00000001"
                                               placeholder="43.1467">
                                        @error('longitude')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-4 mb-3">
                                        <div class="form-check form-switch mt-4">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   wire:model="is_active"
                                                   id="is_active">
                                            <label class="form-check-label" for="is_active">
                                                Ambassade active
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                Annuler
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-save me-2"></i>
                                    {{ $modalMode === 'create' ? 'Créer' : 'Modifier' }}
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Enregistrement...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>