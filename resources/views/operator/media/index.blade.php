@extends('operator.layouts.app')

@section('title', 'Gestion des Médias')
@section('page-title', 'Médias')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="mb-1">Bibliothèque de Médias</h2>
        <p class="text-muted">Gérez vos images, vidéos et documents. Note: Vous ne pouvez pas supprimer de médias.</p>
    </div>

    <!-- Utiliser le composant Livewire MediaManager spécifique à l'opérateur -->
    @livewire('operator.media-manager')
</div>
@endsection
