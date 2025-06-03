/**
 * Script principal pour le panneau d'administration Visit Djibouti
 */
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips Bootstrap
    initializeTooltips();
    
    // Configurer le bouton de bascule du sidebar
    setupSidebarToggle();
});

/**
 * Initialise les tooltips Bootstrap sur tous les éléments avec l'attribut data-bs-toggle="tooltip"
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Stocker la liste des tooltips dans une variable globale pour y accéder plus tard
    window.appTooltips = tooltipList;
}

/**
 * Configure le comportement du bouton de bascule du sidebar
 */
function setupSidebarToggle() {
    const sidebar = document.getElementById('sidebar');
    const sidebarCollapseBtn = document.getElementById('sidebarCollapse');
    const collapseIcon = sidebarCollapseBtn.querySelector('i');
    
    // Fonction pour mettre à jour l'icône du bouton
    function updateCollapseIcon() {
        if (sidebar.classList.contains('active')) {
            collapseIcon.classList.remove('fa-bars');
            collapseIcon.classList.add('fa-indent');
        } else {
            collapseIcon.classList.remove('fa-indent');
            collapseIcon.classList.add('fa-bars');
        }
    }
    
    // Gérer l'événement de clic sur le bouton
    sidebarCollapseBtn.addEventListener('click', function() {
        // Basculer la classe active sur le sidebar
        sidebar.classList.toggle('active');
        
        // Mettre à jour l'icône
        updateCollapseIcon();
        
        // Détruire et réinitialiser les tooltips
        resetTooltips();
    });
}

/**
 * Réinitialise tous les tooltips après un changement d'état du sidebar
 * Les tooltips doivent être recréés pour s'adapter à la nouvelle position des éléments
 */
function resetTooltips() {
    // Détruire tous les tooltips existants
    if (window.appTooltips) {
        window.appTooltips.forEach(function(tooltip) {
            tooltip.dispose();
        });
    }
    
    // Recréer les tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    window.appTooltips = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}