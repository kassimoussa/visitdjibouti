@extends('operator.layouts.app')

@section('title', 'Modifier le Tour')
@section('page-title', 'Modifier le Tour')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('operator.tours.index') }}">
                        <i class="fas fa-route me-1"></i>
                        Tours
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('operator.tours.show', $tour) }}">
                        {{ Str::limit($tour->translation('fr')->title ?? 'N/A', 30) }}
                    </a>
                </li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </nav>
        <h2 class="mb-1">Modifier le Tour</h2>
        <p class="text-muted">{{ $tour->translation('fr')->title ?? 'N/A' }}</p>
    </div>

    @livewire('operator.tour.tour-form', ['tour' => $tour])
</div>
@endsection
