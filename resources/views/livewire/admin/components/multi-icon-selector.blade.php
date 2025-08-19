<div>
    <!-- Sélecteur de fournisseur d'icônes -->
    <div class="mb-3">
        <label class="form-label fw-semibold">Fournisseur d'icônes</label>
        <div class="btn-group w-100" role="group">
            @foreach($iconProviders as $provider => $name)
                <button type="button" 
                        class="btn {{ $activeProvider === $provider ? 'btn-primary' : 'btn-outline-primary' }} btn-sm"
                        wire:click="switchProvider('{{ $provider }}')">
                    @switch($provider)
                        @case('fontawesome')
                            <i class="fab fa-font-awesome"></i>
                            @break
                        @case('tabler')
                            <i class="ti ti-icons"></i>
                            @break
                        @case('lucide')
                            <i class="lucide-zap"></i>
                            @break
                        @case('bootstrap')
                            <i class="bi bi-bootstrap"></i>
                            @break
                        @case('flags')
                            🏳️
                            @break
                        @case('emojis')
                            😀
                            @break
                    @endswitch
                    <span class="ms-1 d-none d-md-inline">{{ $name }}</span>
                </button>
            @endforeach
        </div>
    </div>

    <!-- Barre de recherche -->
    <div class="mb-3">
        <div class="input-group">
            <span class="input-group-text">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" 
                   class="form-control" 
                   placeholder="Rechercher une icône..."
                   wire:model.live.debounce.300ms="searchQuery">
            @if($searchQuery)
                <button class="btn btn-outline-secondary" 
                        type="button" 
                        wire:click="$set('searchQuery', '')">
                    <i class="fas fa-times"></i>
                </button>
            @endif
        </div>
    </div>

    <!-- Catégories -->
    @if(count($currentCategories) > 1)
    <div class="mb-3">
        <div class="btn-group flex-wrap" role="group">
            @foreach($currentCategories as $categoryKey => $categoryName)
                <button type="button" 
                        class="btn {{ $activeCategory === $categoryKey ? 'btn-secondary' : 'btn-outline-secondary' }} btn-sm"
                        wire:click="changeCategory('{{ $categoryKey }}')">
                    {{ $categoryName }}
                </button>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Icône sélectionnée -->
    @if($selectedIcon)
    <div class="mb-3 p-3 bg-light rounded">
        <label class="form-label fw-semibold">Icône sélectionnée :</label>
        <div class="d-flex align-items-center">
            @if($activeProvider === 'flags' && isset($filteredIcons[$selectedIcon]))
                <span class="{{ $selectedIcon }}" style="font-size: 1.5rem;"></span>
                <span class="ms-2">{{ $filteredIcons[$selectedIcon] }}</span>
            @elseif($activeProvider === 'emojis')
                <span style="font-size: 1.5rem;">{{ $selectedIcon }}</span>
                @if(isset($filteredIcons[$selectedIcon]))
                    <span class="ms-2">{{ $filteredIcons[$selectedIcon] }}</span>
                @endif
            @else
                <i class="{{ $selectedIcon }}" style="font-size: 1.5rem;"></i>
                <code class="ms-2">{{ $selectedIcon }}</code>
            @endif
        </div>
    </div>
    @endif

    <!-- Grille d'icônes -->
    <div class="icon-selector-grid" style="max-height: 400px; overflow-y: auto;">
        @if(count($filteredIcons) > 0)
            <div class="row g-2">
                @if($activeProvider === 'flags')
                    @foreach($filteredIcons as $flagClass => $flagName)
                        <div class="col-6 col-sm-4 col-md-3">
                            <button type="button" 
                                    class="btn btn-outline-secondary w-100 p-2 icon-btn {{ $selectedIcon === $flagClass ? 'active' : '' }}" 
                                    wire:click="selectIcon('{{ $flagClass }}')"
                                    title="{{ $flagName }}">
                                <span class="{{ $flagClass }}" style="font-size: 1.2rem;"></span>
                                <small class="d-block mt-1 text-truncate">{{ explode(' ', $flagName)[1] ?? $flagName }}</small>
                            </button>
                        </div>
                    @endforeach
                @elseif($activeProvider === 'emojis')
                    @foreach($filteredIcons as $emoji => $description)
                        <div class="col-6 col-sm-4 col-md-3">
                            <button type="button" 
                                    class="btn btn-outline-secondary w-100 p-2 icon-btn {{ $selectedIcon === $emoji ? 'active' : '' }}" 
                                    wire:click="selectIcon('{{ $emoji }}')"
                                    title="{{ $description }}">
                                <span style="font-size: 1.5rem;">{{ $emoji }}</span>
                                <small class="d-block mt-1 text-truncate">{{ $description }}</small>
                            </button>
                        </div>
                    @endforeach
                @else
                    @foreach($filteredIcons as $icon)
                        <div class="col-6 col-sm-4 col-md-3">
                            <button type="button" 
                                    class="btn btn-outline-secondary w-100 p-2 icon-btn {{ $selectedIcon === $icon ? 'active' : '' }}" 
                                    wire:click="selectIcon('{{ $icon }}')"
                                    title="{{ $icon }}">
                                <i class="{{ $icon }}" style="font-size: 1.2rem;"></i>
                                <small class="d-block mt-1 text-truncate">{{ str_replace(['fas fa-', 'far fa-', 'fab fa-', 'ti ti-', 'lucide-', 'bi-'], '', $icon) }}</small>
                            </button>
                        </div>
                    @endforeach
                @endif
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-search fa-2x text-muted mb-2"></i>
                <p class="text-muted">Aucune icône trouvée</p>
                @if($searchQuery)
                    <button class="btn btn-sm btn-outline-primary" wire:click="$set('searchQuery', '')">
                        Effacer la recherche
                    </button>
                @endif
            </div>
        @endif
    </div>

    <style>
    .icon-btn {
        transition: all 0.2s ease;
        min-height: 60px;
    }

    .icon-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .icon-btn.active {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
        color: white;
    }

    /* Responsive icon grid */
    @media (max-width: 576px) {
        .icon-btn {
            min-height: 50px;
            font-size: 0.8rem;
        }
    }

    /* Flag icons support */
    .fi {
        font-size: inherit !important;
    }

    /* Tabler icons support */
    .ti::before {
        font-size: inherit;
    }

    /* Lucide icons support */
    .lucide-zap::before {
        content: "⚡";
    }

    /* Bootstrap icons support */
    .bi::before {
        font-size: inherit;
    }
    </style>
</div>

