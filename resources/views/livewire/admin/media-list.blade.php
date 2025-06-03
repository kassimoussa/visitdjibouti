<div>
    <div class="container-fluid">
        <!-- Titre et bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Gestion des médias</h1>
            <div class="d-flex">
                <div class="input-group me-2">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control" placeholder="Rechercher...">
                </div>
                <a href="{{ route('media.create') }}" class="btn btn-primary">
                    <i class="fas fa-upload me-1"></i> Téléverser des médias
                </a>
            </div>
        </div>

        <!-- Filtres et contrôles de vue -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div class="d-flex">
                        <select wire:model.live="typeFilter" class="form-select me-2">
                            <option value="">Tous les types</option>
                            <option value="images">Images</option>
                            <option value="documents">Documents</option>
                            <option value="videos">Vidéos</option>
                            <option value="others">Autres</option>
                        </select>
                        
                        <select wire:model.live="dateFilter" class="form-select me-2">
                            <option value="">Toutes les dates</option>
                            <option value="today">Aujourd'hui</option>
                            <option value="week">Cette semaine</option>
                            <option value="month">Ce mois</option>
                            <option value="year">Cette année</option>
                        </select>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <!-- Boutons pour changer le mode d'affichage -->
                        <div class="btn-group">
                            <button wire:click="changeViewMode('grid')" type="button" class="btn btn-sm {{ $viewMode == 'grid' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-th"></i>
                            </button>
                            <button wire:click="changeViewMode('list')" type="button" class="btn btn-sm {{ $viewMode == 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions en masse (visible quand des éléments sont sélectionnés) -->
        <div class="card shadow-sm mb-4 {{ count($selectedItems) > 0 ? '' : 'd-none' }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold">{{ count($selectedItems) }} élément(s) sélectionné(s)</span>
                    </div>
                    <div>
                        <button wire:click="openDeleteSelectedModal" class="btn btn-danger btn-sm">
                            <i class="fas fa-trash me-1"></i> Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vue en grille -->
        @if($viewMode == 'grid')
            <div class="row g-3">
                @forelse($media as $item)
                    <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                        <div class="card h-100 {{ in_array($item->id, $selectedItems) ? 'border-primary' : '' }}">
                            <div class="position-relative">
                                <!-- Checkbox de sélection -->
                                <div class="position-absolute top-0 start-0 m-2">
                                    <div class="form-check">
                                        <input wire:model.live="selectedItems" class="form-check-input" type="checkbox" value="{{ $item->id }}" id="check-{{ $item->id }}">
                                    </div>
                                </div>
                                
                                <!-- Prévisualisation -->
                                <div class="media-thumbnail" style="height: 150px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                    @if($item->type == 'images')
                                        <img src="{{ asset($item->thumbnail_path ?? $item->path) }}" class="card-img-top img-fluid" alt="{{ $item->alt_text }}" style="max-height: 150px; width: auto;">
                                    @elseif($item->type == 'documents')
                                        <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                    @elseif($item->type == 'videos')
                                        <i class="fas fa-file-video fa-3x text-primary"></i>
                                    @else
                                        <i class="fas fa-file fa-3x text-secondary"></i>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="card-body p-2">
                                <h6 class="card-title mb-0 text-truncate" title="{{ $item->title }}">{{ $item->title }}</h6>
                                <p class="card-text text-muted small mb-0">{{ humanFileSize($item->size) }}</p>
                                <p class="card-text text-muted small">{{ $item->created_at->format('d/m/Y') }}</p>
                            </div>
                            
                            <div class="card-footer p-2 d-flex justify-content-around">
                                <a href="{{ route('media.edit', $item->id) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="{{ asset($item->path) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button wire:click="openDeleteModal({{ $item->id }})" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info text-center">
                            Aucun média disponible. Commencez par téléverser des fichiers.
                        </div>
                    </div>
                @endforelse
            </div>
        @else
            <!-- Vue en liste -->
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 40px;">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="selectAll"
                                            onchange="toggleAllCheckboxes(this)">
                                    </div>
                                </th>
                                <th>Fichier</th>
                                <th>Type</th>
                                <th>Taille</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($media as $item)
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input wire:model.live="selectedItems" class="form-check-input checkboxItem" type="checkbox" value="{{ $item->id }}" id="list-check-{{ $item->id }}">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                @if($item->type == 'images')
                                                    <img src="{{ asset($item->thumbnail_path ?? $item->path) }}" class="img-fluid" style="max-width: 40px; max-height: 40px;">
                                                @elseif($item->type == 'documents')
                                                    <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                @elseif($item->type == 'videos')
                                                    <i class="fas fa-file-video fa-2x text-primary"></i>
                                                @else
                                                    <i class="fas fa-file fa-2x text-secondary"></i>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-semibold">{{ $item->title }}</div>
                                                <div class="text-muted small">{{ $item->filename }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($item->type == 'images')
                                            <span class="badge bg-info">Image</span>
                                        @elseif($item->type == 'documents')
                                            <span class="badge bg-danger">Document</span>
                                        @elseif($item->type == 'videos')
                                            <span class="badge bg-primary">Vidéo</span>
                                        @else
                                            <span class="badge bg-secondary">Autre</span>
                                        @endif
                                    </td>
                                    <td>{{ humanFileSize($item->size) }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('media.edit', $item->id) }}" class="btn btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ asset($item->path) }}" target="_blank" class="btn btn-outline-secondary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <button wire:click="openDeleteModal({{ $item->id }})" class="btn btn-outline-danger">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">Aucun média disponible</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                @if($media->isEmpty())
                    <div class="text-center p-4">
                        <div class="text-muted">Aucun média disponible. Commencez par téléverser des fichiers.</div>
                    </div>
                @endif
            </div>
        @endif

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $media->links() }}
        </div>
    </div>
    
    <!-- Utilisation du composant delete-modal pour la suppression d'un seul média -->
    <x-delete-modal delmodal="deleteModal" message="Êtes-vous sûr de vouloir supprimer ce média" delf="deleteMedia({{ $mediaToDelete }})" />
    
    <!-- Utilisation du composant delete-modal pour la suppression en masse -->
    <x-delete-modal delmodal="deleteSelectedModal" message="Êtes-vous sûr de vouloir supprimer les médias sélectionnés" delf="deleteSelected" />
    
    @script
    <script>
        // Fonction pour formater les tailles de fichiers
        function humanFileSize(size) {
            var i = size == 0 ? 0 : Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'KB', 'MB', 'GB', 'TB'][i];
        }
        
        // Fonction pour sélectionner/désélectionner tous les éléments
        function toggleAllCheckboxes(checkbox) {
            const checkboxes = document.getElementsByClassName('checkboxItem');
            
            if (checkbox.checked) {
                @this.selectedItems = Array.from(checkboxes).map(item => parseInt(item.value));
            } else {
                @this.selectedItems = [];
            }
        }
        
        // Écouter les événements de suppression
        document.addEventListener('DOMContentLoaded', () => {
            window.addEventListener('openDeleteModal', () => {
                let deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
            
            window.addEventListener('openDeleteSelectedModal', () => {
                let deleteSelectedModal = new bootstrap.Modal(document.getElementById('deleteSelectedModal'));
                deleteSelectedModal.show();
            });
        });
    </script>
    @endscript
</div>