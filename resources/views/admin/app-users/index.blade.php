@extends('layouts.admin')

@section('title', 'Gestion des Utilisateurs Mobiles')
@section('page-title', 'Gestion des Utilisateurs Mobiles')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Utilisateurs Mobiles</h1>
                    <p class="text-muted mb-0">Gestion des utilisateurs de l'application mobile</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('app-users.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-1"></i> Tableau de bord
                    </a>
                    <a href="{{ route('app-users.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i> Exporter
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-cog me-1"></i> Actions
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="sendBulkNotification()">
                                <i class="fas fa-bell me-2"></i> Notification groupée
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="generateReport()">
                                <i class="fas fa-file-pdf me-2"></i> Générer rapport
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="{{ route('app-users.stats') }}">
                                <i class="fas fa-chart-bar me-2"></i> Statistiques détaillées
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Composant Livewire principal -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @livewire('admin.app-user-manager')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    // Écouter les événements Livewire
    Livewire.on('userUpdated', () => {
        console.log('Utilisateur mis à jour');
    });
});

function sendBulkNotification() {
    // TODO: Implement bulk notification functionality
    alert('Fonctionnalité à implémenter : Notification groupée');
}

function generateReport() {
    // TODO: Implement report generation functionality
    alert('Fonctionnalité à implémenter : Génération de rapport');
}
</script>
@endpush