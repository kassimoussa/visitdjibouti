<!-- resources/views/admin/media/simple-upload.blade.php -->
@extends('layouts.admin')

@section('title', 'Test d\'upload')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">Test d'upload</h1>
            <a href="{{ route('media.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
        
        <livewire:admin.simple-media-upload />
    </div>
@endsection