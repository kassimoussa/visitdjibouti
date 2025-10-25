<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-8">
                <!-- Informations multilingues -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-language me-2"></i>Informations Multilingues
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Onglets pour les langues -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-fr" role="tab">FR</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-en" role="tab">EN</a>
                            </li>
                        </ul>

                        <!-- Contenu des onglets -->
                        <div class="tab-content">
                            <!-- Français -->
                            <div class="tab-pane fade show active" id="tab-fr" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Nom *</label>
                                    <input type="text" class="form-control @error('translations.fr.name') is-invalid @enderror"
                                           wire:model="translations.fr.name"
                                           placeholder="Nom de l'entreprise en français">
                                    @error('translations.fr.name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control @error('translations.fr.description') is-invalid @enderror"
                                              rows="6"
                                              wire:model="translations.fr.description"
                                              placeholder="Description de l'entreprise"></textarea>
                                    @error('translations.fr.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- English -->
                            <div class="tab-pane fade" id="tab-en" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control @error('translations.en.name') is-invalid @enderror"
                                           wire:model="translations.en.name"
                                           placeholder="Company name in English">
                                    @error('translations.en.name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control @error('translations.en.description') is-invalid @enderror"
                                              rows="6"
                                              wire:model="translations.en.description"
                                              placeholder="Company description"></textarea>
                                    @error('translations.en.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informations de contact -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title">Informations de Contact</h5>
                    </div>
                    <div class="card-body">
                        <!-- Téléphones -->
                        <div class="mb-3">
                            <label class="form-label">Téléphones</label>
                            @foreach($phones as $index => $phone)
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" wire:model="phones.{{ $index }}"
                                           placeholder="+253 XX XX XX XX">
                                    <button type="button" class="btn btn-outline-danger"
                                            wire:click="removePhone({{ $index }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    wire:click="addPhone">
                                <i class="fas fa-plus me-1"></i>Ajouter un téléphone
                            </button>
                        </div>

                        <!-- Emails -->
                        <div class="mb-3">
                            <label class="form-label">Emails</label>
                            @foreach($emails as $index => $email)
                                <div class="input-group mb-2">
                                    <input type="email" class="form-control" wire:model="emails.{{ $index }}"
                                           placeholder="contact@example.com">
                                    <button type="button" class="btn btn-outline-danger"
                                            wire:click="removeEmail({{ $index }})">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            @endforeach
                            <button type="button" class="btn btn-sm btn-outline-primary"
                                    wire:click="addEmail">
                                <i class="fas fa-plus me-1"></i>Ajouter un email
                            </button>
                        </div>

                        <!-- Site Web -->
                        <div class="mb-3">
                            <label class="form-label">Site Web</label>
                            <input type="url" class="form-control @error('website') is-invalid @enderror"
                                   wire:model="website"
                                   placeholder="https://www.example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Adresse -->
                        <div class="mb-3">
                            <label class="form-label">Adresse</label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      rows="3"
                                      wire:model="address"
                                      placeholder="Adresse complète"></textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Coordonnées GPS -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror"
                                       wire:model="latitude"
                                       placeholder="11.5721">
                                @error('latitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror"
                                       wire:model="longitude"
                                       placeholder="43.1456">
                                @error('longitude')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Logo -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-image me-2"></i>Logo
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary"
                                wire:click="openLogoSelector">
                            <i class="fas fa-images me-1"></i>Choisir
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($logo_id && $allMedia)
                            <div class="text-center">
                                @php $logo = collect($allMedia)->firstWhere('id', $logo_id); @endphp
                                @if ($logo)
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ asset($logo->path) }}"
                                             alt="Logo"
                                             class="img-fluid rounded border shadow-sm"
                                             style="max-height: 200px; max-width: 100%;">
                                        <button type="button"
                                                class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                                wire:click="$set('logo_id', null)"
                                                title="Supprimer le logo">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">{{ $logo->original_name }}</small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4 border rounded" style="border: 2px dashed #dee2e6;">
                                <i class="fas fa-building fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Aucun logo sélectionné</p>
                                <button type="button" class="btn btn-outline-primary"
                                        wire:click="openLogoSelector">
                                    <i class="fas fa-plus me-1"></i>Sélectionner un logo
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Enregistrer
            </button>
            <a href="{{ route('operator.tour-operator.show') }}" class="btn btn-secondary">
                <i class="fas fa-times me-2"></i>Annuler
            </a>
        </div>
    </form>

    <!-- Modal de sélection de médias -->
    @livewire('admin.media-selector-modal')
</div>
