<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VISIT DJIBOUTI - @yield('title')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">

    <!-- Multi-Provider Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.2.3/css/flag-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@phosphor-icons/web@2.1.1/src/regular/style.css">
    
    @livewireStyles
    @stack('styles')

    <style>
        /* Rendre le contenu invisible pendant le chargement */
        body {
            visibility: hidden;
        }

        /* Empêcher les transitions pendant le chargement de la page */
        .preload * {
            transition: none !important;
        }

        .spinner-fade {
            transition: opacity 0.3s ease-in-out;
        }

        .spinner-fade[wire\:loading] {
            opacity: 1;
        }

        .spinner-fade:not([wire\:loading]) {
            opacity: 0;
        }

        /* Styles pour le modal "Bientôt disponible" */
        .bg-gradient-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .bg-gradient-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        
        .feature-icon-container {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .coming-soon-animation {
            position: relative;
            display: inline-block;
        }
        
        .construction-icon {
            position: relative;
            display: inline-block;
            animation: bounce 2s infinite;
        }
        
        .construction-sparks {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        .spark {
            position: absolute;
            font-size: 1.2rem;
            animation: sparkle 3s infinite;
        }
        
        .spark-1 {
            top: -10px;
            left: -10px;
            animation-delay: 0s;
        }
        
        .spark-2 {
            top: -15px;
            right: -10px;
            animation-delay: 1s;
        }
        
        .spark-3 {
            bottom: -10px;
            left: 50%;
            animation-delay: 2s;
        }
        
        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateY(0);
            }
            40% {
                transform: translateY(-10px);
            }
            60% {
                transform: translateY(-5px);
            }
        }
        
        @keyframes sparkle {
            0%, 100% {
                opacity: 0;
                transform: scale(0.5);
            }
            50% {
                opacity: 1;
                transform: scale(1);
            }
        }
        
        .progress-bar-animated {
            animation: progress-bar-stripes 1s linear infinite;
        }
        
        @keyframes progress-bar-stripes {
            0% {
                background-position: 1rem 0;
            }
            100% {
                background-position: 0 0;
            }
        }
        
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }
        
        .modal-header {
            border-radius: 15px 15px 0 0;
        }
        
        .coming-soon-link {
            position: relative;
            overflow: hidden;
        }
        
        .coming-soon-link::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }
        
        .coming-soon-link:hover::after {
            left: 100%;
        }
    </style>
    @yield('styles')
    @stack('style')

</head>

<body class="preload">
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>




    <div class="wrapper">
        <!-- Menu latéral -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h2 class="site-title">VISIT DJIBOUTI</h2>
                <div class="site-title-short">VD</div>
                <p class="site-subtitle">Panneau d'administration</p>
            </div>

            <div class="menu-items">
                <ul class="list-unstyled components">
                    <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
                        <a href="{{ route('dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Tableau de bord">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Tableau de bord</span>
                        </a>
                    </li>
                </ul>

                <div class="menu-section">
                    <div class="menu-section-title">CONTENU</div>
                    <ul class="list-unstyled components">
                        <li class="{{ request()->is('pois*') ? 'active' : '' }}">
                            <a href="{{ route('pois.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Points d'intérêt">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Points d'intérêt</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('events*') ? 'active' : '' }}">
                            <a href="{{ route('events.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Événements">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Événements</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('categories*') ? 'active' : '' }}">
                            <a href="{{ route('categories.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Catégories">
                                <i class="fas fa-tags"></i>
                                <span>Catégories</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('news*') ? 'active' : '' }} hidden-menu-item" data-menu="news">
                            <a href="{{ route('news.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Actualités">
                                <i class="fas fa-newspaper"></i>
                                <span>Actualités</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('media*') ? 'active' : '' }}">
                            <a href="{{ route('media.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Médias">
                                <i class="fas fa-photo-video"></i>
                                <span>Médias</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('external-links*') ? 'active' : '' }} hidden-menu-item" data-menu="external-links">
                            <a href="{{ route('external-links.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Liens externes">
                                <i class="fas fa-external-link-alt"></i>
                                <span>Liens externes</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('tour-operators*') ? 'active' : '' }}">
                            <a href="{{ route('tour-operators.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Opérateurs de tour">
                                <i class="fas fa-route"></i>
                                <span>Opérateurs de tour</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">INTERACTIONS</div>
                    <ul class="list-unstyled components">
                        <li class="{{ request()->is('reservations*') ? 'active' : '' }}">
                            <a href="{{ route('reservations.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Réservations">
                                <i class="fas fa-calendar-check"></i>
                                <span>Réservations</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('reviews*') ? 'active' : '' }}">
                            <a href="#" class="coming-soon-link" data-feature="Avis & Commentaires" data-icon="fas fa-comments" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Avis & Commentaires">
                                <i class="fas fa-comments"></i>
                                <span>Avis & Commentaires</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">ADMINISTRATION</div>
                    <ul class="list-unstyled components">
                        <li class="{{ request()->is('users*') ? 'active' : '' }}">
                            <a href="{{ route('users.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Utilisateurs">
                                <i class="fas fa-users"></i>
                                <span>Utilisateurs</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('app-users*') ? 'active' : '' }}">
                            <a href="{{ route('app-users.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Utilisateurs Mobiles">
                                <i class="fas fa-mobile-alt"></i>
                                <span>Utilisateurs Mobiles</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('stats*') ? 'active' : '' }}">
                            <a href="#" class="coming-soon-link" data-feature="Statistiques" data-icon="fas fa-chart-line" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Statistiques">
                                <i class="fas fa-chart-line"></i>
                                <span>Statistiques</span>
                            </a>
                        </li>
                        <li class="{{ request()->is('settings*') ? 'active' : '' }}">
                            <a href="{{ route('settings.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Paramètres">
                                <i class="fas fa-cog"></i>
                                <span>Paramètres</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Conteneur du contenu principal -->
        <div id="content-container">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <button type="button" id="sidebarCollapse" class="btn">
                        <i class="fas fa-bars"></i>
                    </button>
                    <span class="topbar-title">@yield('page-title', 'VISIT DJIBOUTI')</span>
                </div>
                <div class="topbar-right">
                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Rechercher">
                        <i class="fas fa-search"></i>
                    </a>
                    {{-- <a href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Notifications">
                        <div style="position: relative;">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </div>
                    </a> --}}
                    <a href="#" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Aide">
                        <i class="fas fa-question-circle"></i>
                    </a>
                    <!-- Dropdown pour le profil et la déconnexion -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">{{ Auth::guard('admin')->user()->name }}</h6>
                            </li>
                            <li><a class="dropdown-item" href={{ route('profile.show') }}>Mon profil</a>
                            </li>
                            <li><a class="dropdown-item" href="#">Paramètres</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout', [], false) }}" id="logout-form">
                                    @csrf
                                    <a class="dropdown-item" href="#"
                                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                        <i class="fas fa-sign-out-alt me-2"></i> Déconnexion
                                    </a>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Contenu de la page -->
            <div id="content">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Modal "Bientôt disponible" -->
    <div class="modal fade" id="comingSoonModal" tabindex="-1" aria-labelledby="comingSoonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-gradient-primary text-white border-0">
                    <div class="d-flex align-items-center">
                        <div class="feature-icon-container me-3">
                            <i id="modalFeatureIcon" class="fas fa-rocket"></i>
                        </div>
                        <div>
                            <h5 class="modal-title mb-0" id="comingSoonModalLabel">
                                <span id="modalFeatureName">Fonctionnalité</span> - Bientôt disponible
                            </h5>
                            <small class="opacity-75">En cours de développement</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-4">
                        <div class="coming-soon-animation">
                            <div class="construction-icon">
                                <i class="fas fa-hard-hat text-warning" style="font-size: 3rem;"></i>
                                <div class="construction-sparks">
                                    <span class="spark spark-1">✨</span>
                                    <span class="spark spark-2">⚡</span>
                                    <span class="spark spark-3">💫</span>
                                </div>
                            </div>
                        </div>
                        <h6 class="text-muted mt-3">Cette fonctionnalité est en cours de développement</h6>
                        <p class="text-muted small mb-4">
                            Notre équipe travaille activement sur <strong id="modalFeatureNameInText">cette fonctionnalité</strong> 
                            pour vous offrir la meilleure expérience possible.
                        </p>
                    </div>
                    
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar bg-gradient-success progress-bar-animated" role="progressbar" style="width: 65%" aria-valuenow="65" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <div class="text-center">
                        <small class="text-muted">Progression : 65% complété</small>
                    </div>
                    
                    <div class="mt-4 p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-lightbulb me-2"></i>
                            En attendant, vous pouvez :
                        </h6>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-1">
                                <i class="fas fa-check text-success me-2"></i>
                                Gérer vos <strong>Points d'intérêt</strong>
                            </li>
                            <li class="mb-1">
                                <i class="fas fa-check text-success me-2"></i>
                                Créer et modifier vos <strong>Événements</strong>
                            </li>
                            <li class="mb-1">
                                <i class="fas fa-check text-success me-2"></i>
                                Organiser vos <strong>Catégories</strong>
                            </li>
                            <li>
                                <i class="fas fa-check text-success me-2"></i>
                                Gérer votre <strong>Bibliothèque de médias</strong>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Fermer
                    </button>
                    <button type="button" class="btn btn-primary">
                        <i class="fas fa-bell me-2"></i>Me notifier
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>

    <!-- Initialisez GLightbox -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lightbox = GLightbox({
                touchNavigation: true,
                loop: true,
                autoplayVideos: true
            });

            // Gestion des liens "Bientôt disponible"
            document.querySelectorAll('.coming-soon-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const featureName = this.getAttribute('data-feature');
                    const featureIcon = this.getAttribute('data-icon');
                    
                    // Mise à jour du contenu du modal
                    document.getElementById('modalFeatureName').textContent = featureName;
                    document.getElementById('modalFeatureNameInText').textContent = featureName.toLowerCase();
                    document.getElementById('modalFeatureIcon').className = featureIcon;
                    
                    // Affichage du modal
                    const modal = new bootstrap.Modal(document.getElementById('comingSoonModal'));
                    modal.show();
                });
            });
        });
    </script>
    
    @stack('scripts')
    @livewireScripts

</body>

</html>
