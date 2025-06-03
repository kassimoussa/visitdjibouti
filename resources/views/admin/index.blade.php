@extends('layouts.admin')

@section('title', 'Tableau de bord')

@section('page-title', 'Tableau de bord')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="content-card">
                <h1 class="mb-4">Tableau de bord</h1>
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="content-card" style="background-color: #f0f7ff; border-left: 4px solid #3860f8;">
                            <h5>Visiteurs</h5>
                            <h2>14,258</h2>
                            <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> +5.2%</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="content-card" style="background-color: #fff8f0; border-left: 4px solid #ff9800;">
                            <h5>Réservations</h5>
                            <h2>562</h2>
                            <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> +12.7%</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="content-card" style="background-color: #f0fff4; border-left: 4px solid #4caf50;">
                            <h5>Avis</h5>
                            <h2>128</h2>
                            <p class="text-danger mb-0"><i class="fas fa-arrow-down"></i> -2.8%</p>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-4">
                        <div class="content-card" style="background-color: #fff0f0; border-left: 4px solid #f44336;">
                            <h5>Nouveaux utilisateurs</h5>
                            <h2>87</h2>
                            <p class="text-success mb-0"><i class="fas fa-arrow-up"></i> +8.5%</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="content-card">
                <h4 class="mb-4">Statistiques de visites</h4>
                <div style="height: 300px; background-color: #f5f7fb; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                    <p class="text-center text-muted">Graphique des visites (visualisation)</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="content-card">
                <h4 class="mb-4">Activités récentes</h4>
                <div class="d-flex align-items-center mb-3">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background-color: #e1e8ff; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                        <i class="fas fa-user text-primary"></i>
                    </div>
                    <div>
                        <h6 class="mb-0">Nouvel utilisateur</h6>
                        <small class="text-muted">Il y a 5 minutes</small>
                    </div>
                </div>
                <!-- Autres activités... -->
            </div>
        </div>
    </div>
</div>
@endsection