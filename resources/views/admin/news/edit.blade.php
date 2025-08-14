@extends('layouts.admin')

@section('title', 'Modifier : ' . $news->title)

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Modifier l'actualité</h1>
            <p class="text-muted">{{ $news->title }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('news.show', $news) }}" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>
                Aperçu
            </a>
            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <livewire:admin.news.news-editor :news-id="$news->id" />
</div>

@endsection