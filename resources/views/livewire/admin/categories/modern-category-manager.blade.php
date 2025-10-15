<div class="modern-category-manager">
    <!-- Messages flash -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Vue principale : Catégories principales -->
    @if($viewMode === 'main')
        <div class="main-view">
            <!-- Actions rapides -->
            <div class="d-flex justify-content-end align-items-center mb-3">
                <button wire:click="create" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nouvelle Catégorie
                </button>
            </div>

            <!-- Barre de recherche -->
            <div class="search-bar mb-3">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" 
                           class="form-control border-start-0 ps-0" 
                           placeholder="Rechercher une catégorie..."
                           wire:model.live.debounce.300ms="search">
                </div>
            </div>

            <!-- Grid des catégories principales -->
            <div class="categories-grid">
                <div class="row g-3">
                    @forelse($mainCategories as $category)
                        <div class="col-lg-3 col-md-6">
                            <div class="category-card" 
                                 style="--category-color: {{ $category->color }}">
                                
                                <!-- Header de la card -->
                                <div class="category-header">
                                    <div class="category-icon">
                                        @php
                                            $isEmoji = $category->icon && strlen($category->icon) > 0 && !str_contains($category->icon, ' ') && 
                                                       !str_contains($category->icon, 'fa-') && !str_contains($category->icon, 'bi-') && 
                                                       !str_contains($category->icon, 'fi fi-') && !str_contains($category->icon, 'ph ph-') && 
                                                       !str_contains($category->icon, 'ti ti-') && 
                                                       preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $category->icon);
                                        @endphp
                                        
                                        @if($category->icon && str_contains($category->icon, 'fi fi-'))
                                            <span class="{{ $category->icon }}"></span>
                                        @elseif($category->icon && str_contains($category->icon, 'ti ti-'))
                                            <i class="{{ $category->icon }}"></i>
                                        @elseif($category->icon && str_contains($category->icon, 'bi-'))
                                            <i class="{{ $category->icon }}"></i>
                                        @elseif($category->icon && str_contains($category->icon, 'ph ph-'))
                                            <i class="{{ $category->icon }}"></i>
                                        @elseif($isEmoji)
                                            <span style="font-size: 1.2rem;">{{ $category->icon }}</span>
                                        @else
                                            <i class="{{ $category->icon }}"></i>
                                        @endif
                                    </div>
                                    <div class="category-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" wire:click="edit({{ $category->id }})">
                                                        <i class="fas fa-edit me-2"></i>Modifier
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" wire:click="create({{ $category->id }})">
                                                        <i class="fas fa-plus me-2"></i>Ajouter sous-catégorie
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item" wire:click="toggleActive({{ $category->id }})">
                                                        <i class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }} me-2"></i>
                                                        {{ $category->is_active ? 'Désactiver' : 'Activer' }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" 
                                                       wire:click="delete({{ $category->id }})"
                                                       onclick="return confirm('Êtes-vous sûr ?')">
                                                        <i class="fas fa-trash me-2"></i>Supprimer
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Contenu de la card -->
                                <div class="category-content" wire:click="viewSubcategories({{ $category->id }})">
                                    <h4 class="category-title">{{ $category->translation('fr')->name ?? 'Sans nom' }}</h4>
                                    <p class="category-description">
                                        {{ $category->translation('fr')->description ?? 'Aucune description' }}
                                    </p>
                                    
                                    <!-- Stats -->
                                    <div class="category-stats">
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $category->children->count() }}</span>
                                            <span class="stat-label">Sous-catégories</span>
                                        </div>
                                        <div class="stat-item">
                                            <span class="stat-number">{{ $category->pois->count() ?? 0 }}</span>
                                            <span class="stat-label">POIs</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Footer de la card -->
                                <div class="category-footer">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                        @if($category->children->count() > 0)
                                            <span class="view-subcategories" wire:click="viewSubcategories({{ $category->id }})" style="cursor: pointer;">
                                                Voir les sous-catégories <i class="fas fa-arrow-right ms-1"></i>
                                            </span>
                                        @else
                                            <span class="text-muted small">Aucune sous-catégorie</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <h3>Aucune catégorie trouvée</h3>
                                <p class="text-muted">Commencez par créer votre première catégorie principale</p>
                                <button wire:click="create" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Créer une catégorie
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Vue des sous-catégories -->
    @if($viewMode === 'subcategories' && $selectedParent)
        <div class="subcategories-view">
            <!-- Breadcrumb et header -->
            <div class="subcategories-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a wire:click="backToMain" class="text-decoration-none cursor-pointer">
                                <i class="fas fa-layer-group me-1"></i>Catégories
                            </a>
                        </li>
                        <li class="breadcrumb-item active">{{ $selectedParent->translation('fr')->name }}</li>
                    </ol>
                </nav>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="parent-category-info">
                        <div class="d-flex align-items-center">
                            <div class="parent-icon me-3" style="color: {{ $selectedParent->color }}">
                                @php
                                    $isEmoji = $selectedParent->icon && strlen($selectedParent->icon) > 0 && !str_contains($selectedParent->icon, ' ') && 
                                               !str_contains($selectedParent->icon, 'fa-') && !str_contains($selectedParent->icon, 'bi-') && 
                                               !str_contains($selectedParent->icon, 'fi fi-') && !str_contains($selectedParent->icon, 'ph ph-') && 
                                               !str_contains($selectedParent->icon, 'ti ti-') && 
                                               preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $selectedParent->icon);
                                @endphp
                                
                                @if($selectedParent->icon && str_contains($selectedParent->icon, 'fi fi-'))
                                    <span class="{{ $selectedParent->icon }}" style="font-size: 2rem;"></span>
                                @elseif($selectedParent->icon && str_contains($selectedParent->icon, 'ti ti-'))
                                    <i class="{{ $selectedParent->icon }}" style="font-size: 2rem;"></i>
                                @elseif($selectedParent->icon && str_contains($selectedParent->icon, 'bi-'))
                                    <i class="{{ $selectedParent->icon }}" style="font-size: 2rem;"></i>
                                @elseif($selectedParent->icon && str_contains($selectedParent->icon, 'ph ph-'))
                                    <i class="{{ $selectedParent->icon }}" style="font-size: 2rem;"></i>
                                @elseif($isEmoji)
                                    <span style="font-size: 2rem;">{{ $selectedParent->icon }}</span>
                                @else
                                    <i class="{{ $selectedParent->icon }} fa-2x"></i>
                                @endif
                            </div>
                            <div>
                                <h2 class="h3 mb-1">{{ $selectedParent->translation('fr')->name }}</h2>
                                <p class="text-muted mb-0">{{ $selectedParent->translation('fr')->description }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button wire:click="create({{ $selectedParent->id }})" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nouvelle Sous-catégorie
                        </button>
                        <button wire:click="backToMain" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-arrow-left me-2"></i>Retour
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recherche des sous-catégories -->
            <div class="search-bar mb-4">
                <div class="input-group">
                    <span class="input-group-text bg-light">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           placeholder="Rechercher une sous-catégorie..."
                           wire:model.live.debounce.300ms="searchSubcategories">
                </div>
            </div>

            <!-- Liste des sous-catégories -->
            <div class="subcategories-list">
                <div class="row g-3">
                    @forelse($subcategories as $subcategory)
                        <div class="col-lg-4 col-md-6">
                            <div class="subcategory-card">
                                <div class="subcategory-header">
                                    <div class="subcategory-icon" style="color: {{ $subcategory->color }}">
                                        @php
                                            $isEmoji = $subcategory->icon && strlen($subcategory->icon) > 0 && !str_contains($subcategory->icon, ' ') && 
                                                       !str_contains($subcategory->icon, 'fa-') && !str_contains($subcategory->icon, 'bi-') && 
                                                       !str_contains($subcategory->icon, 'fi fi-') && !str_contains($subcategory->icon, 'ph ph-') && 
                                                       !str_contains($subcategory->icon, 'ti ti-') && 
                                                       preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $subcategory->icon);
                                        @endphp
                                        
                                        @if($subcategory->icon && str_contains($subcategory->icon, 'fi fi-'))
                                            <span class="{{ $subcategory->icon }}"></span>
                                        @elseif($subcategory->icon && str_contains($subcategory->icon, 'ti ti-'))
                                            <i class="{{ $subcategory->icon }}"></i>
                                        @elseif($subcategory->icon && str_contains($subcategory->icon, 'bi-'))
                                            <i class="{{ $subcategory->icon }}"></i>
                                        @elseif($subcategory->icon && str_contains($subcategory->icon, 'ph ph-'))
                                            <i class="{{ $subcategory->icon }}"></i>
                                        @elseif($isEmoji)
                                            <span style="font-size: 1.5rem;">{{ $subcategory->icon }}</span>
                                        @else
                                            <i class="{{ $subcategory->icon }}"></i>
                                        @endif
                                    </div>
                                    <div class="subcategory-actions">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light dropdown-toggle" 
                                                    data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li>
                                                    <a class="dropdown-item" wire:click="edit({{ $subcategory->id }})">
                                                        <i class="fas fa-edit me-2"></i>Modifier
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" wire:click="toggleActive({{ $subcategory->id }})">
                                                        <i class="fas fa-{{ $subcategory->is_active ? 'eye-slash' : 'eye' }} me-2"></i>
                                                        {{ $subcategory->is_active ? 'Désactiver' : 'Activer' }}
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a class="dropdown-item text-danger" 
                                                       wire:click="delete({{ $subcategory->id }})"
                                                       onclick="return confirm('Êtes-vous sûr ?')">
                                                        <i class="fas fa-trash me-2"></i>Supprimer
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="subcategory-content">
                                    <h5 class="subcategory-title">{{ $subcategory->translation('fr')->name }}</h5>
                                    <p class="subcategory-description">
                                        {{ $subcategory->translation('fr')->description }}
                                    </p>
                                    
                                    <div class="subcategory-meta">
                                        <span class="badge {{ $subcategory->is_active ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $subcategory->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                        <span class="text-muted small">
                                            {{ $subcategory->pois->count() ?? 0 }} POIs
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="empty-state-small">
                                <div class="empty-icon">
                                    <i class="fas fa-folder-open"></i>
                                </div>
                                <h4>Aucune sous-catégorie</h4>
                                <p class="text-muted">Cette catégorie n'a pas encore de sous-catégories</p>
                                <button wire:click="create({{ $selectedParent->id }})" class="btn btn-primary">
                                    <i class="fas fa-plus me-2"></i>Créer la première sous-catégorie
                                </button>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de création/édition -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white border-0">
                        <h5 class="modal-title">
                            <i class="fas fa-{{ $modalMode === 'create' ? 'plus' : 'edit' }} me-2"></i>
                            {{ $modalMode === 'create' ? 'Nouvelle' : 'Modifier' }} 
                            {{ $parent_id ? 'Sous-catégorie' : 'Catégorie' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>

                    <form wire:submit.prevent="save">
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                <!-- Colonne gauche : Configuration -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">
                                        <i class="fas fa-cog me-2"></i>Configuration
                                    </h6>

                                    <!-- Icône -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Icône</label>

                                        <!-- Dropdown pour choisir le fournisseur -->
                                        <div class="mb-2">
                                            <select class="form-select form-select-sm" id="iconProvider" onchange="updateIconPlaceholder(this.value)">
                                                <option value="fontawesome">FontAwesome (fas fa-...)</option>
                                                <option value="bootstrap">Bootstrap Icons (bi-...)</option>
                                                <option value="phosphor">Phosphor Icons (ph ph-...)</option>
                                                <option value="tabler">Tabler Icons (ti ti-...)</option>
                                                <option value="flags">Flag Icons (fi fi-...)</option>
                                                <option value="emojis">Emojis Unicode (🏛️)</option>
                                            </select>
                                        </div>

                                        <!-- Champ texte avec preview -->
                                        <div class="input-group">
                                            <span class="input-group-text icon-preview" style="min-width: 60px; justify-content: center; background: #f8f9fa;">
                                                @php
                                                    $isEmoji = $icon && strlen($icon) > 0 && !str_contains($icon, ' ') &&
                                                               !str_contains($icon, 'fa-') && !str_contains($icon, 'bi-') &&
                                                               !str_contains($icon, 'fi fi-') && !str_contains($icon, 'ph ph-') &&
                                                               !str_contains($icon, 'ti ti-') &&
                                                               preg_match('/[\x{1F600}-\x{1F64F}]|[\x{1F300}-\x{1F5FF}]|[\x{1F680}-\x{1F6FF}]|[\x{1F1E0}-\x{1F1FF}]|[\x{2600}-\x{26FF}]|[\x{2700}-\x{27BF}]/u', $icon);
                                                @endphp

                                                @if($icon && str_contains($icon, 'fi fi-'))
                                                    <span class="{{ $icon }}" style="font-size: 1.5rem;"></span>
                                                @elseif($icon && str_contains($icon, 'ti ti-'))
                                                    <i class="{{ $icon }}" style="font-size: 1.5rem; color: {{ $color }};"></i>
                                                @elseif($icon && str_contains($icon, 'bi-'))
                                                    <i class="{{ $icon }}" style="font-size: 1.5rem; color: {{ $color }};"></i>
                                                @elseif($icon && str_contains($icon, 'ph ph-'))
                                                    <i class="{{ $icon }}" style="font-size: 1.5rem; color: {{ $color }};"></i>
                                                @elseif($isEmoji)
                                                    <span style="font-size: 1.5rem;">{{ $icon }}</span>
                                                @else
                                                    <i class="{{ $icon }}" style="font-size: 1.5rem; color: {{ $color }};"></i>
                                                @endif
                                            </span>
                                            <input type="text"
                                                   class="form-control"
                                                   wire:model.live="icon"
                                                   id="iconInput"
                                                   placeholder="fas fa-folder">
                                        </div>

                                        <!-- Suggestions d'icônes selon le fournisseur -->
                                        <div class="mt-2">
                                            <div class="icon-suggestions-container" id="iconSuggestions">
                                                <div class="mb-1">
                                                    <span class="text-muted small fw-bold">Suggestions:</span>
                                                </div>
                                                <div class="d-flex flex-wrap">
                                                    <span class="icon-suggestion" onclick="selectIcon('fas fa-home')">fas fa-home</span>
                                                    <span class="icon-suggestion" onclick="selectIcon('fas fa-user')">fas fa-user</span>
                                                    <span class="icon-suggestion" onclick="selectIcon('fas fa-heart')">fas fa-heart</span>
                                                    <span class="icon-suggestion" onclick="selectIcon('fas fa-star')">fas fa-star</span>
                                                    <span class="icon-suggestion" onclick="selectIcon('fas fa-map-marker-alt')">fas fa-map-marker-alt</span>
                                                </div>
                                            </div>

                                            <!-- Bouton pour ouvrir la galerie complète -->
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-info-circle me-1"></i>
                                                    Tapez votre icône ou cliquez sur une suggestion
                                                </small>
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-info"
                                                        onclick="window.open('{{ route('icons.gallery') }}', 'iconGallery', 'width=1200,height=800,scrollbars=yes,resizable=yes')"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        data-bs-title="Voir toutes les icônes disponibles">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Couleur -->
                                    <div class="form-group mb-3">
                                        <label class="form-label">Couleur</label>
                                        <input type="text" class="form-control"
                                               wire:model.live="color" placeholder="#31a051">
                                    </div>

                                    <!-- Ordre d'affichage et Statut actif sur la même ligne -->
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Ordre d'affichage</label>
                                                <input type="number" class="form-control" wire:model="sort_order" min="0">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label d-block">Statut</label>
                                                <div class="form-check form-switch mt-2">
                                                    <input class="form-check-input" type="checkbox" wire:model="is_active" id="categoryActive">
                                                    <label class="form-check-label" for="categoryActive">Catégorie active</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Slug (masqué par défaut) -->
                                    <div class="form-group mb-3" id="slugField" style="display: none;">
                                        <label class="form-label">Slug (personnalisé)</label>
                                        <input type="text" class="form-control"
                                               wire:model="slug" placeholder="mon-slug">
                                        <small class="text-muted">Laissez vide pour générer automatiquement depuis le nom français</small>
                                    </div>
                                </div>

                                <!-- Colonne droite : Traductions -->
                                <div class="col-md-6">
                                    <h6 class="mb-3">
                                        <i class="fas fa-language me-2"></i>Traductions
                                    </h6>

                                    <ul class="nav nav-tabs" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#tab-fr">
                                                🇫🇷 Français
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tab-en">
                                                🇬🇧 English
                                            </a>
                                        </li>
                                    </ul>

                                    <div class="tab-content border border-top-0 p-3" style="min-height: 400px;">
                                        <!-- Français -->
                                        <div class="tab-pane fade show active" id="tab-fr">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label">Nom *</label>
                                                    <input type="text" class="form-control"
                                                           wire:model="translations.fr.name" required>
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" rows="8"
                                                              wire:model="translations.fr.description"></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- English -->
                                        <div class="tab-pane fade" id="tab-en">
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label">Name</label>
                                                    <input type="text" class="form-control"
                                                           wire:model="translations.en.name">
                                                </div>
                                                <div class="col-12">
                                                    <label class="form-label">Description</label>
                                                    <textarea class="form-control" rows="8"
                                                              wire:model="translations.en.description"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 bg-light">
                            <button type="button" class="btn btn-outline-secondary" wire:click="closeModal">
                                <i class="fas fa-times me-2"></i>Annuler
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Sauvegarder
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Styles intégrés pour éviter les multiples éléments racine -->
    <style>
    .modern-category-manager {
        --primary-color: #3498db;
        --success-color: #27ae60;
        --warning-color: #f39c12;
        --danger-color: #e74c3c;
        --dark-color: #2c3e50;
        --light-color: #ecf0f1;
        --border-radius: 12px;
        --shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.15);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Categories Grid */
    .categories-grid .category-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        transition: var(--transition);
        cursor: pointer;
        border: 2px solid transparent;
        overflow: visible; /* Changé de hidden à visible pour les dropdowns */
        position: relative;
    }

    .category-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
        border-color: var(--category-color);
        z-index: 20;
        position: relative;
    }

    .category-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1rem 0;
    }

    .category-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, var(--category-color), color-mix(in srgb, var(--category-color) 80%, black));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .category-actions .dropdown-toggle {
        border: none;
        box-shadow: none;
        opacity: 0;
        transition: var(--transition);
    }

    .category-card:hover .dropdown-toggle {
        opacity: 1;
    }

    /* Styles pour les dropdowns - force l'affichage au-dessus */
    .category-actions {
        position: relative;
        z-index: 10;
    }

    .category-actions .dropdown {
        position: relative;
    }

    .category-actions .dropdown-menu {
        position: absolute;
        z-index: 1050 !important;
        margin-top: 0.125rem;
        min-width: 180px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        border-radius: 0.375rem;
        right: 0;
        left: auto;
    }

    /* Force le dropdown à s'afficher vers le haut pour les dernières cartes */
    .categories-grid .row > div:nth-last-child(-n+4) .dropdown-menu {
        top: auto;
        bottom: 100%;
        margin-top: 0;
        margin-bottom: 0.125rem;
    }

    .category-card .dropdown.show {
        z-index: 1051;
    }

    /* S'assurer que le dropdown est toujours visible, même à droite de l'écran */
    .categories-grid .col-lg-3:last-child .dropdown-menu,
    .categories-grid .col-lg-3:nth-child(4n) .dropdown-menu {
        right: 0;
        left: auto;
    }

    .category-content {
        padding: 1rem;
    }

    .category-title {
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    .category-description {
        color: #6c757d;
        margin-bottom: 1rem;
        line-height: 1.5;
        font-size: 0.9rem;
    }

    .category-stats {
        display: flex;
        gap: 1.5rem;
        margin-bottom: 0.8rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-number {
        display: block;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--category-color);
    }

    .stat-label {
        display: block;
        font-size: 0.875rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .category-footer {
        padding: 0 1rem 1rem;
    }

    .view-subcategories {
        color: var(--category-color);
        font-weight: 600;
        font-size: 0.875rem;
        transition: var(--transition);
    }

    .category-card:hover .view-subcategories {
        transform: translateX(4px);
    }

    /* Subcategories */
    .subcategories-header {
        margin-bottom: 2rem;
    }

    .parent-category-info .parent-icon {
        width: 60px;
        height: 60px;
        background: rgba(52, 152, 219, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .subcategory-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--shadow);
        transition: var(--transition);
        border: 1px solid #e9ecef;
        overflow: visible; /* Changé pour permettre l'affichage des dropdowns */
        position: relative;
    }

    .subcategory-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
        z-index: 20;
        position: relative;
    }

    .subcategory-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1rem 0;
    }

    .subcategory-icon {
        font-size: 1.5rem;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(52, 152, 219, 0.1);
        border-radius: 8px;
    }

    .subcategory-content {
        padding: 1rem;
    }

    .subcategory-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: var(--dark-color);
        margin-bottom: 0.5rem;
    }

    .subcategory-description {
        color: #6c757d;
        margin-bottom: 1rem;
        font-size: 0.875rem;
        line-height: 1.5;
    }

    .subcategory-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Styles pour les dropdowns des sous-catégories */
    .subcategory-actions {
        position: relative;
        z-index: 10;
    }

    .subcategory-actions .dropdown {
        position: relative;
    }

    .subcategory-actions .dropdown-menu {
        position: absolute;
        z-index: 1050 !important;
        margin-top: 0.125rem;
        min-width: 180px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        border-radius: 0.375rem;
        right: 0;
        left: auto;
    }

    /* Force les dropdowns des sous-catégories vers le haut si nécessaire */
    .subcategories-list .row > div:nth-last-child(-n+3) .dropdown-menu {
        top: auto;
        bottom: 100%;
        margin-top: 0;
        margin-bottom: 0.125rem;
    }

    .subcategory-card .dropdown.show {
        z-index: 1051;
    }

    /* Empty States */
    .empty-state, .empty-state-small {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-small {
        padding: 2rem;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: var(--light-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
        font-size: 2rem;
        color: #6c757d;
    }

    /* Search Bar */
    .search-bar .form-control {
        border-radius: var(--border-radius);
        border: 2px solid #e9ecef;
        padding: 0.75rem 1rem;
        transition: var(--transition);
    }

    .search-bar .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .search-bar .input-group-text {
        border-radius: var(--border-radius) 0 0 var(--border-radius);
        border: 2px solid #e9ecef;
        border-right: none;
    }

    /* Breadcrumb */
    .breadcrumb {
        background: none;
        padding: 0;
        margin-bottom: 1rem;
    }

    .breadcrumb-item a {
        color: var(--primary-color);
        transition: var(--transition);
    }

    .breadcrumb-item a:hover {
        color: var(--dark-color);
    }

    /* Modal */
    .modal-content {
        border-radius: var(--border-radius);
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-footer {
        border-top: 1px solid #e9ecef;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .categories-grid .col-lg-6 {
            margin-bottom: 1rem;
        }
        
        .category-stats {
            gap: 1rem;
        }
        
        .subcategories-header .header-actions {
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .subcategories-header .header-actions .btn {
            width: 100%;
        }
    }

    /* Animations */
    @keyframes slideInUp {
        from {
            transform: translateY(20px);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .category-card, .subcategory-card {
        animation: slideInUp 0.3s ease-out;
    }

    .cursor-pointer {
        cursor: pointer;
    }

    /* Icon selector improvements */
    .icon-preview {
        transition: all 0.2s ease;
    }

    .icon-suggestion {
        cursor: pointer;
        padding: 3px 8px;
        border-radius: 6px;
        margin-right: 6px;
        margin-bottom: 4px;
        display: inline-block;
        background-color: #f8f9fa;
        border: 1px solid #dee2e6;
        color: #495057;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .icon-suggestion:hover {
        background-color: #007bff;
        color: white;
        border-color: #007bff;
        text-decoration: none;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,123,255,0.2);
    }

    .icon-suggestions-container {
        line-height: 1.8;
    }
    </style>

    @script
    <script>
        // Fonction pour afficher/masquer le champ slug
        window.toggleSlugField = function() {
            const slugField = document.getElementById('slugField');
            const toggleLink = document.getElementById('toggleSlug');

            if (slugField.style.display === 'none') {
                slugField.style.display = 'block';
                toggleLink.innerHTML = '<i class="fas fa-eye-slash me-1"></i>Masquer les options avancées';
            } else {
                slugField.style.display = 'none';
                toggleLink.innerHTML = '<i class="fas fa-cog me-1"></i>Options avancées (slug personnalisé)';
            }
        };

        // Fonction pour sélectionner une icône depuis les suggestions
        window.selectIcon = function(iconClass) {
            @this.set('icon', iconClass);
        };

        // Fonction pour mettre à jour le placeholder selon le fournisseur d'icônes
        window.updateIconPlaceholder = function(provider) {
            const iconInput = document.getElementById('iconInput');
            const suggestionsContainer = document.getElementById('iconSuggestions');

            if (!iconInput || !suggestionsContainer) return;

            let placeholder = '';
            let suggestions = '';

            switch(provider) {
                case 'fontawesome':
                    placeholder = 'fas fa-folder';
                    suggestions = `
                        <div class="mb-1"><span class="text-muted small fw-bold">FontAwesome:</span></div>
                        <div class="d-flex flex-wrap">
                            <span class="icon-suggestion" onclick="selectIcon('fas fa-home')">fas fa-home</span>
                            <span class="icon-suggestion" onclick="selectIcon('fas fa-user')">fas fa-user</span>
                            <span class="icon-suggestion" onclick="selectIcon('fas fa-heart')">fas fa-heart</span>
                            <span class="icon-suggestion" onclick="selectIcon('fas fa-star')">fas fa-star</span>
                            <span class="icon-suggestion" onclick="selectIcon('fas fa-map-marker-alt')">fas fa-map-marker-alt</span>
                            <span class="icon-suggestion" onclick="selectIcon('fas fa-camera')">fas fa-camera</span>
                        </div>
                    `;
                    break;
                case 'bootstrap':
                    placeholder = 'bi-house';
                    suggestions = `
                        <div class="mb-1"><span class="text-muted small fw-bold">Bootstrap Icons:</span></div>
                        <div class="d-flex flex-wrap">
                            <span class="icon-suggestion" onclick="selectIcon('bi-house')">bi-house</span>
                            <span class="icon-suggestion" onclick="selectIcon('bi-person')">bi-person</span>
                            <span class="icon-suggestion" onclick="selectIcon('bi-heart')">bi-heart</span>
                            <span class="icon-suggestion" onclick="selectIcon('bi-star')">bi-star</span>
                            <span class="icon-suggestion" onclick="selectIcon('bi-geo-alt')">bi-geo-alt</span>
                            <span class="icon-suggestion" onclick="selectIcon('bi-camera')">bi-camera</span>
                        </div>
                    `;
                    break;
                case 'phosphor':
                    placeholder = 'ph ph-house';
                    suggestions = `
                        <div class="mb-1"><span class="text-muted small fw-bold">Phosphor Icons:</span></div>
                        <div class="d-flex flex-wrap">
                            <span class="icon-suggestion" onclick="selectIcon('ph ph-house')">ph ph-house</span>
                            <span class="icon-suggestion" onclick="selectIcon('ph ph-user')">ph ph-user</span>
                            <span class="icon-suggestion" onclick="selectIcon('ph ph-heart')">ph ph-heart</span>
                            <span class="icon-suggestion" onclick="selectIcon('ph ph-star')">ph ph-star</span>
                            <span class="icon-suggestion" onclick="selectIcon('ph ph-map-pin')">ph ph-map-pin</span>
                            <span class="icon-suggestion" onclick="selectIcon('ph ph-camera')">ph ph-camera</span>
                        </div>
                    `;
                    break;
                case 'tabler':
                    placeholder = 'ti ti-home';
                    suggestions = `
                        <div class="mb-1"><span class="text-muted small fw-bold">Tabler Icons:</span></div>
                        <div class="d-flex flex-wrap">
                            <span class="icon-suggestion" onclick="selectIcon('ti ti-home')">ti ti-home</span>
                            <span class="icon-suggestion" onclick="selectIcon('ti ti-user')">ti ti-user</span>
                            <span class="icon-suggestion" onclick="selectIcon('ti ti-heart')">ti ti-heart</span>
                            <span class="icon-suggestion" onclick="selectIcon('ti ti-star')">ti ti-star</span>
                            <span class="icon-suggestion" onclick="selectIcon('ti ti-map-pin')">ti ti-map-pin</span>
                            <span class="icon-suggestion" onclick="selectIcon('ti ti-camera')">ti ti-camera</span>
                        </div>
                    `;
                    break;
                case 'flags':
                    placeholder = 'fi fi-fr';
                    suggestions = `
                        <div class="mb-1"><span class="text-muted small fw-bold">Flag Icons:</span></div>
                        <div class="d-flex flex-wrap">
                            <span class="icon-suggestion" onclick="selectIcon('fi fi-dj')">fi fi-dj (Djibouti)</span>
                            <span class="icon-suggestion" onclick="selectIcon('fi fi-fr')">fi fi-fr (France)</span>
                            <span class="icon-suggestion" onclick="selectIcon('fi fi-us')">fi fi-us (USA)</span>
                            <span class="icon-suggestion" onclick="selectIcon('fi fi-gb')">fi fi-gb (UK)</span>
                            <span class="icon-suggestion" onclick="selectIcon('fi fi-de')">fi fi-de (Germany)</span>
                        </div>
                    `;
                    break;
                case 'emojis':
                    placeholder = '🏛️';
                    suggestions = `
                        <div class="mb-1"><span class="text-muted small fw-bold">Emojis:</span></div>
                        <div class="d-flex flex-wrap">
                            <span class="icon-suggestion" onclick="selectIcon('🏛️')">🏛️</span>
                            <span class="icon-suggestion" onclick="selectIcon('🏖️')">🏖️</span>
                            <span class="icon-suggestion" onclick="selectIcon('🏝️')">🏝️</span>
                            <span class="icon-suggestion" onclick="selectIcon('🗿')">🗿</span>
                            <span class="icon-suggestion" onclick="selectIcon('🕌')">🕌</span>
                            <span class="icon-suggestion" onclick="selectIcon('⛰️')">⛰️</span>
                            <span class="icon-suggestion" onclick="selectIcon('🌋')">🌋</span>
                            <span class="icon-suggestion" onclick="selectIcon('🐪')">🐪</span>
                        </div>
                    `;
                    break;
            }

            iconInput.placeholder = placeholder;
            suggestionsContainer.innerHTML = suggestions;
        };
    </script>
    @endscript

</div>

