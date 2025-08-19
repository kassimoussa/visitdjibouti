<div class="icon-gallery-container">
    <!-- Header -->
    <div class="gallery-header mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="h4 mb-1">
                    <i class="fas fa-palette me-2"></i>
                    Galerie d'icônes
                </h2>
                <p class="text-muted mb-0">
                    Parcourez toutes les icônes disponibles par fournisseur
                </p>
            </div>
            <button type="button" class="btn btn-outline-secondary" onclick="window.close()">
                <i class="fas fa-times me-1"></i>
                Fermer
            </button>
        </div>
    </div>

    <!-- Onglets des fournisseurs -->
    <div class="mb-4">
        <ul class="nav nav-tabs" role="tablist">
            @foreach($iconCollections as $key => $collection)
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === $key ? 'active' : '' }}"
                            wire:click="switchTab('{{ $key }}')"
                            type="button">
                        @switch($key)
                            @case('fontawesome')
                                <i class="fab fa-font-awesome me-1"></i>
                                @break
                            @case('bootstrap')
                                <i class="bi bi-bootstrap me-1"></i>
                                @break
                            @case('phosphor')
                                ⚡
                                @break
                            @case('tabler')
                                <i class="ti ti-icons me-1"></i>
                                @break
                            @case('flags')
                                🏳️
                                @break
                            @case('emojis')
                                😀
                                @break
                        @endswitch
                        <span class="d-none d-md-inline">{{ $collection['name'] }}</span>
                        <span class="badge bg-secondary ms-1">{{ count($collection['icons']) }}</span>
                    </button>
                </li>
            @endforeach
        </ul>
    </div>

    <!-- Barre de recherche -->
    <div class="search-section mb-4">
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           class="form-control" 
                           placeholder="Rechercher une icône..."
                           wire:model.live.debounce.300ms="search">
                    @if($search)
                        <button class="btn btn-outline-secondary" 
                                type="button" 
                                wire:click="$set('search', '')">
                            <i class="fas fa-times"></i>
                        </button>
                    @endif
                </div>
            </div>
            <div class="col-md-6 d-flex align-items-center">
                <span class="text-muted">
                    {{ count($filteredCollection['icons']) }} icônes trouvées
                    @if($search)
                        pour "{{ $search }}"
                    @endif
                </span>
            </div>
        </div>
    </div>

    <!-- Informations sur le fournisseur actuel -->
    <div class="provider-info mb-3">
        <div class="alert alert-info d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <div>
                <strong>{{ $filteredCollection['name'] }}</strong>
                @if($filteredCollection['prefix'])
                    - Format: <code>{{ $filteredCollection['prefix'] }}nom-icone</code>
                @else
                    - Format: Emojis Unicode
                @endif
            </div>
        </div>
    </div>

    <!-- Grille d'icônes -->
    <div class="icons-grid">
        @if(count($filteredCollection['icons']) > 0)
            <div class="row g-2">
                @foreach($filteredCollection['icons'] as $icon)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="icon-card" onclick="copyIconCode('{{ $activeTab === 'emojis' ? $icon : $filteredCollection['prefix'] . $icon }}')">
                            <div class="icon-display">
                                @if($activeTab === 'flags')
                                    <span class="fi fi-{{ $icon }}" style="font-size: 2rem;"></span>
                                @elseif($activeTab === 'emojis')
                                    <span style="font-size: 2rem;">{{ $icon }}</span>
                                @else
                                    <i class="{{ $filteredCollection['prefix'] }}{{ $icon }}" style="font-size: 2rem;"></i>
                                @endif
                            </div>
                            <div class="icon-info">
                                <div class="icon-name">
                                    @if($activeTab === 'emojis')
                                        {{ $icon }}
                                    @else
                                        {{ $icon }}
                                    @endif
                                </div>
                                <div class="icon-code">
                                    <small class="text-muted">
                                        @if($activeTab === 'emojis')
                                            {{ $icon }}
                                        @else
                                            {{ $filteredCollection['prefix'] }}{{ $icon }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            <div class="copy-indicator">
                                <i class="fas fa-copy"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h4>Aucune icône trouvée</h4>
                <p class="text-muted">
                    @if($search)
                        Aucune icône ne correspond à votre recherche "{{ $search }}"
                        <br>
                        <button class="btn btn-sm btn-outline-primary mt-2" wire:click="$set('search', '')">
                            Effacer la recherche
                        </button>
                    @else
                        Il n'y a pas d'icônes dans cette catégorie
                    @endif
                </p>
            </div>
        @endif
    </div>

    <style>
    .icon-gallery-container {
        padding: 1rem;
        max-width: 100%;
    }

    .gallery-header {
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 1rem;
    }

    .nav-tabs .nav-link {
        border: 1px solid transparent;
        border-bottom: 2px solid transparent;
        color: #6c757d;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        color: #495057;
    }

    .nav-tabs .nav-link.active {
        color: #495057;
        background-color: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
        border-bottom-color: transparent;
    }

    .icon-card {
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 1rem;
        text-align: center;
        transition: all 0.2s ease;
        cursor: pointer;
        height: 120px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
    }

    .icon-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #007bff;
    }

    .icon-card:hover .copy-indicator {
        opacity: 1;
        transform: translateY(0);
    }

    .copy-indicator {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #007bff;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .copy-indicator:hover {
        background: #0056b3;
        transform: translateY(0) scale(1.1);
    }

    .icon-display {
        margin-bottom: 0.5rem;
        color: #495057;
    }

    .icon-name {
        font-weight: 500;
        font-size: 0.875rem;
        color: #495057;
        margin-bottom: 0.25rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .icon-code {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .search-section {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 1rem;
    }

    .provider-info .alert {
        margin-bottom: 0;
        border-radius: 8px;
    }

    .icons-grid {
        min-height: 200px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .nav-tabs .nav-link span.d-none {
            display: none !important;
        }
        
        .nav-tabs .nav-link {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
        
        .icon-card {
            height: 100px;
            padding: 0.75rem;
        }
        
        .icon-display {
            font-size: 1.5rem !important;
        }
    }

    @media (max-width: 576px) {
        .gallery-header .d-flex {
            flex-direction: column;
            gap: 1rem;
            align-items: start !important;
        }
        
        .search-section .row {
            flex-direction: column;
        }
        
        .search-section .col-md-6:last-child {
            margin-top: 1rem;
        }
    }

    /* Flag icons support */
    .fi {
        font-size: inherit !important;
        line-height: 1;
    }

    /* Animation for icon loading */
    .icon-card {
        animation: fadeInUp 0.3s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    </style>
</div>