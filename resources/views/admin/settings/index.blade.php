@extends('layouts.admin')

@section('title', 'Paramètres')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Paramètres</h4>
            <p class="text-muted mb-0">Configuration générale de l'application</p>
        </div>
    </div>

    <!-- Onglets de navigation -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" 
                    id="organization-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#organization-content" 
                    type="button" 
                    role="tab"
                    aria-controls="organization-content" 
                    aria-selected="true">
                <i class="fas fa-building me-2"></i>
                Organisation
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" 
                    id="embassies-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#embassies-content" 
                    type="button" 
                    role="tab"
                    aria-controls="embassies-content" 
                    aria-selected="false">
                <i class="fas fa-building me-2"></i>
                Ambassades
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" 
                    id="general-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#general-content" 
                    type="button" 
                    role="tab"
                    aria-controls="general-content" 
                    aria-selected="false">
                <i class="fas fa-cogs me-2"></i>
                Général
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" 
                    id="appearance-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#appearance-content" 
                    type="button" 
                    role="tab"
                    aria-controls="appearance-content" 
                    aria-selected="false">
                <i class="fas fa-palette me-2"></i>
                Apparence
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" 
                    id="notifications-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#notifications-content" 
                    type="button" 
                    role="tab"
                    aria-controls="notifications-content" 
                    aria-selected="false">
                <i class="fas fa-bell me-2"></i>
                Notifications
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" 
                    id="security-tab" 
                    data-bs-toggle="tab" 
                    data-bs-target="#security-content" 
                    type="button" 
                    role="tab"
                    aria-controls="security-content" 
                    aria-selected="false">
                <i class="fas fa-shield-alt me-2"></i>
                Sécurité
            </button>
        </li>
    </ul>

    <!-- Contenu des onglets -->
    <div class="tab-content" id="settingsTabsContent">
        <!-- Onglet Organisation -->
        <div class="tab-pane fade show active" 
             id="organization-content" 
             role="tabpanel" 
             aria-labelledby="organization-tab">
            <livewire:admin.organization-info-manager />
        </div>

        <!-- Onglet Ambassades -->
        <div class="tab-pane fade" 
             id="embassies-content" 
             role="tabpanel" 
             aria-labelledby="embassies-tab">
            <livewire:admin.embassy-manager />
        </div>

        <!-- Onglet Général -->
        <div class="tab-pane fade" 
             id="general-content" 
             role="tabpanel" 
             aria-labelledby="general-tab">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Paramètres généraux
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-cogs fa-3x mb-3 d-block"></i>
                        <h6>Paramètres généraux</h6>
                        <p class="mb-0">Configuration générale de l'application (à implémenter)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Apparence -->
        <div class="tab-pane fade" 
             id="appearance-content" 
             role="tabpanel" 
             aria-labelledby="appearance-tab">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-palette me-2"></i>
                        Paramètres d'apparence
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-palette fa-3x mb-3 d-block"></i>
                        <h6>Apparence</h6>
                        <p class="mb-0">Personnalisation de l'interface (thème, couleurs, etc.) - À implémenter</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Notifications -->
        <div class="tab-pane fade" 
             id="notifications-content" 
             role="tabpanel" 
             aria-labelledby="notifications-tab">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-bell me-2"></i>
                        Paramètres de notifications
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-bell fa-3x mb-3 d-block"></i>
                        <h6>Notifications</h6>
                        <p class="mb-0">Configuration des notifications email, push, etc. - À implémenter</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Onglet Sécurité -->
        <div class="tab-pane fade" 
             id="security-content" 
             role="tabpanel" 
             aria-labelledby="security-tab">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-shield-alt me-2"></i>
                        Paramètres de sécurité
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-shield-alt fa-3x mb-3 d-block"></i>
                        <h6>Sécurité</h6>
                        <p class="mb-0">Configuration de la sécurité, mots de passe, sessions, etc. - À implémenter</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gérer la persistance de l'onglet actif
    const settingsTabs = document.querySelectorAll('#settingsTabs button[data-bs-toggle="tab"]');
    
    // Restaurer l'onglet actif depuis le localStorage
    const activeTab = localStorage.getItem('activeSettingsTab');
    if (activeTab) {
        const tabButton = document.querySelector(`#settingsTabs button[data-bs-target="${activeTab}"]`);
        if (tabButton) {
            const tab = new bootstrap.Tab(tabButton);
            tab.show();
        }
    }
    
    // Sauvegarder l'onglet actif dans le localStorage
    settingsTabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            localStorage.setItem('activeSettingsTab', event.target.getAttribute('data-bs-target'));
        });
    });
});
</script>
@endsection