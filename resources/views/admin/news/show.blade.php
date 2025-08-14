@extends('layouts.admin')

@section('title', $news->title)

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Actualités</a></li>
                    <li class="breadcrumb-item active">{{ $news->title }}</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0">{{ $news->title }}</h1>
            <div class="mt-2">
                <span class="badge bg-{{ $news->status === 'published' ? 'success' : ($news->status === 'draft' ? 'secondary' : 'warning') }}">
                    {{ ucfirst($news->status) }}
                </span>
                @if($news->is_featured)
                    <span class="badge bg-warning"><i class="fas fa-star"></i> À la une</span>
                @endif
            </div>
        </div>
        
        <div class="d-flex gap-2">
            <a href="{{ route('news.edit', $news) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>
                Modifier
            </a>
            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Contenu principal --}}
        <div class="col-lg-8">
            {{-- Image à la une --}}
            @if($news->featuredImage)
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <img src="{{ $news->featuredImage->url }}" 
                             alt="{{ $news->title }}"
                             class="img-fluid rounded shadow"
                             style="max-height: 400px;">
                    </div>
                </div>
            @endif

            {{-- Contenu --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Contenu de l'article</h5>
                </div>
                <div class="card-body">
                    @if($news->excerpt)
                        <div class="alert alert-light">
                            <strong>Extrait :</strong> {{ $news->excerpt }}
                        </div>
                    @endif
                    
                    <div class="content-body">
                        {!! $news->content_blocks !!}
                    </div>
                </div>
            </div>

            {{-- Galerie d'images --}}
            @if($news->media->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-images me-2"></i>Galerie ({{ $news->media->count() }} images)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach($news->media as $media)
                                <div class="col-md-4">
                                    <div class="position-relative">
                                        <img src="{{ $media->thumbnail_url }}" 
                                             alt="{{ $media->getTranslation('fr')->title ?? $media->original_name }}"
                                             class="img-fluid rounded shadow-sm"
                                             style="height: 200px; width: 100%; object-fit: cover;">
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-dark">{{ $loop->iteration }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Informations --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informations</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-12">
                            <strong>Statut :</strong>
                            <span class="badge bg-{{ $news->status === 'published' ? 'success' : ($news->status === 'draft' ? 'secondary' : 'warning') }}">
                                {{ ucfirst($news->status) }}
                            </span>
                        </div>
                        
                        @if($news->category)
                            <div class="col-12">
                                <strong>Catégorie :</strong>
                                <span class="badge bg-secondary">{{ $news->category->name }}</span>
                            </div>
                        @endif
                        
                        <div class="col-12">
                            <strong>Auteur :</strong>
                            {{ $news->creator->name }}
                        </div>
                        
                        <div class="col-12">
                            <strong>Créé le :</strong>
                            {{ $news->created_at->format('d/m/Y à H:i') }}
                        </div>
                        
                        @if($news->published_at)
                            <div class="col-12">
                                <strong>Publié le :</strong>
                                {{ $news->published_at->format('d/m/Y à H:i') }}
                            </div>
                        @endif
                        
                        @if($news->reading_time)
                            <div class="col-12">
                                <strong>Temps de lecture :</strong>
                                <i class="fas fa-clock me-1"></i>{{ $news->reading_time }} min
                            </div>
                        @endif
                        
                        <div class="col-12">
                            <strong>Vues :</strong>
                            <i class="fas fa-eye me-1"></i>{{ number_format($news->views_count) }}
                        </div>
                        
                        <div class="col-12">
                            <strong>Commentaires :</strong>
                            @if($news->allow_comments)
                                <span class="text-success">Autorisés</span>
                            @else
                                <span class="text-muted">Désactivés</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tags --}}
            @if($news->tags->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-tags me-2"></i>Étiquettes</h6>
                    </div>
                    <div class="card-body">
                        @foreach($news->tags as $tag)
                            <span class="badge bg-primary me-1 mb-1">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Actions</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('news.edit', $news) }}" class="btn btn-primary">
                            <i class="fas fa-edit me-2"></i>
                            Modifier
                        </a>
                        
                        @if($news->status === 'draft')
                            <a href="{{ route('news.edit', $news) }}" class="btn btn-success w-100">
                                <i class="fas fa-globe me-2"></i>
                                Publier (via édition)
                            </a>
                        @elseif($news->status === 'published')
                            <a href="{{ route('news.edit', $news) }}" class="btn btn-warning w-100">
                                <i class="fas fa-eye-slash me-2"></i>
                                Modifier le statut
                            </a>
                        @endif
                        
                        <hr>
                        
                        <button type="button" 
                                class="btn btn-outline-danger w-100"
                                onclick="deleteArticle({{ $news->id }}, '{{ addslashes($news->title) }}')">
                            <i class="fas fa-trash me-2"></i>
                            Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal de suppression --}}
@livewire('admin.news.delete-news-modal')

@push('scripts')
<script>
// Fonction de suppression d'article
function deleteArticle(articleId, articleTitle) {
    Livewire.dispatch('confirm-delete-news', articleId, articleTitle);
}

// Écouter l'événement de suppression pour rediriger
document.addEventListener('livewire:initialized', () => {
    Livewire.on('news-deleted', (event) => {
        // Rediriger vers la liste des articles
        window.location.href = '{{ route("news.index") }}';
    });
});
</script>
@endpush

@push('styles')
<style>
.content-body {
    line-height: 1.7;
}

.content-body h1, 
.content-body h2, 
.content-body h3, 
.content-body h4, 
.content-body h5, 
.content-body h6 {
    margin-top: 1.5rem;
    margin-bottom: 1rem;
    font-weight: 600;
}

.content-body p {
    margin-bottom: 1rem;
}

.content-body img {
    max-width: 100%;
    height: auto;
    border-radius: 0.375rem;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.content-body blockquote {
    border-left: 4px solid #007bff;
    margin: 1.5rem 0;
    padding: 1rem 1.5rem;
    background-color: #f8f9fa;
    border-radius: 0.375rem;
}

.content-body ul, 
.content-body ol {
    margin-bottom: 1rem;
    padding-left: 2rem;
}

.content-body table {
    width: 100%;
    margin-bottom: 1rem;
    border-collapse: collapse;
}

.content-body table th,
.content-body table td {
    padding: 0.5rem;
    border: 1px solid #dee2e6;
}

.content-body table th {
    background-color: #f8f9fa;
    font-weight: 600;
}
</style>
@endpush
@endsection