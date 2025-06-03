<div>
    <style>
        .icon-grid {
            max-height: 200px;
            overflow-y: auto;
        }

        .icon-item {
            cursor: pointer;
            padding: 10px;
            text-align: center;
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .icon-item:hover {
            background-color: #e9ecef;
        }

        .icon-item.selected {
            background-color: #cfe2ff;
            border: 1px solid #9ec5fe;
        }

        .icon-item i {
            font-size: 1.5rem;
            margin-bottom: 5px;
            display: block;
        }

        .icon-item .icon-name {
            font-size: 0.75rem;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <!-- Titre et bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Gestion des catégories</h1>
        <div class="d-flex align-items-center">
            <div class="input-group me-2" style="height: fit-content;">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                    placeholder="Rechercher...">
            </div>
            <button wire:click="openCreateModal" class="btn btn-primary"
                style="height: 38px; display: flex; align-items: center;">
                <i class="fas fa-plus-circle me-1"></i> Ajouter
            </button>
        </div>
    </div>

    <!-- Messages Flash -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tableau des catégories -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Liste des catégories</h5>
            <span class="badge bg-primary">{{ $categories->total() }} catégories</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Catégorie</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="category-icon me-3"
                                            style="background-color: #2563eb; color: #fff; width: 40px; height: 40px; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                            <i class="{{ $category->icon ?? 'fas fa-folder' }}"></i>
                                        </div>
                                        <div class="fw-semibold">{{ $category->name }}</div>
                                    </div>
                                </td>
                                <td class="align-middle text-muted">{{ $category->slug }}</td>
                                <td class="align-middle">
                                    @if ($category->is_active)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="btn-group btn-group-sm">
                                        <button wire:click="openEditModal({{ $category->id }})"
                                            class="btn btn-outline-primary">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="delete({{ $category->id }})" class="btn btn-outline-danger"
                                            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">
                                    <div class="text-muted">Aucune catégorie disponible</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4 mb-4">
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <!-- Contenu du Modal -->
    <x-modal-component>
        <x-slot name="modalTitle">{{ $modalTitle }}</x-slot>
        <x-slot name="modalSize">modal-xl</x-slot>

        <!-- Corps du Modal -->
        <form wire:submit.prevent="save">
            <!-- Onglets pour les langues -->
            <ul class="nav nav-tabs mb-3" role="tablist">
                @foreach($availableLocales as $locale)
                    <li class="nav-item">
                        <a class="nav-link @if($loop->first) active @endif" 
                           data-bs-toggle="tab" 
                           href="#lang-{{ $locale }}" 
                           role="tab"
                           aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                            @if($locale == 'fr')
                                <span>🇫🇷</span> Français
                            @elseif($locale == 'en')
                                <span>🇬🇧</span> English
                            @elseif($locale == 'ar')
                                <span>🇸🇦</span> العربية
                            @else
                                {{ strtoupper($locale) }}
                            @endif
                            
                            @if($locale == config('app.fallback_locale'))
                                <span class="badge bg-danger">Obligatoire</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
            
            <!-- Contenu des onglets -->
            <div class="tab-content">
                @foreach($availableLocales as $locale)
                    <div class="tab-pane fade @if($loop->first) show active @endif" 
                         id="lang-{{ $locale }}" 
                         role="tabpanel">
                        
                        <!-- Nom traduit -->
                        <div class="mb-3">
                            <label for="name-{{ $locale }}" class="form-label">
                                Nom
                                @if($locale == config('app.fallback_locale'))
                                    <span class="text-danger">*</span>
                                @endif
                            </label>
                            <input wire:model="translations.{{ $locale }}.name" 
                                   type="text" 
                                   class="form-control @error('translations.'.$locale.'.name') is-invalid @enderror" 
                                   id="name-{{ $locale }}"
                                   @if($locale == 'ar') dir="rtl" @endif
                                   @if($locale == config('app.fallback_locale')) required @endif>
                            @error('translations.'.$locale.'.name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Description traduite -->
                        <div class="mb-3">
                            <label for="description-{{ $locale }}" class="form-label">Description</label>
                            <textarea wire:model="translations.{{ $locale }}.description" 
                                      class="form-control @error('translations.'.$locale.'.description') is-invalid @enderror" 
                                      id="description-{{ $locale }}" 
                                      rows="3"
                                      @if($locale == 'ar') dir="rtl" @endif></textarea>
                            @error('translations.'.$locale.'.description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Champs non traduisibles -->
            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input wire:model="slug" type="text" class="form-control @error('slug') is-invalid @enderror"
                    id="slug">
                <div class="form-text">Laissez vide pour générer automatiquement un slug basé sur le nom.</div>
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="icon" class="form-label">Icône</label>

                <!-- Champ visible pour le stockage de la valeur -->
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="{{ $icon }}"></i>
                    </span>
                    <input wire:model="icon" type="text" class="form-control @error('icon') is-invalid @enderror"
                        id="icon" readonly>
                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#iconSelectorContainer">
                        Choisir une icône
                    </button>
                </div>

                @error('icon')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <!-- Conteneur du sélecteur d'icônes, collapsible pour économiser de l'espace -->
                <div class="collapse mt-3" id="iconSelectorContainer">
                    <div class="card card-body">
                        @livewire('admin.icon-selector', ['initialIcon' => $icon])
                    </div>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input wire:model="is_active" type="checkbox" class="form-check-input" id="is_active"
                    value="1">
                <label class="form-check-label" for="is_active">Actif</label>
            </div>
        </form>

        <!-- Pied du Modal personnalisé -->
        <x-slot name="modalFooter">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
            <button type="button" class="btn btn-primary" wire:click="save">
                {{ $modalMode === 'create' ? 'Ajouter' : 'Mettre à jour' }}
            </button>
        </x-slot>

        <!-- Action principale du modal (utilisée si modalFooter n'est pas défini) -->
        <x-slot name="modalAction">save</x-slot>
        <x-slot name="modalActionText">{{ $modalMode === 'create' ? 'Ajouter' : 'Mettre à jour' }}</x-slot>
    </x-modal-component>
</div>