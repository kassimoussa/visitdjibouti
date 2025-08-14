<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Catégories d'actualités</h1>
            <p class="text-muted">Gérer les catégories pour organiser vos actualités</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour aux actualités
            </a>
            <a href="{{ route('news.create') }}" class="btn btn-success">
                <i class="fas fa-plus me-2"></i>
                Créer un article
            </a>
            <button wire:click="openCreateModal" class="btn btn-primary">
                <i class="fas fa-folder-plus me-2"></i>
                Nouvelle catégorie
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">
                        <i class="fas fa-folder me-2"></i>
                        Liste des catégories
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="d-flex gap-2 justify-content-end">
                        <div class="input-group" style="max-width: 300px;">
                            <input type="text" class="form-control" wire:model.live.debounce.300ms="search" 
                                   placeholder="Rechercher une catégorie...">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                        </div>
                        <select wire:model.live="perPage" class="form-select" style="max-width: 100px;">
                            <option value="5">5</option>
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <button wire:click="sortBy('id')" class="btn btn-sm btn-link text-decoration-none p-0">
                                        ID 
                                        @if($sortField === 'id')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>
                                    <button wire:click="sortBy('name')" class="btn btn-sm btn-link text-decoration-none p-0">
                                        Nom
                                        @if($sortField === 'name')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>Slug</th>
                                <th>Couleur</th>
                                <th>Articles</th>
                                <th>Statut</th>
                                <th>
                                    <button wire:click="sortBy('sort_order')" class="btn btn-sm btn-link text-decoration-none p-0">
                                        Ordre
                                        @if($sortField === 'sort_order')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th>
                                    <button wire:click="sortBy('created_at')" class="btn btn-sm btn-link text-decoration-none p-0">
                                        Créé le
                                        @if($sortField === 'created_at')
                                            <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                        @endif
                                    </button>
                                </th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                            <tr>
                                <td><span class="badge bg-light text-dark">#{{ $category->id }}</span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($category->color)
                                            <div class="me-2" style="width: 20px; height: 20px; background: {{ $category->color }}; border-radius: 50%;"></div>
                                        @endif
                                        <div>
                                            <strong>{{ $category->name }}</strong>
                                            @if($category->description)
                                                <br><small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $category->slug }}</code>
                                </td>
                                <td>
                                    @if($category->color)
                                        <div class="d-flex align-items-center">
                                            <div style="width: 30px; height: 20px; background: {{ $category->color }}; border-radius: 4px; border: 1px solid #ddd;" class="me-2"></div>
                                            <small class="text-muted">{{ $category->color }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $category->news_count ?? '—' }}</span>
                                </td>
                                <td>
                                    <button wire:click="toggleStatus({{ $category->id }})" 
                                            class="btn btn-sm badge {{ $category->is_active ? 'bg-success' : 'bg-secondary' }}"
                                            wire:loading.attr="disabled" 
                                            wire:target="toggleStatus({{ $category->id }})">
                                        <span wire:loading.remove wire:target="toggleStatus({{ $category->id }})">
                                            {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                        <span wire:loading wire:target="toggleStatus({{ $category->id }})">
                                            <i class="fas fa-spinner fa-spin"></i>
                                        </span>
                                    </button>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ $category->sort_order ?? 0 }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $category->created_at->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button wire:click="openEditModal({{ $category->id }})" 
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="tooltip" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button wire:click="openDeleteModal({{ $category->id }})" 
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="tooltip" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                @if($categories->hasPages())
                    <div class="card-footer">
                        {{ $categories->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-5">
                    <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                    <h5>Aucune catégorie trouvée</h5>
                    <p class="text-muted">
                        @if($search)
                            Aucun résultat pour "{{ $search }}"
                        @else
                            Commencez par créer votre première catégorie d'actualités pour mieux organiser vos articles
                        @endif
                    </p>
                    @if(!$search)
                        <div class="d-flex gap-2 justify-content-center">
                            <button wire:click="openCreateModal" class="btn btn-primary">
                                <i class="fas fa-folder-plus me-2"></i>
                                Créer une catégorie
                            </button>
                            <a href="{{ route('news.create') }}" class="btn btn-success">
                                <i class="fas fa-plus me-2"></i>
                                Créer un article
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Create/Edit --}}
    @if($showCreateModal || $showEditModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-{{ $showCreateModal ? 'plus' : 'edit' }} me-2"></i>
                        {{ $showCreateModal ? 'Nouvelle catégorie' : 'Modifier la catégorie' }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="{{ $showCreateModal ? 'store' : 'update' }}">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Nom de la catégorie *</label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           wire:model.live.debounce.500ms="name" placeholder="Ex: Tourisme, Culture, Économie...">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Slug (URL)</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           wire:model="slug" placeholder="Ex: tourisme, culture, economie...">
                                    <div class="form-text">Laissez vide pour générer automatiquement depuis le nom</div>
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              wire:model="description" rows="3" 
                                              placeholder="Description courte de cette catégorie..."></textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Couleur</label>
                                    <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           wire:model="color" title="Choisir une couleur">
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Ordre d'affichage</label>
                                    <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                           wire:model="sort_order" min="0">
                                    <div class="form-text">Plus petit = plus haut</div>
                                    @error('sort_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" wire:model="is_active" id="isActive">
                                    <label class="form-check-label fw-semibold" for="isActive">
                                        Catégorie active
                                    </label>
                                    <div class="form-text">Les catégories inactives ne sont pas visibles</div>
                                </div>

                                <hr class="my-3">

                                <div class="d-grid gap-2">
                                    <small class="text-muted mb-2">
                                        <i class="fas fa-lightbulb me-1"></i>
                                        Actions rapides
                                    </small>
                                    <a href="{{ route('news.create') }}" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-plus me-2"></i>
                                        Créer un article
                                    </a>
                                    <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-list me-2"></i>
                                        Voir les articles
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </button>
                    <button type="button" wire:click="{{ $showCreateModal ? 'store' : 'update' }}" 
                            class="btn btn-primary" wire:loading.attr="disabled">
                        <i class="fas fa-{{ $showCreateModal ? 'save' : 'check' }} me-2"></i>
                        <span wire:loading.remove>{{ $showCreateModal ? 'Créer' : 'Modifier' }}</span>
                        <span wire:loading>Traitement...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Delete --}}
    @if($showDeleteModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                        Confirmer la suppression
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir supprimer la catégorie <strong>{{ $deletingCategoryName }}</strong> ?</p>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Cette action est irréversible. Si des articles sont associés à cette catégorie, la suppression sera refusée.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        <i class="fas fa-times me-2"></i>
                        Annuler
                    </button>
                    <button type="button" wire:click="delete" class="btn btn-danger" wire:loading.attr="disabled">
                        <i class="fas fa-trash me-2"></i>
                        <span wire:loading.remove>Supprimer</span>
                        <span wire:loading>Suppression...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

</div>