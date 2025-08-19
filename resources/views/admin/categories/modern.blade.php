@extends('layouts.admin')

@section('title', 'Gestion Moderne des Catégories')

@section('content')
<div class="container-fluid px-4">
    <div class="modern-categories-page">
        <!-- Composant Livewire -->
        <div class="category-manager-container">
            @livewire('admin.modern-category-manager')
        </div>
    </div>
</div>

<script>
// Fonction pour exécuter le seeder (à adapter selon votre setup)
function runSeeder() {
    if (confirm('Cela va réinitialiser toutes les catégories avec la structure touristique. Continuer ?')) {
        // Ici vous pourriez faire un appel AJAX vers un endpoint qui exécute le seeder
        // Ou rediriger vers une route qui l'exécute
        alert('Fonctionnalité à implémenter : exécution du TourismCategoriesSeeder');
    }
}

// Améliorer l'UX avec des animations supplémentaires
document.addEventListener('DOMContentLoaded', function() {
    // Animation d'entrée pour les éléments
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    });

    // Observer tous les éléments avec animation
    document.querySelectorAll('.category-card, .subcategory-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.3s ease-out';
        observer.observe(el);
    });
});

// Messages de feedback améliorés
document.addEventListener('livewire:init', () => {
    Livewire.on('category-saved', (message) => {
        // Toast notification ou autre feedback
        console.log('Catégorie sauvegardée:', message);
    });
});
</script>

<style>
/* Styles spécifiques à la page */
.modern-categories-page {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    min-height: calc(100vh - 100px);
    border-radius: 15px;
    padding: 2rem;
    margin: -1rem;
}

.page-header {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.category-manager-container {
    background: white;
    border-radius: 12px;
    padding: 2rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    border: 1px solid #e9ecef;
}

.page-actions .btn {
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.page-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Animation pour le loading */
.category-manager-container.loading {
    position: relative;
    pointer-events: none;
}

.category-manager-container.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(2px);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    z-index: 1000;
}

.category-manager-container.loading::before {
    content: 'Chargement...';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1001;
    padding: 1rem 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    font-weight: 600;
    color: #6c757d;
}

/* Responsive pour mobile */
@media (max-width: 768px) {
    .modern-categories-page {
        padding: 1rem;
        margin: -0.5rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .page-header .d-flex {
        flex-direction: column;
        gap: 1rem;
        align-items: start !important;
    }
    
    .page-actions {
        display: flex;
        gap: 0.5rem;
        width: 100%;
    }
    
    .page-actions .btn {
        flex: 1;
        font-size: 0.875rem;
        padding: 0.5rem 1rem;
    }
    
    .category-manager-container {
        padding: 1rem;
    }
}

/* Animations personnalisées */
@keyframes fadeInScale {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.category-manager-container {
    animation: fadeInScale 0.4s ease-out;
}

.page-header {
    animation: fadeInScale 0.3s ease-out;
}

/* Style pour les tooltips */
[data-bs-toggle="tooltip"] {
    cursor: help;
}

/* Amélioration des focus states */
.btn:focus,
.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    border-color: #3498db;
}

/* Animation pour les notifications */
.alert {
    animation: slideInDown 0.3s ease-out;
}

@keyframes slideInDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}
</style>
@endsection

@push('scripts')
<script>
// Functions globales pour le sélecteur d'icônes
// Variable pour stocker le fournisseur actuel
let currentProvider = 'fontawesome';

function updateIconPlaceholder(provider) {
    const input = document.getElementById('iconInput');
    const suggestions = document.getElementById('iconSuggestions');
    
    if (!input || !suggestions) return;
    
    // Stocker le fournisseur sélectionné
    currentProvider = provider;
    
    const placeholders = {
        'fontawesome': 'fas fa-folder',
        'bootstrap': 'bi-folder',
        'phosphor': 'ph ph-folder',
        'tabler': 'ti ti-folder',
        'flags': 'fi fi-fr',
        'emojis': '🏛️'
    };
    
    const suggestionContent = {
        'fontawesome': `
            <div class="mb-2"><span class="text-muted fw-bold">FontAwesome:</span></div>
            <div class="d-flex flex-wrap">
                <span class="icon-suggestion" onclick="selectIcon('fas fa-home')">fas fa-home</span>
                <span class="icon-suggestion" onclick="selectIcon('fas fa-user')">fas fa-user</span>
                <span class="icon-suggestion" onclick="selectIcon('fas fa-heart')">fas fa-heart</span>
                <span class="icon-suggestion" onclick="selectIcon('fas fa-star')">fas fa-star</span>
                <span class="icon-suggestion" onclick="selectIcon('fas fa-map-marker-alt')">fas fa-map-marker-alt</span>
            </div>
        `,
        'bootstrap': `
            <div class="mb-2"><span class="text-muted fw-bold">Bootstrap Icons:</span></div>
            <div class="d-flex flex-wrap">
                <span class="icon-suggestion" onclick="selectIcon('bi-house')">bi-house</span>
                <span class="icon-suggestion" onclick="selectIcon('bi-person')">bi-person</span>
                <span class="icon-suggestion" onclick="selectIcon('bi-heart')">bi-heart</span>
                <span class="icon-suggestion" onclick="selectIcon('bi-star')">bi-star</span>
                <span class="icon-suggestion" onclick="selectIcon('bi-geo-alt')">bi-geo-alt</span>
            </div>
        `,
        'phosphor': `
            <div class="mb-2"><span class="text-muted fw-bold">Phosphor Icons:</span></div>
            <div class="d-flex flex-wrap">
                <span class="icon-suggestion" onclick="selectIcon('ph ph-house')">ph ph-house</span>
                <span class="icon-suggestion" onclick="selectIcon('ph ph-user')">ph ph-user</span>
                <span class="icon-suggestion" onclick="selectIcon('ph ph-heart')">ph ph-heart</span>
                <span class="icon-suggestion" onclick="selectIcon('ph ph-star')">ph ph-star</span>
                <span class="icon-suggestion" onclick="selectIcon('ph ph-map-pin')">ph ph-map-pin</span>
            </div>
        `,
        'tabler': `
            <div class="mb-2"><span class="text-muted fw-bold">Tabler Icons:</span></div>
            <div class="d-flex flex-wrap">
                <span class="icon-suggestion" onclick="selectIcon('ti ti-home')">ti ti-home</span>
                <span class="icon-suggestion" onclick="selectIcon('ti ti-user')">ti ti-user</span>
                <span class="icon-suggestion" onclick="selectIcon('ti ti-heart')">ti ti-heart</span>
                <span class="icon-suggestion" onclick="selectIcon('ti ti-star')">ti ti-star</span>
                <span class="icon-suggestion" onclick="selectIcon('ti ti-map-pin')">ti ti-map-pin</span>
            </div>
        `,
        'flags': `
            <div class="mb-2"><span class="text-muted fw-bold">Flag Icons:</span></div>
            <div class="d-flex flex-wrap">
                <span class="icon-suggestion" onclick="selectIcon('fi fi-fr')">fi fi-fr</span>
                <span class="icon-suggestion" onclick="selectIcon('fi fi-gb')">fi fi-gb</span>
                <span class="icon-suggestion" onclick="selectIcon('fi fi-us')">fi fi-us</span>
                <span class="icon-suggestion" onclick="selectIcon('fi fi-dj')">fi fi-dj</span>
                <span class="icon-suggestion" onclick="selectIcon('fi fi-de')">fi fi-de</span>
            </div>
        `,
        'emojis': `
            <div class="mb-2"><span class="text-muted fw-bold">Emojis:</span></div>
            <div class="d-flex flex-wrap">
                <span class="icon-suggestion" onclick="selectIcon('🏛️')">🏛️</span>
                <span class="icon-suggestion" onclick="selectIcon('🌟')">🌟</span>
                <span class="icon-suggestion" onclick="selectIcon('🎯')">🎯</span>
                <span class="icon-suggestion" onclick="selectIcon('🏖️')">🏖️</span>
                <span class="icon-suggestion" onclick="selectIcon('🗺️')">🗺️</span>
            </div>
        `
    };
    
    input.placeholder = placeholders[provider] || placeholders['fontawesome'];
    suggestions.innerHTML = suggestionContent[provider] || suggestionContent['fontawesome'];
}

function selectIcon(icon) {
    const input = document.getElementById('iconInput');
    if (input) {
        // Mettre à jour la valeur du champ
        input.value = icon;
        
        // Déclencher les événements pour que Livewire détecte le changement
        input.dispatchEvent(new Event('input', { bubbles: true }));
        input.dispatchEvent(new Event('change', { bubbles: true }));
        
        // Maintenir les suggestions du fournisseur actuel après la sélection
        setTimeout(() => {
            if (currentProvider !== 'fontawesome') {
                updateIconPlaceholder(currentProvider);
                
                // Garder le dropdown sur le bon fournisseur
                const dropdown = document.getElementById('iconProvider');
                if (dropdown) {
                    dropdown.value = currentProvider;
                }
            }
        }, 100);
    }
}

// Fonction pour maintenir l'état du fournisseur après les updates Livewire
function maintainProviderState() {
    if (currentProvider !== 'fontawesome') {
        updateIconPlaceholder(currentProvider);
        
        // Garder le dropdown sur le bon fournisseur
        const dropdown = document.getElementById('iconProvider');
        if (dropdown) {
            dropdown.value = currentProvider;
        }
    }
}

// Scripts additionnels pour l'UX
document.addEventListener('livewire:initialized', () => {
    // Gestion des états de chargement
    Livewire.on('loading-start', () => {
        document.querySelector('.category-manager-container').classList.add('loading');
    });
    
    Livewire.on('loading-end', () => {
        document.querySelector('.category-manager-container').classList.remove('loading');
    });
    
    // Maintenir l'état du fournisseur après les updates Livewire
    Livewire.hook('morph.updated', () => {
        setTimeout(() => {
            maintainProviderState();
        }, 50);
    });
    
    // Auto-dismiss des alertes après 5 secondes
    setTimeout(() => {
        document.querySelectorAll('.alert[role="alert"]').forEach(alert => {
            if (alert.querySelector('.btn-close')) {
                alert.querySelector('.btn-close').click();
            }
        });
    }, 5000);
});

// Gestion des raccourcis clavier
document.addEventListener('keydown', function(e) {
    // Ctrl+N pour nouvelle catégorie
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        Livewire.dispatch('create-category');
    }
    
    // Escape pour fermer les modals
    if (e.key === 'Escape') {
        Livewire.dispatch('close-modal');
    }
});

// Amélioration de l'accessibilité
document.addEventListener('DOMContentLoaded', function() {
    // Initialiser les tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Ajouter des attributs ARIA
    document.querySelectorAll('.category-card').forEach((card, index) => {
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', `Catégorie ${index + 1}`);
        
        // Navigation au clavier
        card.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                card.click();
            }
        });
    });
});

// Réinitialiser les tooltips après les updates Livewire
document.addEventListener('livewire:initialized', () => {
    Livewire.hook('morph.updated', () => {
        setTimeout(() => {
            // Réinitialiser les tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        }, 100);
    });
});
</script>
@endpush