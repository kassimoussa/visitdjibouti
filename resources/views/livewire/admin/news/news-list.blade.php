<div>
    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" class="form-control" 
                           wire:model.live.debounce.300ms="search"
                           placeholder="Titre, contenu...">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select class="form-select" wire:model.live="status">
                        <option value="">Tous les statuts</option>
                        <option value="draft">Brouillon</option>
                        <option value="published">Publié</option>
                        <option value="archived">Archivé</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Catégorie</label>
                    <select class="form-select" wire:model.live="category">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Auteur</label>
                    <select class="form-select" wire:model.live="author">
                        <option value="">Tous les auteurs</option>
                        @foreach($authors as $auth)
                            <option value="{{ $auth->id }}">{{ $auth->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    @if($search || $status || $category || $author)
                        <button type="button" class="btn btn-outline-secondary w-100" wire:click="resetFilters">
                            <i class="fas fa-times me-2"></i>
                            Reset
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div class="text-muted">
                {{ $news->total() }} article(s)
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Titre</th>
                        <th>Catégorie</th>
                        <th>Statut</th>
                        <th>Auteur</th>
                        <th>Date</th>
                        <th>Vues</th>
                        <th width="120">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($news as $article)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($article->featuredImage)
                                        <img src="{{ $article->featuredImage->thumbnail_url }}" 
                                             class="rounded me-3" 
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                             style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="{{ route('news.show', $article) }}" 
                                               class="text-decoration-none">
                                                {{ $article->title }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            @if($article->is_featured)
                                                <i class="fas fa-star text-warning me-1"></i>
                                            @endif
                                            @if($article->reading_time)
                                                <i class="fas fa-clock me-1"></i>{{ $article->reading_time }} min
                                            @endif
                                        </small>
                                    </div>
                                </div>
                            </td>
                            
                            <td>
                                @if($article->category)
                                    <span class="badge bg-secondary">{{ $article->category->name }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            
                            <td>
                                <span class="badge bg-{{ $article->status === 'published' ? 'success' : ($article->status === 'draft' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($article->status) }}
                                </span>
                            </td>
                            
                            <td>
                                <small>{{ $article->creator->name }}</small>
                            </td>
                            
                            <td>
                                <small>
                                    @if($article->published_at)
                                        {{ $article->published_at->format('d/m/Y H:i') }}
                                    @else
                                        {{ $article->created_at->format('d/m/Y H:i') }}
                                    @endif
                                </small>
                            </td>
                            
                            <td>
                                <small>{{ number_format($article->views_count) }}</small>
                            </td>
                            
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('news.show', $article) }}"
                                        class="btn btn-outline-secondary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('news.edit', $article) }}"
                                        class="btn btn-outline-primary" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Supprimer"
                                            wire:click="confirmDelete({{ $article->id }}, '{{ addslashes($article->title) }}')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-newspaper text-muted" style="font-size: 3rem;"></i>
                                <h6 class="mt-3 text-muted">Aucun article trouvé</h6>
                                <p class="text-muted">
                                    @if($search || $status || $category || $author)
                                        Essayez de modifier vos filtres ou 
                                        <button type="button" class="btn btn-link p-0" wire:click="resetFilters">voir tous les articles</button>
                                    @else
                                        <a href="{{ route('news.create') }}">Créez votre premier article</a>
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($news->hasPages())
            <div class="card-footer">
                {{ $news->links() }}
            </div>
        @endif
    </div>

    {{-- Modal de suppression --}}
    @livewire('admin.news.delete-news-modal')
</div>