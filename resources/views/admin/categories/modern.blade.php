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
// Scripts additionnels pour l'UX
document.addEventListener('livewire:initialized', () => {
    // Gestion des états de chargement
    Livewire.on('loading-start', () => {
        document.querySelector('.category-manager-container').classList.add('loading');
    });
    
    Livewire.on('loading-end', () => {
        document.querySelector('.category-manager-container').classList.remove('loading');
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
</script>
@endpush