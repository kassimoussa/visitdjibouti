@extends('layouts.admin')

@section('title', 'Modifier le Tour')
@section('page-title', 'Modifier le Tour')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Modifier le tour: {{ $tour->title }}</h1>
            <div class="btn-group">
                <a href="{{ route('tours.show', $tour) }}" class="btn btn-outline-info">
                    <i class="fas fa-eye me-1"></i> Voir
                </a>
                <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
        </div>

        <livewire:admin.tour.tour-form :tour="$tour" />
    </div>
@endsection