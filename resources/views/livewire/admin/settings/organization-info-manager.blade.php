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

    <!-- Header -->
    <div class="mb-4">
        <h6 class="text-primary mb-2">Informations de l'Organisation</h6>
        <p class="text-muted mb-0 small">Gérer les informations de l'organisme de tourisme par sections</p>
    </div>

    <!-- Cards séparées pour chaque type d'information -->
    @php
        $currentOrg = \App\Models\OrganizationInfo::with(['translations', 'logo', 'links.translations'])->first();
    @endphp
    
    <div class="row">
        <!-- Card 1: Informations de base -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2 text-primary"></i>
                        Informations de base
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openModal('basic')">
                        <i class="fas fa-edit me-1"></i>
                        Modifier
                    </button>
                </div>
                <div class="card-body">
                    @if($currentOrg && ($currentOrg->name || $currentOrg->description))
                        <div class="mb-2">
                            <strong class="text-primary">{{ $currentOrg->name ?: 'Nom non défini' }}</strong>
                        </div>
                        @if($currentOrg->description)
                            <p class="text-muted small mb-0">{{ Str::limit($currentOrg->description, 100) }}</p>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-info-circle fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0 small">Aucune information de base</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 2: Logo -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-image me-2 text-success"></i>
                        Logo
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-success" wire:click="openModal('logo')">
                        <i class="fas fa-edit me-1"></i>
                        Modifier
                    </button>
                </div>
                <div class="card-body text-center">
                    @if($logo_id)
                        @php $currentLogo = \App\Models\Media::find($logo_id); @endphp
                        @if($currentLogo)
                            <img src="{{ asset($currentLogo->path) }}" 
                                 alt="Logo" 
                                 class="img-fluid" 
                                 style="max-height: 100px;">
                        @endif
                    @else
                        <div class="text-muted py-3">
                            <i class="fas fa-image fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0 small">Aucun logo défini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 3: Informations de contact -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-address-card me-2 text-info"></i>
                        Contact
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-info" wire:click="openModal('contact')">
                        <i class="fas fa-edit me-1"></i>
                        Modifier
                    </button>
                </div>
                <div class="card-body">
                    @if($currentOrg && ($currentOrg->email || $currentOrg->phone || $currentOrg->address))
                        @if($currentOrg->email)
                            <div class="mb-2">
                                <i class="fas fa-envelope me-1 text-muted"></i>
                                <span class="small">{{ $currentOrg->email }}</span>
                            </div>
                        @endif
                        @if($currentOrg->phone)
                            <div class="mb-2">
                                <i class="fas fa-phone me-1 text-muted"></i>
                                <span class="small">{{ $currentOrg->phone }}</span>
                            </div>
                        @endif
                        @if($currentOrg->address)
                            <div class="mb-0">
                                <i class="fas fa-map-marker-alt me-1 text-muted"></i>
                                <span class="small">{{ Str::limit($currentOrg->address, 80) }}</span>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-address-card fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0 small">Aucune information de contact</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 4: Horaires -->
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2 text-warning"></i>
                        Horaires d'ouverture
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-warning" wire:click="openModal('hours')">
                        <i class="fas fa-edit me-1"></i>
                        Modifier
                    </button>
                </div>
                <div class="card-body">
                    @if($currentOrg && ($currentOrg->opening_hours_translated || $currentOrg->opening_hours))
                        <div class="small">
                            {{ $currentOrg->opening_hours_translated ?: $currentOrg->opening_hours }}
                        </div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-clock fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0 small">Aucun horaire défini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Card 5: Liens et réseaux sociaux -->
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-link me-2 text-danger"></i>
                        Liens et réseaux sociaux
                        @if($currentOrg && $currentOrg->links->count() > 0)
                            <span class="badge bg-secondary ms-2">{{ $currentOrg->links->count() }}</span>
                        @endif
                    </h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openCreateLinkModal">
                        <i class="fas fa-plus me-1"></i>
                        Ajouter un lien
                    </button>
                </div>
                <div class="card-body">
                    @if($currentOrg && $currentOrg->links->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($currentOrg->links->sortBy('order') as $link)
                                <div class="list-group-item d-flex justify-content-between align-items-center p-3">
                                    <div class="d-flex align-items-center flex-grow-1">
                                        @php
                                            $platformIcons = [
                                                'website' => 'fas fa-globe',
                                                'facebook' => 'fab fa-facebook',
                                                'instagram' => 'fab fa-instagram',
                                                'twitter' => 'fab fa-twitter',
                                                'linkedin' => 'fab fa-linkedin',
                                                'youtube' => 'fab fa-youtube',
                                                'tiktok' => 'fab fa-tiktok',
                                                'whatsapp' => 'fab fa-whatsapp'
                                            ];
                                            $platformColors = [
                                                'website' => 'primary',
                                                'facebook' => 'primary',
                                                'instagram' => 'danger',
                                                'twitter' => 'info',
                                                'linkedin' => 'primary',
                                                'youtube' => 'danger',
                                                'tiktok' => 'dark',
                                                'whatsapp' => 'success'
                                            ];
                                        @endphp
                                        <i class="{{ $platformIcons[$link->platform] ?? 'fas fa-link' }} text-{{ $platformColors[$link->platform] ?? 'secondary' }} me-3 fa-lg"></i>
                                        <div class="flex-grow-1">
                                            <div class="fw-bold small">{{ $link->name ?: ucfirst($link->platform) }}</div>
                                            <div class="text-muted small">
                                                <a href="{{ $link->url }}" target="_blank" class="text-decoration-none text-muted">
                                                    {{ parse_url($link->url, PHP_URL_HOST) ?: $link->url }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-outline-primary" wire:click="openEditLinkModal({{ $link->id }})" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger" wire:click="deleteLink({{ $link->id }})" 
                                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce lien ?')" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-link fa-2x mb-2 d-block opacity-50"></i>
                            <p class="mb-0 small">Aucun lien défini</p>
                            <button type="button" class="btn btn-outline-primary btn-sm mt-2" wire:click="openCreateLinkModal">
                                <i class="fas fa-plus me-1"></i>
                                Ajouter votre premier lien
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if(!$currentOrg)
        <div class="text-center text-muted py-5">
            <i class="fas fa-building fa-3x mb-3 d-block opacity-50"></i>
            <h6>Aucune information d'organisation</h6>
            <p class="mb-3">Commencez par configurer les informations de votre organisme</p>
            <button type="button" class="btn btn-primary" wire:click="openModal('basic')">
                <i class="fas fa-plus me-2"></i>
                Commencer la configuration
            </button>
        </div>
    @endif

    <!-- Modal pour éditer les informations -->
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
                            <!-- Modal pour informations de base -->
                            @if($modalType === 'basic')
                                <!-- Onglets de langue -->
                                <ul class="nav nav-tabs mb-3">
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'fr') active @endif" type="button" wire:click="switchLocale('fr')">
                                            FR <span class="text-danger">*</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'en') active @endif" type="button" wire:click="switchLocale('en')">EN</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'ar') active @endif" type="button" wire:click="switchLocale('ar')">AR</button>
                                    </li>
                                </ul>

                                <!-- Contenu des onglets -->
                                <div class="tab-content">
                                    <!-- Onglet Français -->
                                    <div class="tab-pane fade @if($currentLocale === 'fr') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">Nom de l'organisation <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('translations.fr.name') is-invalid @enderror"
                                                   wire:model="translations.fr.name" placeholder="Ex: Office National du Tourisme de Djibouti">
                                            @error('translations.fr.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control @error('translations.fr.description') is-invalid @enderror"
                                                      wire:model="translations.fr.description" rows="4" placeholder="Description de l'organisation..."></textarea>
                                            @error('translations.fr.description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <!-- Onglet Anglais -->
                                    <div class="tab-pane fade @if($currentLocale === 'en') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">Organization name</label>
                                            <input type="text" class="form-control @error('translations.en.name') is-invalid @enderror"
                                                   wire:model="translations.en.name" placeholder="Ex: National Tourism Office of Djibouti">
                                            @error('translations.en.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control @error('translations.en.description') is-invalid @enderror"
                                                      wire:model="translations.en.description" rows="4" placeholder="Organization description..."></textarea>
                                            @error('translations.en.description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <!-- Onglet Arabe -->
                                    <div class="tab-pane fade @if($currentLocale === 'ar') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">اسم المنظمة</label>
                                            <input type="text" class="form-control @error('translations.ar.name') is-invalid @enderror"
                                                   wire:model="translations.ar.name" placeholder="مثال: المكتب الوطني للسياحة في جيبوتي" dir="rtl">
                                            @error('translations.ar.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">الوصف</label>
                                            <textarea class="form-control @error('translations.ar.description') is-invalid @enderror"
                                                      wire:model="translations.ar.description" rows="4" placeholder="وصف المنظمة..." dir="rtl"></textarea>
                                            @error('translations.ar.description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                            <!-- Modal pour logo -->
                            @elseif($modalType === 'logo')
                                <div class="text-center mb-4">
                                    @if($logo_id)
                                        @php $logoMedia = \App\Models\Media::find($logo_id); @endphp
                                        @if($logoMedia)
                                            <img src="{{ asset($logoMedia->path) }}" alt="Logo actuel" class="img-fluid mb-3" style="max-height: 200px;">
                                            <p class="text-muted small">Logo actuel</p>
                                        @endif
                                    @else
                                        <div class="text-muted py-4">
                                            <i class="fas fa-image fa-3x mb-3 d-block opacity-50"></i>
                                            <p>Aucun logo sélectionné</p>
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Sélectionner un logo</label>
                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-outline-primary" wire:click="openLogoSelector">
                                            <i class="fas fa-image me-2"></i>
                                            Choisir un logo
                                        </button>
                                        @if($logo_id)
                                            <button type="button" class="btn btn-outline-danger" wire:click="$set('logo_id', null)">
                                                <i class="fas fa-times"></i>
                                                Supprimer
                                            </button>
                                        @endif
                                    </div>
                                </div>

                            <!-- Modal pour contact -->
                            @elseif($modalType === 'contact')
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           wire:model="email" placeholder="contact@visitdjibouti.dj">
                                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Téléphone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror"
                                           wire:model="phone" placeholder="+253 21 35 30 52">
                                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Adresse</label>
                                    <textarea class="form-control @error('address') is-invalid @enderror"
                                              wire:model="address" rows="3" placeholder="Boulevard du Maréchal Joffre, Djibouti"></textarea>
                                    @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                            <!-- Modal pour horaires -->
                            @elseif($modalType === 'hours')
                                <!-- Onglets de langue -->
                                <ul class="nav nav-tabs mb-3">
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'fr') active @endif" type="button" wire:click="switchLocale('fr')">FR</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'en') active @endif" type="button" wire:click="switchLocale('en')">EN</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'ar') active @endif" type="button" wire:click="switchLocale('ar')">AR</button>
                                    </li>
                                </ul>

                                <div class="mb-3">
                                    <label class="form-label">Horaires par défaut</label>
                                    <textarea class="form-control @error('opening_hours') is-invalid @enderror"
                                              wire:model="opening_hours" rows="2" placeholder="Lun-Ven: 8h-17h, Sam: 8h-12h"></textarea>
                                    <small class="text-muted">Ces horaires seront utilisés si aucune traduction n'est fournie</small>
                                    @error('opening_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <!-- Horaires traduits par onglets -->
                                <div class="tab-content">
                                    <!-- Onglet Français -->
                                    <div class="tab-pane fade @if($currentLocale === 'fr') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">Horaires en français</label>
                                            <textarea class="form-control @error('translations.fr.opening_hours_translated') is-invalid @enderror"
                                                      wire:model="translations.fr.opening_hours_translated" rows="2" 
                                                      placeholder="Lundi-Vendredi: 8h-17h, Samedi: 8h-12h"></textarea>
                                            @error('translations.fr.opening_hours_translated')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <!-- Onglet Anglais -->
                                    <div class="tab-pane fade @if($currentLocale === 'en') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">Opening hours in English</label>
                                            <textarea class="form-control @error('translations.en.opening_hours_translated') is-invalid @enderror"
                                                      wire:model="translations.en.opening_hours_translated" rows="2" 
                                                      placeholder="Monday-Friday: 8AM-5PM, Saturday: 8AM-12PM"></textarea>
                                            @error('translations.en.opening_hours_translated')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <!-- Onglet Arabe -->
                                    <div class="tab-pane fade @if($currentLocale === 'ar') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">ساعات العمل بالعربية</label>
                                            <textarea class="form-control @error('translations.ar.opening_hours_translated') is-invalid @enderror"
                                                      wire:model="translations.ar.opening_hours_translated" rows="2" 
                                                      placeholder="الإثنين-الجمعة: 8ص-5م، السبت: 8ص-12م" dir="rtl"></textarea>
                                            @error('translations.ar.opening_hours_translated')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                            <!-- Modal pour créer/modifier un lien -->
                            @elseif($modalType === 'links')
                                <!-- Informations du lien -->
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">URL <span class="text-danger">*</span></label>
                                        <input type="url" class="form-control @error('currentLink.url') is-invalid @enderror"
                                               wire:model="currentLink.url" placeholder="https://example.com">
                                        @error('currentLink.url')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Plateforme</label>
                                        <select class="form-select @error('currentLink.platform') is-invalid @enderror"
                                                wire:model="currentLink.platform">
                                            @foreach($availablePlatforms as $key => $label)
                                                <option value="{{ $key }}">{{ $label }}</option>
                                            @endforeach
                                        </select>
                                        @error('currentLink.platform')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>

                                <!-- Onglets de langue pour les noms -->
                                <ul class="nav nav-tabs mb-3">
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'fr') active @endif" type="button" wire:click="switchLocale('fr')">
                                            FR <span class="text-danger">*</span>
                                        </button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'en') active @endif" type="button" wire:click="switchLocale('en')">EN</button>
                                    </li>
                                    <li class="nav-item">
                                        <button class="nav-link @if($currentLocale === 'ar') active @endif" type="button" wire:click="switchLocale('ar')">AR</button>
                                    </li>
                                </ul>

                                <!-- Contenu des onglets pour les noms -->
                                <div class="tab-content">
                                    <!-- Onglet Français -->
                                    <div class="tab-pane fade @if($currentLocale === 'fr') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">Nom du lien en français <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('currentLink.translations.fr.name') is-invalid @enderror"
                                                   wire:model="currentLink.translations.fr.name" placeholder="Site web officiel">
                                            @error('currentLink.translations.fr.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <!-- Onglet Anglais -->
                                    <div class="tab-pane fade @if($currentLocale === 'en') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">Link name in English</label>
                                            <input type="text" class="form-control @error('currentLink.translations.en.name') is-invalid @enderror"
                                                   wire:model="currentLink.translations.en.name" placeholder="Official website">
                                            @error('currentLink.translations.en.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>

                                    <!-- Onglet Arabe -->
                                    <div class="tab-pane fade @if($currentLocale === 'ar') show active @endif">
                                        <div class="mb-3">
                                            <label class="form-label">اسم الرابط بالعربية</label>
                                            <input type="text" class="form-control @error('currentLink.translations.ar.name') is-invalid @enderror"
                                                   wire:model="currentLink.translations.ar.name" placeholder="الموقع الرسمي" dir="rtl">
                                            @error('currentLink.translations.ar.name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Annuler</button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove><i class="fas fa-save me-2"></i>Enregistrer</span>
                                <span wire:loading><i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de sélection de médias -->
    @livewire('admin.media-selector-modal')
</div>