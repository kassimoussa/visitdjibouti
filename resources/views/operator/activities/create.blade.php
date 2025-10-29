@extends('operator.layouts.app')

@section('title', 'Créer une Activité')

@section('page-title', 'Créer une Activité')

@section('content')
    <div class="fade-in">
        <div class="mb-4">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.activities.index') }}">
                            <i class="fas fa-running me-1"></i>
                            Activités
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Créer</li>
                </ol>
            </nav>
        </div>

        <livewire:operator.activity.activity-form />
    </div>
@endsection
