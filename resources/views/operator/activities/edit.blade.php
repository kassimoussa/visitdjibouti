@extends('operator.layouts.app')

@section('title', 'Modifier l\'Activité')

@section('page-title', 'Modifier l\'Activité')

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
                    <li class="breadcrumb-item">
                        <a href="{{ route('operator.activities.show', $activity) }}">
                            {{ Str::limit($activity->title, 30) }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active">Modifier</li>
                </ol>
            </nav>
        </div>

        <livewire:operator.activity.activity-form :activity="$activity" />
    </div>
@endsection
