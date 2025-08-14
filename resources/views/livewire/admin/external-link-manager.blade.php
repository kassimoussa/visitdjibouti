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

    <!-- Header avec bouton d'ajout -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Liens externes</h4>
            <p class="text-muted mb-0">Gérer les liens externes pour l'application mobile</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="fas fa-plus me-2"></i>
            Nouveau lien
        </button>
    </div>

    <!-- Barre de recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Rechercher un lien..."
                               wire:model.live="search">
                        @if($search)
                            <button class="btn btn-outline-secondary" type="button" wire:click="$set('search', '')">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des liens -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-link me-2"></i>
                Liste des liens ({{ $links->total() }})
            </h6>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>URL</th>
                        <th>Statut</th>
                        <th>Date de création</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($links as $link)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-external-link-alt fa-lg text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $link->name }}</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ $link->url }}" target="_blank" class="text-decoration-none small">
                                    {{ Str::limit($link->url, 50) }}
                                    <i class="fas fa-external-link-alt ms-1"></i>
                                </a>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           {{ $link->status ? 'checked' : '' }}
                                           wire:click="toggleStatus({{ $link->id }})">
                                    <label class="form-check-label">
                                        <span class="badge bg-{{ $link->status ? 'success' : 'secondary' }}">
                                            {{ $link->status ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $link->created_at->format('d/m/Y H:i') }}
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary"
                                            wire:click="openEditModal({{ $link->id }})"
                                            title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $link->status ? 'warning' : 'success' }}"
                                            wire:click="toggleStatus({{ $link->id }})"
                                            title="{{ $link->status ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $link->status ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                            wire:click="delete({{ $link->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer ce lien ?"
                                            title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="fas fa-link fa-3x mb-3 d-block"></i>
                                    @if($search)
                                        <h6>Aucun lien trouvé</h6>
                                        <p class="mb-0">Essayez de modifier votre recherche</p>
                                    @else
                                        <h6>Aucun lien externe</h6>
                                        <p class="mb-0">
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    wire:click="openCreateModal">
                                                Créer le premier lien
                                            </button>
                                        </p>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($links->hasPages())
            <div class="card-footer">
                {{ $links->links() }}
            </div>
        @endif
    </div>

    <!-- Modal pour créer/éditer un lien -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $modalTitle }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <!-- Nom -->
                            <div class="mb-3">
                                <label class="form-label">
                                    Nom du lien <span class="text-danger">*</span>
                                </label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror"
                                       wire:model="name"
                                       placeholder="Ex: Site officiel du tourisme">
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- URL -->
                            <div class="mb-3">
                                <label class="form-label">
                                    URL <span class="text-danger">*</span>
                                </label>
                                <input type="url" 
                                       class="form-control @error('url') is-invalid @enderror"
                                       wire:model="url"
                                       placeholder="https://example.com">
                                @error('url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Statut -->
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           wire:model="status"
                                           id="status">
                                    <label class="form-check-label" for="status">
                                        Lien actif
                                    </label>
                                </div>
                                <small class="text-muted">
                                    Seuls les liens actifs seront visibles dans l'application mobile
                                </small>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">
                                Annuler
                            </button>
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                                <span wire:loading.remove>
                                    <i class="fas fa-save me-2"></i>
                                    {{ $modalMode === 'create' ? 'Créer' : 'Modifier' }}
                                </span>
                                <span wire:loading>
                                    <i class="fas fa-spinner fa-spin me-2"></i>
                                    Enregistrement...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>