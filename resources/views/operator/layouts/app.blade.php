<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VISIT DJIBOUTI - @yield('title')</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    <!-- Multi-Provider Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

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
    </style>
    @yield('styles')

</head>

<body class="preload">
    <div class="wrapper">
        <!-- Menu latéral -->
        <nav id="sidebar">
            <div class="sidebar-header">
                <h2 class="site-title">VISIT DJIBOUTI</h2>
                <div class="site-title-short">VD</div>
                <p class="site-subtitle">Espace Tour Operator</p>
            </div>

            <div class="menu-items">
                <ul class="list-unstyled components">
                    <li class="{{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                        <a href="{{ route('operator.dashboard') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                            title="Tableau de bord">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Tableau de bord</span>
                        </a>
                    </li>
                </ul>

                <div class="menu-section">
                    <div class="menu-section-title">GESTION</div>
                    <ul class="list-unstyled components">
                        @if(Auth::guard('operator')->user()->canManageEvents())
                        <li class="{{ request()->routeIs('operator.events.*') ? 'active' : '' }}">
                            <a href="{{ route('operator.events.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Événements">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Événements</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::guard('operator')->user()->canManageTours())
                        <li class="{{ request()->routeIs('operator.tours.*') ? 'active' : '' }}">
                            <a href="{{ route('operator.tours.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Tours Guidés">
                                <i class="fas fa-route"></i>
                                <span>Tours Guidés</span>
                            </a>
                        </li>
                        @endif

                        @if(Auth::guard('operator')->user()->canViewReservations())
                        <li class="{{ request()->routeIs('operator.reservations.*') ? 'active' : '' }}">
                            <a href="{{ route('operator.reservations.index') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Réservations">
                                <i class="fas fa-ticket-alt"></i>
                                <span>Réservations</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="menu-section">
                    <div class="menu-section-title">PARAMÈTRES</div>
                    <ul class="list-unstyled components">
                        @if(Auth::guard('operator')->user()->canManageProfile())
                        <li class="{{ request()->routeIs('operator.profile.*') ? 'active' : '' }}">
                            <a href="{{ route('operator.profile.show') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Profil">
                                <i class="fas fa-user-cog"></i>
                                <span>Mon Profil</span>
                            </a>
                        </li>

                        <li class="{{ request()->routeIs('operator.tour-operator.*') ? 'active' : '' }}">
                            <a href="{{ route('operator.tour-operator.show') }}" data-bs-toggle="tooltip" data-bs-placement="right"
                                title="Mon Entreprise">
                                <i class="fas fa-building"></i>
                                <span>Mon Entreprise</span>
                            </a>
                        </li>
                        @endif
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
                    <!-- Dropdown pour le profil et la déconnexion -->
                    <div class="dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-user-circle"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <h6 class="dropdown-header">{{ Auth::guard('operator')->user()->name }}</h6>
                                <p class="dropdown-header text-muted small mb-0">{{ Auth::guard('operator')->user()->tourOperator->name }}</p>
                            </li>
                            <li><a class="dropdown-item" href="{{ route('operator.profile.show') }}">Mon profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('operator.tour-operator.show') }}">Mon Entreprise</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('operator.logout') }}" id="logout-form">
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

    @stack('scripts')

</body>

</html>