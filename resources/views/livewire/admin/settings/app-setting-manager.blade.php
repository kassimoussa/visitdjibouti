<div>
    {{-- Messages Flash --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">
                <i class="fas fa-mobile-alt me-2 text-primary"></i>
                Paramètres App Mobile
            </h5>
            <p class="text-muted mb-0">Configuration des éléments d'interface pour l'application mobile</p>
        </div>
        <button type="button" 
                class="btn btn-primary"
                wire:click="openCreateModal">
            <i class="fas fa-plus me-1"></i>
            Nouveau paramètre
        </button>
    </div>

    {{-- Paramètres prédéfinis --}}
    <div class="row mb-4">
        <div class="col-12">
            <h6 class="mb-3">
                <i class="fas fa-star me-2 text-warning"></i>
                Paramètres prédéfinis
            </h6>
            <div class="row g-3">
                @foreach($predefinedSettings as $key => $setting)
                    @php
                        $existingSetting = $settings->where('key', $key)->first();
                    @endphp
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 {{ $existingSetting ? 'border-success' : 'border-light' }}">
                            <div class="card-body">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <h6 class="card-title mb-0">{{ $setting['name'] }}</h6>
                                    @if($existingSetting)
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i>
                                        </span>
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                    @endif
                                </div>
                                <p class="card-text text-muted small mb-3">{{ $setting['description'] }}</p>
                                <div class="d-flex gap-2">
                                    <button type="button" 
                                            class="btn btn-sm {{ $existingSetting ? 'btn-outline-primary' : 'btn-primary' }}"
                                            wire:click="openPredefinedModal('{{ $key }}')">
                                        <i class="fas fa-{{ $existingSetting ? 'edit' : 'plus' }} me-1"></i>
                                        {{ $existingSetting ? 'Modifier' : 'Configurer' }}
                                    </button>
                                    @if($existingSetting)
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-{{ $existingSetting->is_active ? 'warning' : 'success' }}"
                                                wire:click="toggleStatus({{ $existingSetting->id }})">
                                            <i class="fas fa-{{ $existingSetting->is_active ? 'pause' : 'play' }}"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Paramètres personnalisés --}}
    @php
        $customSettings = $settings->whereNotIn('key', array_keys($predefinedSettings));
    @endphp
    
    @if($customSettings->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-cogs me-2"></i>
                    Paramètres personnalisés
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Clé</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Dernière modification</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customSettings as $setting)
                                <tr>
                                    <td>
                                        <strong>{{ $setting->key }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">
                                            {{ $availableTypes[$setting->type] ?? $setting->type }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $setting->is_active ? 'success' : 'secondary' }}">
                                            {{ $setting->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $setting->updated_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-outline-primary"
                                                    wire:click="openEditModal({{ $setting->id }})"
                                                    title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-{{ $setting->is_active ? 'warning' : 'success' }}"
                                                    wire:click="toggleStatus({{ $setting->id }})"
                                                    title="{{ $setting->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $setting->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                            <button type="button" 
                                                    class="btn btn-outline-danger"
                                                    wire:click="delete({{ $setting->id }})"
                                                    wire:confirm="Êtes-vous sûr de vouloir supprimer ce paramètre ?"
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
            </div>
        </div>
    @endif

    {{-- Modal d'édition --}}
    @if($showModal)
        <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-mobile-alt me-2"></i>
                            {{ $modalTitle }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <form wire:submit.prevent="save">
                            {{-- Informations de base --}}
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Clé du paramètre *</label>
                                    <input type="text" 
                                           class="form-control @error('currentKey') is-invalid @enderror"
                                           wire:model="currentKey"
                                           placeholder="ex: splash_screens">
                                    @error('currentKey')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label class="form-label">Type *</label>
                                    <select class="form-select @error('currentType') is-invalid @enderror"
                                            wire:model="currentType">
                                        @foreach($availableTypes as $value => $label)
                                            <option value="{{ $value }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('currentType')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            {{-- Média optionnel --}}
                            @if(in_array($currentType, ['image', 'mixed']))
                                <div class="mb-3">
                                    <label class="form-label">Média principal (optionnel)</label>
                                    <div class="d-flex align-items-center gap-3">
                                        @if($mediaId)
                                            @php
                                                $selectedMedia = \App\Models\Media::find($mediaId);
                                            @endphp
                                            @if($selectedMedia)
                                                <img src="{{ $selectedMedia->thumbnail_url ?: $selectedMedia->url }}" 
                                                     alt="Média sélectionné" 
                                                     class="rounded border"
                                                     style="width: 80px; height: 60px; object-fit: cover;">
                                                <div class="text-muted small">
                                                    <div class="fw-bold">{{ $selectedMedia->original_name }}</div>
                                                    <div>{{ $selectedMedia->type }} - {{ number_format($selectedMedia->size / 1024, 1) }} KB</div>
                                                </div>
                                            @else
                                                <div class="text-warning small">
                                                    <i class="fas fa-exclamation-triangle me-1"></i>
                                                    Média non trouvé (ID: {{ $mediaId }})
                                                </div>
                                            @endif
                                        @else
                                            <div class="text-center p-3 border border-dashed rounded" style="width: 80px; height: 60px;">
                                                <i class="fas fa-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="d-flex flex-column gap-2">
                                            <button type="button" 
                                                    class="btn btn-outline-primary btn-sm"
                                                    wire:click="openMediaSelector">
                                                <i class="fas fa-image me-1"></i>
                                                {{ $mediaId ? 'Changer' : 'Sélectionner' }}
                                            </button>
                                            @if($mediaId)
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        wire:click="$set('mediaId', null)">
                                                    <i class="fas fa-times me-1"></i>
                                                    Supprimer
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Statut --}}
                            <div class="mb-3">
                                <div class="form-check">
                                    <input type="checkbox" 
                                           class="form-check-input"
                                           wire:model="isActive"
                                           id="isActive">
                                    <label class="form-check-label" for="isActive">
                                        Paramètre actif
                                    </label>
                                </div>
                            </div>

                            {{-- Formulaires spécialisés selon le type de paramètre --}}
                            @if(in_array($currentKey, array_keys($predefinedSettings)))
                                @include('livewire.admin.app-settings.' . str_replace('_', '-', $currentKey))
                            @else
                                {{-- Formulaire générique pour paramètres personnalisés --}}
                                <div class="mb-3">
                                    <label class="form-label">Contenu JSON personnalisé</label>
                                    <textarea class="form-control"
                                              wire:model="jsonContent"
                                              rows="10"
                                              style="font-family: 'Courier New', monospace;"
                                              placeholder='{"exemple": {"fr": "Texte français", "en": "English text"}}'></textarea>
                                    <div class="form-text">Saisissez votre contenu JSON personnalisé</div>
                                </div>
                            @endif
                        </form>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" 
                                class="btn btn-secondary"
                                wire:click="closeModal">
                            Annuler
                        </button>
                        <button type="button" 
                                class="btn btn-primary"
                                wire:click="save">
                            <i class="fas fa-save me-1"></i>
                            {{ $modalMode === 'create' ? 'Créer' : 'Mettre à jour' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Inclusion du sélecteur de médias --}}
    <livewire:admin.media-selector-modal />
</div>