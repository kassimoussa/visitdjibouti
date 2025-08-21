@extends('layouts.admin')

@section('title', 'Tableau de Bord des Utilisateurs Mobiles')
@section('page-title', 'Tableau de Bord des Utilisateurs Mobiles')

@section('content')
<div class="container-fluid">
    <!-- Navigation breadcrumb -->
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('app-users.index') }}">Utilisateurs Mobiles</a></li>
                    <li class="breadcrumb-item active">Tableau de bord</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Tableau de Bord des Utilisateurs Mobiles</h1>
                    <p class="text-muted mb-0">Analyses et statistiques détaillées des utilisateurs</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('app-users.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                    </a>
                    <a href="{{ route('app-users.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i> Exporter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-gradient rounded-circle p-3">
                                <i class="fas fa-users text-white"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $totalUsers }}</h4>
                            <p class="text-muted mb-0 small">Total utilisateurs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-gradient rounded-circle p-3">
                                <i class="fas fa-user-check text-white"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $activeUsers }}</h4>
                            <p class="text-muted mb-0 small">Utilisateurs actifs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-gradient rounded-circle p-3">
                                <i class="fas fa-user-times text-white"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $inactiveUsers }}</h4>
                            <p class="text-muted mb-0 small">Utilisateurs inactifs</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-gradient rounded-circle p-3">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                        </div>
                        <div class="ms-3">
                            <h4 class="mb-0">{{ $recentSignups }}</h4>
                            <p class="text-muted mb-0 small">Inscriptions (30j)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top utilisateurs et activités récentes -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Top utilisateurs par réservations</h5>
                </div>
                <div class="card-body">
                    @if($topUsersByReservations->count() > 0)
                        @foreach($topUsersByReservations as $user)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                         class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-user text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->reservations_count }} réservations</small>
                                </div>
                            </div>
                            <a href="{{ route('app-users.show', $user->id) }}" class="btn btn-sm btn-outline-primary">
                                Voir
                            </a>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Aucune donnée disponible</p>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Top utilisateurs par favoris</h5>
                </div>
                <div class="card-body">
                    @if($topUsersByFavorites->count() > 0)
                        @foreach($topUsersByFavorites as $user)
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                @if($user->avatar_url)
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                         class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                @else
                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3" 
                                         style="width: 40px; height: 40px;">
                                        <i class="fas fa-heart text-white"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-0">{{ $user->name }}</h6>
                                    <small class="text-muted">{{ $user->favorites_count }} favoris</small>
                                </div>
                            </div>
                            <a href="{{ route('app-users.show', $user->id) }}" class="btn btn-sm btn-outline-success">
                                Voir
                            </a>
                        </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">Aucune donnée disponible</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Utilisateurs récents -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Utilisateurs récents</h5>
                </div>
                <div class="card-body">
                    @if($recentUsers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Email</th>
                                        <th>Provider</th>
                                        <th>Réservations</th>
                                        <th>Favoris</th>
                                        <th>Statut</th>
                                        <th>Inscrit le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentUsers as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($user->avatar_url)
                                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                                         class="rounded-circle me-2" style="width: 30px; height: 30px; object-fit: cover;">
                                                @else
                                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center me-2" 
                                                         style="width: 30px; height: 30px;">
                                                        <i class="fas fa-user text-white" style="font-size: 12px;"></i>
                                                    </div>
                                                @endif
                                                <span class="fw-medium">{{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @php
                                                $providerClass = match($user->provider) {
                                                    'google' => 'bg-danger',
                                                    'facebook' => 'bg-primary',
                                                    default => 'bg-secondary'
                                                };
                                                $providerLabel = match($user->provider) {
                                                    'google' => 'Google',
                                                    'facebook' => 'Facebook',
                                                    default => 'Email'
                                                };
                                            @endphp
                                            <span class="badge {{ $providerClass }}">{{ $providerLabel }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">{{ $user->reservations_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-warning">{{ $user->favorites_count }}</span>
                                        </td>
                                        <td>
                                            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-danger' }}">
                                                {{ $user->is_active ? 'Actif' : 'Inactif' }}
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('app-users.show', $user->id) }}" 
                                               class="btn btn-sm btn-outline-primary">
                                                Voir
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center py-4">Aucun utilisateur récent trouvé</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection