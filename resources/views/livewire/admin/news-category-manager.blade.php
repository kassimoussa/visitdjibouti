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
            <h4 class="mb-0">Catégories d'actualités</h4>
            <p class="text-muted mb-0">Gérer les catégories pour organiser vos actualités</p>
        </div>
        <button type="button" class="btn btn-primary" wire:click="openCreateModal">
            <i class="fas fa-plus me-2"></i>
            Nouvelle catégorie
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
                        <input type="text" class="form-control" placeholder="Rechercher une catégorie..."
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

    <!-- Liste des catégories -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-folder me-2"></i>
                Liste des catégories ({{ $categories->total() }})
            </h6>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Catégorie</th>
                        <th>Slug</th>
                        <th>Articles</th>
                        <th>Statut</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <i class="fas fa-folder fa-lg text-primary"></i>
                                    </div>
                                    <div>
                                        <h6 class="mb-1">{{ $category->name }}</h6>
                                        @if($category->description)
                                            <small class="text-muted">{{ Str::limit($category->description, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code class="small">{{ $category->slug }}</code>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $category->news_count ?? 0 }}</span>
                            </td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           {{ $category->is_active ? 'checked' : '' }}
                                           wire:click="toggleStatus({{ $category->id }})">
                                    <label class="form-check-label">
                                        <span class="badge bg-{{ $category->is_active ? 'success' : 'secondary' }}">
                                            {{ $category->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-primary"
                                            wire:click="openEditModal({{ $category->id }})"
                                            title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-{{ $category->is_active ? 'warning' : 'success' }}"
                                            wire:click="toggleStatus({{ $category->id }})"
                                            title="{{ $category->is_active ? 'Désactiver' : 'Activer' }}">
                                        <i class="fas fa-{{ $category->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-danger"
                                            wire:click="delete({{ $category->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible."
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
                                    <i class="fas fa-folder fa-3x mb-3 d-block"></i>
                                    @if($search)
                                        <h6>Aucune catégorie trouvée</h6>
                                        <p class="mb-0">Essayez de modifier votre recherche</p>
                                    @else
                                        <h6>Aucune catégorie</h6>
                                        <p class="mb-0">
                                            <button type="button" class="btn btn-primary btn-sm" 
                                                    wire:click="openCreateModal">
                                                Créer la première catégorie
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

        @if($categories->hasPages())
            <div class="card-footer">
                {{ $categories->links() }}
            </div>
        @endif
    </div>

    <!-- Modal pour créer/éditer une catégorie -->
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog {{ $modalSize }}" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $modalTitle }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    
                    <form wire:submit.prevent="save">
                        <div class="modal-body">
                            <!-- Onglets de langue -->
                            <ul class="nav nav-tabs mb-3" id="languageTabs">
                                @foreach($availableLocales as $locale)
                                    <li class="nav-item">
                                        <button class="nav-link @if($loop->first) active @endif" 
                                                type="button"
                                                data-bs-toggle="tab" 
                                                data-bs-target="#tab-{{ $locale }}">
                                            {{ strtoupper($locale) }}
                                            @if($locale === 'fr') <span class="text-danger">*</span> @endif
                                        </button>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Contenu des onglets -->
                            <div class="tab-content">
                                @foreach($availableLocales as $locale)
                                    <div class="tab-pane @if($loop->first) show active @endif" 
                                         id="tab-{{ $locale }}">
                                        
                                        <!-- Nom -->
                                        <div class="mb-3">
                                            <label class="form-label">
                                                Nom de la catégorie
                                                @if($locale === 'fr') <span class="text-danger">*</span> @endif
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('translations.'.$locale.'.name') is-invalid @enderror"
                                                   wire:model="translations.{{ $locale }}.name"
                                                   placeholder="Ex: Tourisme, Culture, Économie..."
                                                   @if($locale === 'fr') required @endif>
                                            @error('translations.'.$locale.'.name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <!-- Description -->
                                        <div class="mb-3">
                                            <label class="form-label">Description</label>
                                            <textarea class="form-control @error('translations.'.$locale.'.description') is-invalid @enderror"
                                                      wire:model="translations.{{ $locale }}.description"
                                                      rows="3"
                                                      placeholder="Description de la catégorie..."></textarea>
                                            @error('translations.'.$locale.'.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Paramètres généraux -->
                            <div class="border-top pt-3 mt-3">
                                <h6>Paramètres généraux</h6>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label class="form-label">Slug (URL)</label>
                                            <input type="text" 
                                                   class="form-control @error('slug') is-invalid @enderror"
                                                   wire:model="slug"
                                                   placeholder="Ex: tourisme, culture...">
                                            <small class="form-text text-muted">
                                                Laissez vide pour générer automatiquement
                                            </small>
                                            @error('slug')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <div class="form-check mt-4">
                                                <input class="form-check-input" 
                                                       type="checkbox" 
                                                       wire:model="is_active"
                                                       id="is_active">
                                                <label class="form-check-label" for="is_active">
                                                    Catégorie active
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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