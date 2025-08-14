@extends('layouts.admin')

@section('title', 'Catégories d\'actualités')
@section('page-title', 'Catégories d\'actualités')

@section('content')
<div class="container-fluid">
    <div class="alert alert-info">
        <i class="fas fa-info-circle me-2"></i>
        La création de catégorie se fait maintenant directement depuis la <a href="{{ route('news-categories.index') }}">liste des catégories</a> via le bouton "Nouvelle catégorie".
    </div>
    
    <div class="text-center">
        <a href="{{ route('news-categories.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left me-2"></i>
            Retour aux catégories
        </a>
    </div>
</div>
@endsection