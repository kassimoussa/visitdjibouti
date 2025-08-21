@extends('layouts.admin')

@section('title', 'Gestion des Réservations')
@section('page-title', 'Gestion des Réservations')

@section('content')
<div class="container-fluid">
    <!-- En-tête avec statistiques rapides -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">Gestion des Réservations</h1>
                    <p class="text-muted mb-0">Vue d'ensemble de toutes les réservations (POI et Événements)</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('reservations.dashboard') }}" class="btn btn-outline-primary">
                        <i class="fas fa-chart-line me-1"></i> Tableau de bord
                    </a>
                    <a href="{{ route('reservations.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i> Exporter
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Composant Livewire principal -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            @livewire('admin.global-reservation-manager')
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    // Écouter les événements Livewire pour les notifications
    Livewire.on('reservationUpdated', () => {
        // Rafraîchir les statistiques si nécessaire
        console.log('Réservation mise à jour');
    });
});
</script>
@endpush