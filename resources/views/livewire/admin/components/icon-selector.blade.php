<div class="icon-selector">
    <!-- Recherche et Catégories -->
    <div class="row mb-3">
        <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Rechercher une icône..."
                   wire:model.live.debounce.300ms="searchQuery">
        </div>
        <div class="col-md-6">
            <select class="form-select" wire:model.live="activeCategory">
                @foreach($categories as $id => $name)
                    <option value="{{ $id }}">{{ $name }}</option>
                @endforeach
            </select>
        </div>
    </div>
    
    <!-- Grille d'icônes -->
    <div class="icon-grid">
        @forelse($filteredIcons as $icon)
            <div class="icon-item {{ $selectedIcon === $icon ? 'active' : '' }}"
                 wire:click="selectIcon('{{ $icon }}')">
                <i class="{{ $icon }}"></i>
            </div>
        @empty
            <div class="text-center p-4 text-muted">
                Aucune icône trouvée pour cette recherche.
            </div>
        @endforelse
    </div>
    
    <!-- Aperçu de l'icône sélectionnée -->
    @if($selectedIcon)
        <div class="selected-icon-preview mt-3 p-3 border rounded text-center">
            <div class="mb-2">Icône sélectionnée</div>
            <div class="selected-icon">
                <i class="{{ $selectedIcon }}"></i>
            </div>
        </div>
    @endif
    
    <style>
        .icon-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 10px;
            max-height: 300px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
        }
        
        .icon-item {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 50px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .icon-item:hover {
            background-color: #f8f9fa;
            transform: scale(1.05);
        }
        
        .icon-item.active {
            background-color: #e8f4fc;
            border-color: #2563eb;
            color: #2563eb;
        }
        
        .selected-icon {
            font-size: 32px;
        }
    </style>
</div>