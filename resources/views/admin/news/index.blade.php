@extends('layouts.admin')

@section('title', 'Actualités')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Actualités</h1>
            <p class="text-muted">Gérer les articles d'actualité</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('news-categories.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-folder me-2"></i>
                Catégories
            </a>
            <a href="{{ route('news.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nouvel article
            </a>
        </div>
    </div>

    {{-- Liste des articles --}}
    @livewire('admin.news.news-list')
</div>
@endsection