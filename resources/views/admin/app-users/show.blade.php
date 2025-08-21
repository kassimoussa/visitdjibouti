@extends('layouts.admin')

@section('title', 'Détails Utilisateur - ' . $appUser->name)
@section('page-title', 'Détails Utilisateur')

@section('content')
<div class="container-fluid">
    <!-- Navigation breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('app-users.index') }}">Utilisateurs Mobiles</a></li>
                    <li class="breadcrumb-item active">{{ $appUser->name }}</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    @if($appUser->avatar_url)
                        <img src="{{ $appUser->avatar_url }}" alt="{{ $appUser->name }}" 
                             class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    @endif
                    <div>
                        <h1 class="h3 mb-0">{{ $appUser->name }}</h1>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge {{ $appUser->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $appUser->is_active ? 'Actif' : 'Inactif' }}
                            </span>
                            @php
                                $providerClass = match($appUser->provider) {
                                    'google' => 'bg-danger',
                                    'facebook' => 'bg-primary',
                                    default => 'bg-secondary'
                                };
                                $providerLabel = match($appUser->provider) {
                                    'google' => 'Google',
                                    'facebook' => 'Facebook',
                                    default => 'Email'
                                };
                            @endphp
                            <span class="badge {{ $providerClass }}">{{ $providerLabel }}</span>
                            @if($appUser->preferred_language)
                                <span class="badge bg-info">{{ strtoupper($appUser->preferred_language) }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('app-users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <a href="{{ route('app-users.edit', $appUser->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-danger dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i> Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <form method="POST" action="{{ route('app-users.toggle-status', $appUser->id) }}" style="display: inline;">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas {{ $appUser->is_active ? 'fa-ban' : 'fa-check' }} me-2"></i>
                                        {{ $appUser->is_active ? 'Désactiver' : 'Activer' }}
                                    </button>
                                </form>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('app-users.reset-password', $appUser->id) }}" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="dropdown-item">
                                        <i class="fas fa-key me-2"></i> Réinitialiser mot de passe
                                    </button>
                                </form>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('app-users.destroy', $appUser->id) }}" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="fas fa-trash me-2"></i> Supprimer
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Composant Livewire pour les détails -->
    @livewire('admin.app-user-details', ['userId' => $appUser->id])
</div>
@endsection