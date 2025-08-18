<div class="universal-media-selector">
<style>
/* Styles critiques pour le modal */
.ums-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.75);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(3px);
    animation: fadeIn 0.3s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideIn {
    from { 
        opacity: 0;
        transform: scale(0.95) translateY(-20px);
    }
    to { 
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

.ums-modal {
    background: white;
    border-radius: 12px;
    max-width: 90vw;
    max-height: 90vh;
    width: 1200px;
    height: 800px;
    display: flex;
    flex-direction: column;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    animation: slideIn 0.3s ease-out;
    border: 1px solid #e1e5e9;
}

.ums-header {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.ums-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #2c3e50;
    margin: 0;
}

.ums-selection-count {
    font-size: 0.875rem;
    color: #0073aa;
    background: #e7f3ff;
    padding: 4px 8px;
    border-radius: 4px;
    margin-left: 12px;
}

.ums-header-right {
    display: flex;
    gap: 8px;
}

.ums-btn-icon {
    background: none;
    border: 1px solid #ddd;
    border-radius: 4px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

.ums-btn-icon:hover {
    background: #f0f0f0;
    border-color: #0073aa;
}

.ums-content {
    display: flex;
    flex: 1;
    overflow: hidden;
}

.ums-sidebar {
    width: 280px;
    background: #f8f9fa;
    border-right: 1px solid #e9ecef;
    padding: 16px;
    overflow-y: auto;
    flex-shrink: 0;
}

.ums-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
    height: 100%;
    min-height: 0; /* Important pour le flexbox scroll */
}

.ums-toolbar {
    background: white;
    border-bottom: 1px solid #e9ecef;
    padding: 12px 16px;
    display: flex;
    gap: 12px;
    align-items: center;
    flex-shrink: 0;
}

.ums-media-grid {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px;
    height: 100%;
    max-height: calc(100vh - 300px);
}

.ums-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 16px;
}

.ums-media-item {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
    cursor: pointer;
    transition: all 0.2s ease;
    background: white;
    position: relative;
}

.ums-media-item:hover {
    border-color: #0073aa;
    transform: scale(1.02);
}

.ums-media-item.selected {
    border-color: #0073aa;
    box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.2);
}

.ums-media-thumbnail {
    width: 100%;
    height: 140px;
    object-fit: cover;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.ums-media-thumbnail img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.2s ease;
}

.ums-media-item:hover .ums-media-thumbnail img {
    transform: scale(1.05);
}

.ums-file-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    width: 100%;
    height: 100%;
    color: #666;
    font-size: 2.5rem;
}

.ums-file-icon.ums-video {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.ums-file-icon.ums-audio {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
}

.ums-file-icon.ums-document {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    color: white;
}

.ums-file-icon.ums-archive {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    color: white;
}

.ums-file-icon.ums-other {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    color: white;
}

.ums-file-type-label {
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-top: 4px;
    letter-spacing: 0.5px;
}

.ums-media-info {
    padding: 12px;
    border-top: 1px solid #f0f0f0;
}

.ums-media-title {
    font-size: 0.875rem;
    font-weight: 500;
    margin: 0 0 4px 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.ums-media-meta {
    font-size: 0.75rem;
    color: #666;
}

.ums-selection-indicator {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #0073aa;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.ums-media-item.selected .ums-selection-indicator {
    opacity: 1;
}

.ums-footer {
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-shrink: 0;
}

.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: #0073aa;
    color: white;
}

.btn-primary:hover {
    background: #005a87;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

/* Styles pour les formulaires et inputs */
.form-control, .ums-input, .ums-select, .ums-url-input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 0.875rem;
    width: 100%;
    transition: border-color 0.2s ease;
}

.form-control:focus, .ums-input:focus, .ums-select:focus, .ums-url-input:focus {
    border-color: #0073aa;
    outline: none;
    box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.1);
}

.ums-tabs {
    display: flex;
    border-bottom: 1px solid #e9ecef;
    background: #f8f9fa;
}

.ums-tab {
    padding: 12px 20px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
}

.ums-tab:hover {
    background: #e9ecef;
}

.ums-tab.active {
    background: white;
    border-bottom-color: #0073aa;
    color: #0073aa;
}

.ums-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 40px 20px;
    text-align: center;
    transition: all 0.2s ease;
    margin: 20px 0;
}

.ums-upload-area.drag-active {
    border-color: #0073aa;
    background: #f0f8ff;
}

.ums-upload-icon {
    font-size: 3rem;
    color: #ccc;
    margin-bottom: 16px;
}

.ums-url-input-group {
    display: flex;
    gap: 12px;
    margin: 16px 0;
}

.ums-url-input {
    flex: 1;
}

.ums-btn {
    padding: 8px 16px;
    border: none;
    border-radius: 4px;
    font-size: 0.875rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
}

.ums-btn-primary {
    background: #0073aa;
    color: white;
}

.ums-btn-primary:hover {
    background: #005a87;
}

.ums-btn-secondary {
    background: #6c757d;
    color: white;
}

.ums-btn-secondary:hover {
    background: #545b62;
}

.ums-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.ums-upload-preview {
    margin-top: 20px;
}

.ums-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    gap: 12px;
}

.ums-preview-item {
    position: relative;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    overflow: hidden;
    background: white;
}

.ums-preview-image {
    width: 100%;
    height: 80px;
    object-fit: cover;
    background: #f8f9fa;
}

.ums-preview-remove {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    font-size: 0.75rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ums-filters {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 12px 16px;
}

.ums-filter-group {
    display: flex;
    gap: 12px;
    align-items: center;
    margin-bottom: 8px;
}

.ums-filter-group label {
    font-size: 0.875rem;
    font-weight: 500;
    margin-right: 6px;
}

.ums-search-box {
    margin-bottom: 12px;
}

.text-muted {
    color: #666 !important;
}

.text-success {
    color: #28a745 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-primary {
    color: #0073aa !important;
}

.fas {
    font-family: "Font Awesome 5 Free";
    font-weight: 900;
}

/* Responsive */
@media (max-width: 768px) {
    .ums-modal {
        width: 95vw;
        height: 95vh;
        margin: 0;
    }
    
    .ums-content {
        flex-direction: column;
    }
    
    .ums-sidebar {
        width: 100%;
        max-height: 200px;
    }
    
    .ums-grid {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        gap: 12px;
    }
    
    .ums-filter-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .ums-url-input-group {
        flex-direction: column;
    }
}

/* Styles supplémentaires pour améliorer l'apparence */
.ums-empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.ums-empty-icon {
    font-size: 4rem;
    color: #ddd;
    margin-bottom: 20px;
}

.ums-stats {
    display: flex;
    gap: 20px;
    background: #f8f9fa;
    padding: 12px 16px;
    font-size: 0.875rem;
    border-top: 1px solid #e9ecef;
}

.ums-stat-item {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #666;
}

.ums-loading {
    text-align: center;
    padding: 40px;
    color: #666;
}

.ums-loading .fas {
    font-size: 2rem;
    color: #0073aa;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Améliorations pour les tables */
.ums-media-list {
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    height: 100%;
    max-height: calc(100vh - 300px);
}

.ums-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
}

.ums-table th {
    background: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 12px 8px;
    text-align: left;
    font-weight: 600;
    font-size: 0.875rem;
    color: #495057;
}

.ums-table td {
    padding: 12px 8px;
    border-bottom: 1px solid #f0f0f0;
    vertical-align: middle;
}

.ums-table tbody tr:hover {
    background: #f8f9fa;
}

.ums-table tbody tr.selected {
    background: #e7f3ff;
    border-left: 3px solid #0073aa;
}

.ums-thumb-cell img {
    width: 60px;
    height: 40px;
    object-fit: cover;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.ums-thumb-cell .ums-file-icon {
    width: 60px;
    height: 40px;
    border-radius: 4px;
    font-size: 1.5rem;
}

.ums-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    padding: 16px;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
}

.ums-pagination button {
    padding: 6px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.ums-pagination button:hover:not(:disabled) {
    background: #f8f9fa;
    border-color: #0073aa;
}

.ums-pagination button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.ums-pagination .active {
    background: #0073aa;
    color: white;
    border-color: #0073aa;
}

/* Styles pour les messages de statut */
.ums-upload-results {
    margin-top: 20px;
}

.ums-upload-success, .ums-upload-errors {
    padding: 16px;
    border-radius: 6px;
    margin-bottom: 16px;
}

.ums-upload-success {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
}

.ums-upload-errors {
    background: #f8d7da;
    border: 1px solid #f5c6cb;
    color: #721c24;
}

.ums-result-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
}

/* Styles pour l'édition inline */
.ums-inline-edit {
    position: relative;
}

.ums-inline-edit input {
    width: 100%;
    padding: 4px 8px;
    border: 1px solid #0073aa;
    border-radius: 3px;
    font-size: inherit;
}

.ums-edit-actions {
    margin-top: 8px;
    display: flex;
    gap: 6px;
}

.ums-edit-actions button {
    padding: 4px 8px;
    font-size: 0.75rem;
    border-radius: 3px;
}

.ums-image-error {
    color: #dc3545 !important;
    background: #f8d7da !important;
}

/* Styles de scrollbar personnalisés */
.ums-media-grid::-webkit-scrollbar,
.ums-media-list::-webkit-scrollbar,
.ums-sidebar::-webkit-scrollbar {
    width: 8px;
}

.ums-media-grid::-webkit-scrollbar-track,
.ums-media-list::-webkit-scrollbar-track,
.ums-sidebar::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.ums-media-grid::-webkit-scrollbar-thumb,
.ums-media-list::-webkit-scrollbar-thumb,
.ums-sidebar::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
    transition: background 0.2s ease;
}

.ums-media-grid::-webkit-scrollbar-thumb:hover,
.ums-media-list::-webkit-scrollbar-thumb:hover,
.ums-sidebar::-webkit-scrollbar-thumb:hover {
    background: #0073aa;
}

/* Pour Firefox */
.ums-media-grid,
.ums-media-list,
.ums-sidebar {
    scrollbar-width: thin;
    scrollbar-color: #c1c1c1 #f1f1f1;
}
</style>
    @if($isOpen)
    <!-- Modal Overlay -->
    <div class="ums-modal-overlay" wire:click="closeModal">
        <div class="ums-modal" onclick="event.stopPropagation()">
            <!-- Header -->
            <div class="ums-header">
                <div class="ums-header-left">
                    <h2 class="ums-title">{{ $modalTitle }}</h2>
                    @if($selectionCount > 0)
                    <span class="ums-selection-count">
                        {{ $selectionCount }} {{ $selectionCount === 1 ? 'élément sélectionné' : 'éléments sélectionnés' }}
                        @if($maxFiles > 1)
                            <span class="ums-max-info">(max {{ $maxFiles }})</span>
                        @endif
                    </span>
                    @endif
                </div>
                <div class="ums-header-right">
                    <button class="ums-btn-icon ums-btn-view-toggle" wire:click="toggleViewMode" title="Changer la vue">
                        <i class="fas {{ $viewMode === 'grid' ? 'fa-list' : 'fa-th' }}"></i>
                    </button>
                    <button class="ums-btn-icon ums-btn-close" wire:click="closeModal" title="Fermer">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            <div class="ums-content">
                <!-- Sidebar Navigation -->
                <div class="ums-sidebar">
                    <!-- Tabs -->
                    <div class="ums-tabs">
                        <button class="ums-tab {{ $currentTab === 'library' ? 'active' : '' }}" 
                                wire:click="switchTab('library')">
                            <i class="fas fa-folder-open"></i>
                            <span>Bibliothèque</span>
                            <span class="ums-count">{{ $stats['total'] }}</span>
                        </button>
                        <button class="ums-tab {{ $currentTab === 'upload' ? 'active' : '' }}" 
                                wire:click="switchTab('upload')">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <span>Télécharger</span>
                        </button>
                        <button class="ums-tab {{ $currentTab === 'url' ? 'active' : '' }}" 
                                wire:click="switchTab('url')">
                            <i class="fas fa-link"></i>
                            <span>Depuis URL</span>
                        </button>
                    </div>

                    <!-- Filters -->
                    @if($currentTab === 'library')
                    <div class="ums-filters">
                        <h4>Filtres</h4>
                        
                        <!-- Type Filter -->
                        <div class="ums-filter-group">
                            <label>Type de média</label>
                            <select wire:model.live="typeFilter" class="ums-select">
                                <option value="all">Tous ({{ $stats['total'] }})</option>
                                @if(in_array('image', $allowedTypes))
                                <option value="image">Images ({{ $stats['image'] }})</option>
                                @endif
                                @if(in_array('video', $allowedTypes))
                                <option value="video">Vidéos ({{ $stats['video'] }})</option>
                                @endif
                                @if(in_array('audio', $allowedTypes))
                                <option value="audio">Audio ({{ $stats['audio'] }})</option>
                                @endif
                                @if(in_array('document', $allowedTypes))
                                <option value="document">Documents ({{ $stats['document'] }})</option>
                                @endif
                            </select>
                        </div>

                        <!-- Date Filter -->
                        <div class="ums-filter-group">
                            <label>Période</label>
                            <select wire:model.live="dateFilter" class="ums-select">
                                <option value="all">Toutes les dates</option>
                                <option value="today">Aujourd'hui</option>
                                <option value="week">Cette semaine</option>
                                <option value="month">Ce mois</option>
                                <option value="year">Cette année</option>
                            </select>
                        </div>

                        <!-- Sort -->
                        <div class="ums-filter-group">
                            <label>Trier par</label>
                            <select wire:model.live="sortBy" class="ums-select">
                                <option value="date">Date</option>
                                <option value="title">Titre</option>
                                <option value="size">Taille</option>
                                <option value="random">Aléatoire</option>
                            </select>
                            <div class="ums-sort-direction">
                                <button class="ums-btn-icon {{ $sortDirection === 'desc' ? 'active' : '' }}" 
                                        wire:click="$set('sortDirection', 'desc')" title="Décroissant">
                                    <i class="fas fa-sort-amount-down"></i>
                                </button>
                                <button class="ums-btn-icon {{ $sortDirection === 'asc' ? 'active' : '' }}" 
                                        wire:click="$set('sortDirection', 'asc')" title="Croissant">
                                    <i class="fas fa-sort-amount-up"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Reset Filters -->
                        <button class="ums-btn ums-btn-reset" wire:click="resetFilters">
                            <i class="fas fa-undo-alt"></i>
                            Réinitialiser
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Main Content -->
                <div class="ums-main {{ $detailsPanel ? 'with-details' : '' }}">
                    <!-- Search and Actions Bar -->
                    @if($currentTab === 'library')
                    <div class="ums-toolbar">
                        <div class="ums-search-wrapper">
                            <i class="fas fa-search ums-search-icon"></i>
                            <input type="text" 
                                   class="ums-search" 
                                   placeholder="Rechercher dans les médias..." 
                                   wire:model.live.debounce.300ms="search">
                            @if($search)
                            <button class="ums-search-clear" wire:click="$set('search', '')">
                                <i class="fas fa-times"></i>
                            </button>
                            @endif
                        </div>

                        <div class="ums-bulk-actions">
                            @if($selectionCount > 0 && $selectionMode === 'multiple')
                            <div class="ums-selection-actions">
                                <button class="ums-btn ums-btn-sm" wire:click="deselectAll">
                                    Tout désélectionner
                                </button>
                                <button class="ums-btn ums-btn-danger ums-btn-sm" wire:click="bulkDelete">
                                    <i class="fas fa-trash"></i>
                                    Supprimer ({{ $selectionCount }})
                                </button>
                            </div>
                            @endif
                            
                            @if($selectionMode === 'multiple')
                            <button class="ums-btn ums-btn-sm" wire:click="selectAll">
                                <i class="fas fa-check-square"></i>
                                Tout sélectionner
                            </button>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!-- Content Area -->
                    <div class="ums-content-area">
                        <!-- Library Tab -->
                        @if($currentTab === 'library')
                            @if($media->count() > 0)
                                <!-- Grid View -->
                                @if($viewMode === 'grid')
                                <div class="ums-media-grid">
                                    @foreach($media as $item)
                                    <div class="ums-media-item {{ in_array($item->id, $selectedMedia) ? 'selected' : '' }}" 
                                         wire:key="media-{{ $item->id }}"
                                         wire:click="toggleMedia({{ $item->id }})"
                                         x-data="{ shiftPressed: false }"
                                         @click="if (shiftPressed) $wire.call('toggleMedia', {{ $item->id }}, true)"
                                         @keydown.shift="shiftPressed = true"
                                         @keyup.shift="shiftPressed = false">
                                        
                                        <div class="ums-media-thumbnail">
                                            @if($item->type === 'image')
                                                @php
                                                    $imagePath = $item->thumbnail_path ?? $item->path;
                                                    // Si c'est une URL externe (http/https), l'utiliser directement
                                                    if (str_starts_with($imagePath, 'http')) {
                                                        $imageUrl = $imagePath;
                                                    } elseif (str_starts_with($imagePath, 'storage/')) {
                                                        $imageUrl = asset($imagePath);
                                                    } else {
                                                        $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
                                                    }
                                                @endphp
                                                <img src="{{ $imageUrl }}" 
                                                     alt="{{ $item->alt_text ?? $item->original_name }}"
                                                     loading="lazy"
                                                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'ums-file-icon ums-image-error\'><i class=\'fas fa-image\'></i><span class=\'ums-file-type-label\'>Image non trouvée</span></div>';">
                                            @elseif($item->type === 'video')
                                                <div class="ums-file-icon ums-video">
                                                    <i class="fas fa-play-circle"></i>
                                                </div>
                                            @elseif($item->type === 'audio')
                                                <div class="ums-file-icon ums-audio">
                                                    <i class="fas fa-music"></i>
                                                </div>
                                            @else
                                                <div class="ums-file-icon ums-document">
                                                    <i class="fas fa-file-alt"></i>
                                                </div>
                                            @endif
                                            
                                            <!-- Selection Overlay -->
                                            @if(in_array($item->id, $selectedMedia))
                                            <div class="ums-selection-overlay">
                                                <div class="ums-checkmark">
                                                    <i class="fas fa-check"></i>
                                                </div>
                                            </div>
                                            @endif

                                            <!-- Quick Actions -->
                                            <div class="ums-media-actions">
                                                <button class="ums-btn-icon" 
                                                        wire:click.stop="toggleDetailsPanel({{ $item->id }})"
                                                        title="Détails">
                                                    <i class="fas fa-info-circle"></i>
                                                </button>
                                                <button class="ums-btn-icon" 
                                                        wire:click.stop="deleteMedia({{ $item->id }})"
                                                        onclick="return confirm('Supprimer ce média ?')"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="ums-media-info">
                                            <div class="ums-media-title" title="{{ $item->title ?? $item->original_name }}">
                                                @if($editingMedia === $item->id && $editingField === 'title')
                                                <input type="text" 
                                                       class="ums-inline-edit" 
                                                       wire:model="editingValue"
                                                       wire:keydown.enter="saveEditing"
                                                       wire:keydown.escape="cancelEditing"
                                                       autofocus>
                                                @else
                                                <span wire:dblclick="startEditing({{ $item->id }}, 'title')">
                                                    {{ Str::limit($item->title ?? $item->original_name, 30) }}
                                                </span>
                                                @endif
                                            </div>
                                            <div class="ums-media-meta">
                                                <span class="ums-file-size">{{ $this->formatFileSize($item->size ?? $item->file_size ?? 0) }}</span>
                                                @if(isset($item->dimensions) && $item->dimensions)
                                                <span class="ums-dimensions">{{ $item->dimensions['width'] }}×{{ $item->dimensions['height'] }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <!-- List View -->
                                <div class="ums-media-list">
                                    <table class="ums-table">
                                        <thead>
                                            <tr>
                                                @if($selectionMode === 'multiple')
                                                <th class="ums-select-col">
                                                    <input type="checkbox" 
                                                           wire:click="selectAll"
                                                           {{ $selectionCount === $media->total() && $media->total() > 0 ? 'checked' : '' }}>
                                                </th>
                                                @endif
                                                <th class="ums-thumb-col">Aperçu</th>
                                                <th class="ums-title-col">Nom</th>
                                                <th class="ums-type-col">Type</th>
                                                <th class="ums-size-col">Taille</th>
                                                <th class="ums-date-col">Date</th>
                                                <th class="ums-actions-col">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($media as $item)
                                            <tr class="ums-media-row {{ in_array($item->id, $selectedMedia) ? 'selected' : '' }}"
                                                wire:key="media-list-{{ $item->id }}">
                                                @if($selectionMode === 'multiple')
                                                <td>
                                                    <input type="checkbox" 
                                                           wire:click="toggleMedia({{ $item->id }})"
                                                           {{ in_array($item->id, $selectedMedia) ? 'checked' : '' }}>
                                                </td>
                                                @endif
                                                <td class="ums-thumb-cell" wire:click="toggleMedia({{ $item->id }})">
                                                    @if($item->type === 'image')
                                                        @php
                                                            $imagePath = $item->thumbnail_path ?? $item->path;
                                                            if (str_starts_with($imagePath, 'http')) {
                                                                $imageUrl = $imagePath;
                                                            } elseif (str_starts_with($imagePath, 'storage/')) {
                                                                $imageUrl = asset($imagePath);
                                                            } else {
                                                                $imageUrl = asset('storage/' . ltrim($imagePath, '/'));
                                                            }
                                                        @endphp
                                                        <img src="{{ $imageUrl }}" 
                                                             alt="{{ $item->alt_text ?? $item->original_name }}"
                                                             onerror="this.onerror=null; this.src='data:image/svg+xml,<svg xmlns=\'http://www.w3.org/2000/svg\' width=\'60\' height=\'40\' viewBox=\'0 0 60 40\'><rect width=\'60\' height=\'40\' fill=\'%23f8f9fa\'/><text x=\'30\' y=\'22\' text-anchor=\'middle\' fill=\'%23666\' font-size=\'10\'>Image</text></svg>';">
                                                    @else
                                                        <div class="ums-file-icon ums-{{ $item->type }}">
                                                            <i class="fas fa-{{ $item->type === 'video' ? 'play-circle' : ($item->type === 'audio' ? 'music' : 'file-alt') }}"></i>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td class="ums-title-cell" wire:click="toggleMedia({{ $item->id }})">
                                                    <strong>{{ $item->title ?? $item->original_name }}</strong>
                                                    @if($item->description)
                                                    <br><small class="ums-description">{{ Str::limit($item->description, 60) }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="ums-badge ums-{{ $item->type }}">{{ strtoupper($item->type) }}</span>
                                                </td>
                                                <td>{{ $this->formatFileSize($item->size ?? $item->file_size ?? 0) }}</td>
                                                <td>{{ $item->created_at ? $item->created_at->format('d/m/Y H:i') : '-' }}</td>
                                                <td>
                                                    <div class="ums-row-actions">
                                                        <button class="ums-btn-icon" 
                                                                wire:click="toggleDetailsPanel({{ $item->id }})"
                                                                title="Détails">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>
                                                        <button class="ums-btn-icon ums-danger" 
                                                                wire:click="deleteMedia({{ $item->id }})"
                                                                onclick="return confirm('Supprimer ce média ?')"
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
                                @endif

                                <!-- Pagination -->
                                <div class="ums-pagination-wrapper">
                                    {{ $media->links('pagination::bootstrap-4') }}
                                </div>
                            @else
                                <!-- Empty State -->
                                <div class="ums-empty-state">
                                    <div class="ums-empty-icon">
                                        <i class="fas fa-images"></i>
                                    </div>
                                    <h3>Aucun média trouvé</h3>
                                    @if($search)
                                        <p>Aucun résultat pour "{{ $search }}"</p>
                                        <button class="ums-btn" wire:click="$set('search', '')">Effacer la recherche</button>
                                    @else
                                        <p>Commencez par télécharger vos premiers fichiers</p>
                                        <button class="ums-btn ums-btn-primary" wire:click="switchTab('upload')">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            Télécharger des fichiers
                                        </button>
                                    @endif
                                </div>
                            @endif
                        @endif

                        <!-- Upload Tab -->
                        @if($currentTab === 'upload')
                        <div class="ums-upload-area">
                            @if(!$isUploading)
                            <!-- Drop Zone -->
                            <div class="ums-dropzone {{ $dragActive ? 'drag-active' : '' }}" 
                                 x-data="{ dragCount: 0 }"
                                 @dragenter="dragCount++; $wire.dragActive = true"
                                 @dragleave="dragCount--; if (dragCount === 0) $wire.dragActive = false"
                                 @dragover.prevent
                                 @drop.prevent="
                                     dragCount = 0; 
                                     $wire.dragActive = false;
                                     let files = Array.from($event.dataTransfer.files);
                                     if (files.length > 0) $wire.call('handleFilesDrop', files);
                                 ">
                                
                                <div class="ums-dropzone-content">
                                    <div class="ums-upload-icon">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                    </div>
                                    <h3>Glissez vos fichiers ici</h3>
                                    <p>ou</p>
                                    <label for="ums-file-input" class="ums-btn ums-btn-primary">
                                        <i class="fas fa-folder-open"></i>
                                        Sélectionner des fichiers
                                    </label>
                                    <input type="file" 
                                           id="ums-file-input" 
                                           class="ums-file-input" 
                                           wire:model="uploadFiles" 
                                           multiple
                                           accept="{{ !empty($allowedMimeTypes) ? implode(',', $allowedMimeTypes) : '*/*' }}">
                                </div>

                                <div class="ums-upload-info">
                                    <p><i class="fas fa-info-circle"></i> Taille maximum : {{ $maxFileSize/1024 }}MB par fichier</p>
                                    @if(!empty($allowedTypes))
                                    <p><i class="fas fa-file-alt"></i> Types autorisés : {{ implode(', ', $allowedTypes) }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- File Preview -->
                            @if($uploadFiles && count($uploadFiles) > 0)
                            <div class="ums-upload-preview">
                                <h4>Fichiers sélectionnés ({{ count($uploadFiles) }})</h4>
                                <div class="ums-upload-list">
                                    @foreach($uploadFiles as $index => $file)
                                    <div class="ums-upload-item" wire:key="upload-{{ $index }}">
                                        <div class="ums-upload-thumb">
                                            @if(str_starts_with($file->getMimeType(), 'image/'))
                                                <img src="{{ $file->temporaryUrl() }}" alt="Preview">
                                            @else
                                                <div class="ums-file-icon">
                                                    <i class="fas fa-file"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="ums-upload-info">
                                            <div class="ums-upload-name">{{ $file->getClientOriginalName() }}</div>
                                            <div class="ums-upload-meta">
                                                {{ $this->formatFileSize($file->getSize()) }}
                                                <span class="ums-upload-type">{{ strtoupper($file->getClientOriginalExtension()) }}</span>
                                            </div>
                                        </div>
                                        <button class="ums-upload-remove" wire:click="removeUploadFile({{ $index }})">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    @endforeach
                                </div>
                                
                                <div class="ums-upload-actions">
                                    <!-- Bouton de test -->
                                    <button class="ums-btn ums-btn-info" wire:click="testUpload" style="background: #17a2b8; margin-right: 8px;">
                                        <i class="fas fa-flask"></i>
                                        Test Livewire
                                    </button>
                                    
                                    <button class="ums-btn ums-btn-primary" wire:click="uploadFiles">
                                        <i class="fas fa-upload"></i>
                                        Télécharger {{ count($uploadFiles) }} fichier(s)
                                    </button>
                                    <button class="ums-btn ums-btn-secondary" wire:click="$set('uploadFiles', [])">
                                        <i class="fas fa-times"></i>
                                        Annuler
                                    </button>
                                </div>
                            </div>
                            @endif
                            @else
                            <!-- Upload Progress -->
                            <div class="ums-upload-progress">
                                <div class="ums-progress-icon">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <h3>Téléchargement en cours...</h3>
                                
                                @foreach($uploadProgress as $index => $progress)
                                <div class="ums-progress-item">
                                    <div class="ums-progress-bar">
                                        <div class="ums-progress-fill" style="width: {{ $progress }}%"></div>
                                    </div>
                                    <span class="ums-progress-text">{{ $progress }}%</span>
                                </div>
                                @endforeach
                            </div>
                            @endif

                            <!-- Upload Results -->
                            @if(!empty($uploadSuccess) || !empty($uploadErrors))
                            <div class="ums-upload-results">
                                @if(!empty($uploadSuccess))
                                <div class="ums-upload-success">
                                    <h4><i class="fas fa-check-circle"></i> Téléchargement réussi</h4>
                                    @foreach($uploadSuccess as $success)
                                    <div class="ums-result-item">
                                        <i class="fas fa-check text-success"></i>
                                        {{ $success['name'] }}
                                    </div>
                                    @endforeach
                                </div>
                                @endif

                                @if(!empty($uploadErrors))
                                <div class="ums-upload-errors">
                                    <h4><i class="fas fa-exclamation-circle"></i> Erreurs de téléchargement</h4>
                                    @foreach($uploadErrors as $error)
                                    <div class="ums-result-item">
                                        <i class="fas fa-times text-danger"></i>
                                        {{ $error['name'] }} : {{ $error['error'] }}
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- URL Tab -->
                        @if($currentTab === 'url')
                        <div class="ums-url-upload">
                            <div class="ums-url-form">
                                <h3>Importer depuis une URL</h3>
                                <p>Collez l'URL d'un fichier image, vidéo ou document</p>
                                
                                <div class="ums-url-input-group">
                                    <input type="url" 
                                           class="ums-url-input" 
                                           wire:model="uploadUrl"
                                           placeholder="https://exemple.com/image.jpg"
                                           {{ $urlUploading ? 'disabled' : '' }}>
                                    <button class="ums-btn ums-btn-primary" 
                                            wire:click="uploadFromUrl"
                                            {{ $urlUploading || empty($uploadUrl) ? 'disabled' : '' }}>
                                        @if($urlUploading)
                                            <i class="fas fa-spinner fa-spin"></i>
                                        @else
                                            <i class="fas fa-download"></i>
                                        @endif
                                        Importer
                                    </button>
                                </div>
                                
                                @error('uploadUrl')
                                <div class="ums-error">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="ums-url-examples">
                                <h4>Exemples d'URLs supportées :</h4>
                                <ul>
                                    <li>Images : JPG, PNG, GIF, WebP</li>
                                    <li>Vidéos : MP4, MOV, AVI</li>
                                    <li>Documents : PDF, DOC, DOCX</li>
                                </ul>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Details Panel -->
                @if($detailsPanel && $selectedForDetails)
                <div class="ums-details-panel">
                    @php $detailsMedia = $media->where('id', $selectedForDetails)->first(); @endphp
                    @if($detailsMedia)
                    <div class="ums-details-header">
                        <h4>Détails du média</h4>
                        <button class="ums-btn-icon" wire:click="toggleDetailsPanel()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <div class="ums-details-content">
                        <!-- Preview -->
                        <div class="ums-details-preview">
                            @if($detailsMedia->type === 'image')
                                <img src="{{ asset($detailsMedia->path) }}" alt="{{ $detailsMedia->alt_text }}">
                            @else
                                <div class="ums-file-icon ums-large ums-{{ $detailsMedia->type }}">
                                    <i class="fas fa-{{ $detailsMedia->type === 'video' ? 'play-circle' : ($detailsMedia->type === 'audio' ? 'music' : 'file-alt') }}"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Editable Fields -->
                        <div class="ums-details-fields">
                            <!-- Title -->
                            <div class="ums-field-group">
                                <label>Titre</label>
                                @if($editingMedia === $detailsMedia->id && $editingField === 'title')
                                <div class="ums-edit-group">
                                    <input type="text" class="ums-input" wire:model="editingValue">
                                    <button class="ums-btn ums-btn-sm" wire:click="saveEditing">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="ums-btn ums-btn-sm" wire:click="cancelEditing">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @else
                                <div class="ums-editable" wire:click="startEditing({{ $detailsMedia->id }}, 'title')">
                                    {{ $detailsMedia->title ?? $detailsMedia->original_name }}
                                    <i class="fas fa-edit ums-edit-icon"></i>
                                </div>
                                @endif
                            </div>

                            <!-- Alt Text (Images only) -->
                            @if($detailsMedia->type === 'image')
                            <div class="ums-field-group">
                                <label>Texte alternatif</label>
                                @if($editingMedia === $detailsMedia->id && $editingField === 'alt_text')
                                <div class="ums-edit-group">
                                    <input type="text" class="ums-input" wire:model="editingValue">
                                    <button class="ums-btn ums-btn-sm" wire:click="saveEditing">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="ums-btn ums-btn-sm" wire:click="cancelEditing">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @else
                                <div class="ums-editable" wire:click="startEditing({{ $detailsMedia->id }}, 'alt_text')">
                                    {{ $detailsMedia->alt_text ?: 'Cliquer pour ajouter' }}
                                    <i class="fas fa-edit ums-edit-icon"></i>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Description -->
                            <div class="ums-field-group">
                                <label>Description</label>
                                @if($editingMedia === $detailsMedia->id && $editingField === 'description')
                                <div class="ums-edit-group">
                                    <textarea class="ums-textarea" wire:model="editingValue" rows="3"></textarea>
                                    <button class="ums-btn ums-btn-sm" wire:click="saveEditing">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="ums-btn ums-btn-sm" wire:click="cancelEditing">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @else
                                <div class="ums-editable" wire:click="startEditing({{ $detailsMedia->id }}, 'description')">
                                    {{ $detailsMedia->description ?: 'Cliquer pour ajouter une description' }}
                                    <i class="fas fa-edit ums-edit-icon"></i>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Metadata -->
                        <div class="ums-details-meta">
                            <h5>Informations techniques</h5>
                            <div class="ums-meta-grid">
                                <div class="ums-meta-item">
                                    <label>Nom de fichier</label>
                                    <span>{{ $detailsMedia->filename }}</span>
                                </div>
                                <div class="ums-meta-item">
                                    <label>Type</label>
                                    <span>{{ $detailsMedia->mime_type }}</span>
                                </div>
                                <div class="ums-meta-item">
                                    <label>Taille</label>
                                    <span>{{ $this->formatFileSize($detailsMedia->size) }}</span>
                                </div>
                                @if($detailsMedia->dimensions)
                                <div class="ums-meta-item">
                                    <label>Dimensions</label>
                                    <span>{{ $detailsMedia->dimensions['width'] }} × {{ $detailsMedia->dimensions['height'] }} pixels</span>
                                </div>
                                @endif
                                <div class="ums-meta-item">
                                    <label>Date d'ajout</label>
                                    <span>{{ $detailsMedia->created_at->format('d/m/Y à H:i') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="ums-details-actions">
                            <a href="{{ asset($detailsMedia->path) }}" target="_blank" class="ums-btn ums-btn-outline">
                                <i class="fas fa-external-link-alt"></i>
                                Voir le fichier
                            </a>
                            <button class="ums-btn ums-btn-danger" 
                                    wire:click="deleteMedia({{ $detailsMedia->id }})"
                                    onclick="return confirm('Supprimer définitivement ce média ?')">
                                <i class="fas fa-trash"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>

            <!-- Footer -->
            <div class="ums-footer">
                <div class="ums-footer-info">
                    @if($selectionCount > 0)
                    <span class="ums-selection-summary">
                        <strong>{{ $selectionCount }}</strong> élément(s) sélectionné(s)
                        @if($maxFiles > 1 && $selectionMode === 'multiple')
                        sur {{ $maxFiles }} maximum
                        @endif
                    </span>
                    @endif
                </div>

                <div class="ums-footer-actions">
                    <button class="ums-btn ums-btn-secondary" wire:click="closeModal">
                        Annuler
                    </button>
                    
                    @if($currentTab === 'library')
                    <button class="ums-btn ums-btn-primary" 
                            wire:click="confirmSelection"
                            {{ $selectionCount === 0 ? 'disabled' : '' }}>
                        @if($selectionMode === 'single')
                            Sélectionner
                        @else
                            Sélectionner ({{ $selectionCount }})
                        @endif
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    @if($successMessage)
    <div class="ums-message ums-success">
        <i class="fas fa-check-circle"></i>
        {{ $successMessage }}
    </div>
    @endif

    @if($errorMessage)
    <div class="ums-message ums-error">
        <i class="fas fa-exclamation-circle"></i>
        {{ $errorMessage }}
    </div>
    @endif

    @if($infoMessage)
    <div class="ums-message ums-info">
        <i class="fas fa-info-circle"></i>
        {{ $infoMessage }}
    </div>
    @endif
    @endif

    <!-- Loading Overlay -->
    <div wire:loading.flex class="ums-loading-overlay">
        <div class="ums-spinner">
            <i class="fas fa-spinner fa-spin"></i>
        </div>
    </div>
</div>

<!-- Styles CSS -->
@push('styles')
<link rel="stylesheet" href="{{ asset('css/universal-media-selector.css') }}">
@endpush

<!-- Scripts JavaScript -->
@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('universalMediaSelector', () => ({
        // État du drag and drop
        dragCounter: 0,
        
        // Gestion du drag and drop pour l'upload
        handleDragEnter(e) {
            e.preventDefault();
            this.dragCounter++;
            this.$wire.dragActive = true;
        },
        
        handleDragLeave(e) {
            e.preventDefault();
            this.dragCounter--;
            if (this.dragCounter === 0) {
                this.$wire.dragActive = false;
            }
        },
        
        handleDragOver(e) {
            e.preventDefault();
        },
        
        handleDrop(e) {
            e.preventDefault();
            this.dragCounter = 0;
            this.$wire.dragActive = false;
            
            const files = Array.from(e.dataTransfer.files);
            if (files.length > 0) {
                // Filtrer les fichiers selon les types autorisés
                const allowedTypes = @json($allowedTypes ?? []);
                const maxSize = @json($maxFileSize ?? 10240) * 1024; // Convert to bytes
                
                const validFiles = files.filter(file => {
                    // Vérifier le type
                    if (allowedTypes.length > 0 && !allowedTypes.includes('all')) {
                        const fileType = this.getFileType(file.type);
                        if (!allowedTypes.includes(fileType)) {
                            return false;
                        }
                    }
                    
                    // Vérifier la taille
                    if (file.size > maxSize) {
                        return false;
                    }
                    
                    return true;
                });
                
                if (validFiles.length !== files.length) {
                    this.showMessage('error', `${files.length - validFiles.length} fichier(s) rejeté(s) (type ou taille non autorisé)`);
                }
                
                if (validFiles.length > 0) {
                    this.$wire.call('handleFileUpload', validFiles);
                }
            }
        },
        
        // Déterminer le type de fichier
        getFileType(mimeType) {
            if (mimeType.startsWith('image/')) return 'image';
            if (mimeType.startsWith('video/')) return 'video';
            if (mimeType.startsWith('audio/')) return 'audio';
            return 'document';
        },
        
        // Sélection avec Shift
        handleMediaClick(mediaId, shiftPressed = false) {
            this.$wire.call('toggleMedia', mediaId, shiftPressed);
        },
        
        // Raccourcis clavier
        handleKeydown(e) {
            if (!@this.isOpen) return;
            
            switch (e.key) {
                case 'Escape':
                    e.preventDefault();
                    this.$wire.call('closeModal');
                    break;
                    
                case 'Enter':
                    if (@this.selectionCount > 0 && @this.currentTab === 'library') {
                        e.preventDefault();
                        this.$wire.call('confirmSelection');
                    }
                    break;
                    
                case 'a':
                case 'A':
                    if ((e.ctrlKey || e.metaKey) && @this.selectionMode === 'multiple') {
                        e.preventDefault();
                        this.$wire.call('selectAll');
                    }
                    break;
                    
                case 'Delete':
                case 'Backspace':
                    if (@this.selectionCount > 0 && e.target.tagName !== 'INPUT' && e.target.tagName !== 'TEXTAREA') {
                        e.preventDefault();
                        if (confirm(`Supprimer ${@this.selectionCount} média(s) sélectionné(s) ?`)) {
                            this.$wire.call('bulkDelete');
                        }
                    }
                    break;
            }
        },
        
        // Messages
        showMessage(type, text) {
            // Créer l'élément message
            const message = document.createElement('div');
            message.className = `ums-message ums-${type}`;
            message.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
                ${text}
            `;
            
            // Ajouter au DOM
            document.body.appendChild(message);
            
            // Supprimer après 5 secondes
            setTimeout(() => {
                if (message.parentNode) {
                    message.remove();
                }
            }, 5000);
        },
        
        // Copier l'URL d'un média
        copyUrl(url) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(url).then(() => {
                    this.showMessage('success', 'URL copiée dans le presse-papiers');
                });
            } else {
                // Fallback pour les navigateurs plus anciens
                const textarea = document.createElement('textarea');
                textarea.value = url;
                document.body.appendChild(textarea);
                textarea.select();
                document.execCommand('copy');
                document.body.removeChild(textarea);
                this.showMessage('success', 'URL copiée dans le presse-papiers');
            }
        },
        
        // Prévisualisation d'image en plein écran
        previewImage(imageUrl, title) {
            const overlay = document.createElement('div');
            overlay.className = 'ums-preview-overlay';
            overlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.9);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
            `;
            
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = title;
            img.style.cssText = `
                max-width: 90%;
                max-height: 90%;
                object-fit: contain;
                border-radius: 8px;
                box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
            `;
            
            overlay.appendChild(img);
            document.body.appendChild(overlay);
            
            // Fermer en cliquant
            overlay.addEventListener('click', () => {
                overlay.remove();
            });
            
            // Fermer avec Escape
            const handleEscape = (e) => {
                if (e.key === 'Escape') {
                    overlay.remove();
                    document.removeEventListener('keydown', handleEscape);
                }
            };
            document.addEventListener('keydown', handleEscape);
        },
        
        // Animation d'apparition des éléments
        animateIn(element) {
            element.style.opacity = '0';
            element.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
                element.style.transition = 'all 0.3s ease-out';
                element.style.opacity = '1';
                element.style.transform = 'translateY(0)';
            }, 100);
        },
        
        // Formatage des tailles de fichier
        formatFileSize(bytes) {
            const sizes = ['B', 'KB', 'MB', 'GB'];
            if (bytes === 0) return '0 B';
            const i = Math.floor(Math.log(bytes) / Math.log(1024));
            return Math.round(bytes / Math.pow(1024, i) * 100) / 100 + ' ' + sizes[i];
        },
        
        // Validation des URLs
        isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        },
        
        // Lazy loading des images
        setupLazyLoading() {
            const images = document.querySelectorAll('.ums-media-thumbnail img[loading="lazy"]');
            
            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries, observer) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src || img.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });
                
                images.forEach(img => imageObserver.observe(img));
            }
        },
        
        // Auto-scroll pour la sélection
        scrollToSelected() {
            const selected = document.querySelector('.ums-media-item.selected');
            if (selected) {
                selected.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest',
                    inline: 'nearest'
                });
            }
        }
    }));
});

// Event listeners globaux
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des raccourcis clavier globaux
    document.addEventListener('keydown', function(e) {
        // Déléguer à Alpine s'il y a une instance active
        const selector = document.querySelector('[x-data*="universalMediaSelector"]');
        if (selector && selector.__x) {
            selector.__x.$data.handleKeydown(e);
        }
    });
    
    // Gestion des messages Livewire
    window.addEventListener('show-message', function(e) {
        const { type, message } = e.detail;
        
        const messageEl = document.createElement('div');
        messageEl.className = `ums-message ums-${type}`;
        messageEl.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
            ${message}
        `;
        
        document.body.appendChild(messageEl);
        
        setTimeout(() => {
            messageEl.style.opacity = '0';
            messageEl.style.transform = 'translateX(100%)';
            setTimeout(() => messageEl.remove(), 300);
        }, 5000);
    });
    
    // Auto-hide des messages flash
    document.querySelectorAll('.ums-message').forEach(message => {
        setTimeout(() => {
            message.style.opacity = '0';
            message.style.transform = 'translateX(100%)';
            setTimeout(() => message.remove(), 300);
        }, 5000);
    });
});

// Livewire hooks
document.addEventListener('livewire:initialized', () => {
    // Setup lazy loading après chaque mise à jour
    Livewire.hook('morph.updated', ({ el, component }) => {
        if (el.querySelector('.ums-media-grid')) {
            const selector = el.querySelector('[x-data*="universalMediaSelector"]');
            if (selector && selector.__x) {
                selector.__x.$data.setupLazyLoading();
            }
        }
    });
});

// Gestion du focus trap dans le modal
function setupFocusTrap(modalElement) {
    const focusableElements = modalElement.querySelectorAll(
        'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );
    
    const firstElement = focusableElements[0];
    const lastElement = focusableElements[focusableElements.length - 1];
    
    modalElement.addEventListener('keydown', function(e) {
        if (e.key === 'Tab') {
            if (e.shiftKey) {
                if (document.activeElement === firstElement) {
                    lastElement.focus();
                    e.preventDefault();
                }
            } else {
                if (document.activeElement === lastElement) {
                    firstElement.focus();
                    e.preventDefault();
                }
            }
        }
    });
    
    // Focus sur le premier élément
    setTimeout(() => firstElement?.focus(), 100);
}

// Performance optimization
const throttle = (func, limit) => {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    }
};

const debounce = (func, wait, immediate) => {
    let timeout;
    return function() {
        const context = this, args = arguments;
        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };
        const callNow = immediate && !timeout;
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
        if (callNow) func.apply(context, args);
    };
};

// Utilités pour les développeurs
window.UMSUtils = {
    // Ouvrir le sélecteur avec une configuration personnalisée
    open: function(config = {}) {
        window.Livewire.dispatch('open-universal-media-selector', config);
    },
    
    // Écouter les sélections
    onSelection: function(callback) {
        window.addEventListener('media-selected', callback);
    },
    
    // Obtenir les médias sélectionnés
    getSelected: function() {
        const selector = document.querySelector('[x-data*="universalMediaSelector"]');
        return selector?.__x?.$wire?.selectedMedia || [];
    },
    
    // Réinitialiser la sélection
    clearSelection: function() {
        const selector = document.querySelector('[x-data*="universalMediaSelector"]');
        selector?.__x?.$wire?.deselectAll();
    }
};

console.log('🎯 Universal Media Selector loaded successfully!');
</script>
@endpush