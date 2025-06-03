<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VISIT DJIBOUTI - @yield('title')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    @yield('styles')

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <link href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css" rel="stylesheet">


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
    </style>

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
                        <li class="{{ request()->is('news*') ? 'active' : '' }}">
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
                    </ul>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">INTERACTIONS</div>
                    <ul class="list-unstyled components">
                        <li class="{{ request()->is('reviews*') ? 'active' : '' }}">
                            <a href="{{ route('reviews.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
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
                        <li class="{{ request()->is('stats*') ? 'active' : '' }}">
                            <a href="{{ route('stats.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
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

    <script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>

    <!-- Initialisez GLightbox -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const lightbox = GLightbox({
                touchNavigation: true,
                loop: true,
                autoplayVideos: true
            });
        });
    </script>
    
    @stack('scripts')
    @livewireScripts

</body>

</html>
