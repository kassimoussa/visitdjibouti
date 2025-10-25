@extends('operator.layouts.app')

@section('title', 'Modifier Mon Entreprise')
@section('page-title', 'Modifier Mon Entreprise')

@section('content')
<div class="fade-in">
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('operator.tour-operator.show') }}">
                        <i class="fas fa-building me-1"></i>
                        Mon Entreprise
                    </a>
                </li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </nav>
        <h2 class="mb-1">Modifier les Informations</h2>
        <p class="text-muted">Mettez à jour les informations de votre entreprise.</p>
    </div>

    @livewire('operator.tour-operator-form')
</div>
@endsection
