@extends('layouts.admin')

@section('title', 'Nouveau Tour')
@section('page-title', 'Nouveau Tour')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Créer un nouveau tour</h1>
            <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour à la liste
            </a>
        </div>

        <livewire:admin.tour.tour-form />
    </div>
@endsection