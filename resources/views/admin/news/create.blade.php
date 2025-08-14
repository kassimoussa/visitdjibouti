@extends('layouts.admin')

@section('title', 'Nouvelle actualité')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0">Nouvelle actualité</h1>
            <p class="text-muted">Créer un nouvel article d'actualité</p>
        </div>
        <div>
            <a href="{{ route('news.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    <livewire:admin.news.news-editor />
</div>

@endsection