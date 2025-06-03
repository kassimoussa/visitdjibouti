<div class="category-tree-item" data-id="{{ $category->id }}">
    <div class="category-tree-item-header d-flex align-items-center p-2 mb-2 bg-light rounded">
        <div class="drag-handle me-2 text-muted cursor-grab">
            <i class="fas fa-grip-vertical"></i>
        </div>
        
        <div class="category-icon me-2" style="background-color: {{ $category->color ?? '#f8f9fa' }}; color: {{ $category->color ? '#fff' : '#6c757d' }}; width: 30px; height: 30px; border-radius: 4px; display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
            {!! $category->icon ?? '<i class="fas fa-folder"></i>' !!}
        </div>
        
        <div class="category-name flex-grow-1 fw-medium">
            {{ $category->name }}
        </div>
        
        <div class="category-actions">
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary me-1">
                <i class="fas fa-edit"></i>
            </a>
            
            @if($category->pois_count == 0 && $category->children_count == 0)
            <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                </button>
            </form>
            @endif
        </div>
    </div>
    
    <!-- Sous-catégories -->
    @if($category->children->count() > 0)
    <div class="category-tree-children ms-4">
        @foreach($category->children->sortBy('order') as $childCategory)
            @include('admin.categories.partials.category-tree-item', ['category' => $childCategory])
        @endforeach
    </div>
    @endif
</div>