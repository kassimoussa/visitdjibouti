@extends('operator.layouts.app')

@section('title', 'Créer un Tour')
@section('page-title', 'Nouveau Tour')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('operator.tours.index') }}">
                        <i class="fas fa-route me-1"></i>
                        Tours
                    </a>
                </li>
                <li class="breadcrumb-item active">Nouveau</li>
            </ol>
        </nav>
        <h2 class="mb-1">Créer un Nouveau Tour</h2>
        <p class="text-muted">Créez un circuit touristique pour vos clients.</p>
    </div>

    @livewire('operator.tour.tour-form')
</div>
@endsection
