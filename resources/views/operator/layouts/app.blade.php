<!DOCTYPE html>
<html lang="{{ session('locale', 'fr') }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ Auth::guard('operator')->user()->tourOperator->getTranslatedName(session('locale', 'fr')) }}</title>

    @vite(['resources/sass/operator.scss'])
</head>
<body>
    <!-- Sidebar -->
    <div class="operator-sidebar" id="sidebar">
        <div class="operator-sidebar-header">
            <div class="operator-logo">
                <i class="fas fa-plane-departure me-2"></i>
                Visit Djibouti
            </div>
            <small class="text-muted">Tour Operators</small>
            <hr>
            <div class="text-center">
                <div class="mb-2">
                    @if(Auth::guard('operator')->user()->avatar)
                        <img src="{{ Storage::url(Auth::guard('operator')->user()->avatar) }}"
                             alt="Avatar"
                             class="rounded-circle"
                             style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center"
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    @endif
                </div>
                <h6 class="mb-0">{{ Auth::guard('operator')->user()->name }}</h6>
                <small class="text-muted">{{ Auth::guard('operator')->user()->position ?? 'Tour Operator' }}</small>
            </div>
        </div>

        <ul class="operator-nav">
            <li class="operator-nav-item">
                <a href="{{ route('operator.dashboard') }}"
                   class="operator-nav-link {{ request()->routeIs('operator.dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>

            @if(Auth::guard('operator')->user()->canManageEvents())
            <li class="operator-nav-item">
                <a href="{{ route('operator.events.index') }}"
                   class="operator-nav-link {{ request()->routeIs('operator.events.*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-alt"></i>
                    Événements
                    @if(Auth::guard('operator')->user()->managedEvents()->where('status', 'draft')->count() > 0)
                        <span class="badge bg-warning ms-2">
                            {{ Auth::guard('operator')->user()->managedEvents()->where('status', 'draft')->count() }}
                        </span>
                    @endif
                </a>
            </li>
            @endif

            @if(Auth::guard('operator')->user()->canManageTours())
            <li class="operator-nav-item">
                <a href="{{ route('operator.tours.index') }}"
                   class="operator-nav-link {{ request()->routeIs('operator.tours.*') ? 'active' : '' }}">
                    <i class="fas fa-route"></i>
                    Tours Guidés
                </a>
            </li>
            @endif

            @if(Auth::guard('operator')->user()->canViewReservations())
            <li class="operator-nav-item">
                <a href="{{ route('operator.reservations.index') }}"
                   class="operator-nav-link {{ request()->routeIs('operator.reservations.*') ? 'active' : '' }}">
                    <i class="fas fa-ticket-alt"></i>
                    Réservations
                    @php
                        $pendingReservations = Auth::guard('operator')->user()->managedReservations()->pending()->count();
                    @endphp
                    @if($pendingReservations > 0)
                        <span class="badge bg-danger ms-2">{{ $pendingReservations }}</span>
                    @endif
                </a>
            </li>
            @endif

            <li class="operator-nav-item">
                <a href="{{ route('operator.reports.dashboard') }}"
                   class="operator-nav-link {{ request()->routeIs('operator.reports.*') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i>
                    Rapports
                </a>
            </li>

            @if(Auth::guard('operator')->user()->canManageProfile())
            <li class="operator-nav-item">
                <a href="{{ route('operator.profile.show') }}"
                   class="operator-nav-link {{ request()->routeIs('operator.profile.*') ? 'active' : '' }}">
                    <i class="fas fa-user-cog"></i>
                    Profil
                </a>
            </li>

            <li class="operator-nav-item">
                <a href="{{ route('operator.tour-operator.show') }}"
                   class="operator-nav-link {{ request()->routeIs('operator.tour-operator.*') ? 'active' : '' }}">
                    <i class="fas fa-building"></i>
                    Mon Entreprise
                </a>
            </li>
            @endif
        </ul>

        <div class="mt-auto p-3">
            <form method="POST" action="{{ route('operator.logout') }}">
                @csrf
                <button type="submit" class="operator-nav-link w-100 text-start border-0 bg-transparent">
                    <i class="fas fa-sign-out-alt"></i>
                    Déconnexion
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content -->
    <div class="operator-main-content">
        <!-- Topbar -->
        <div class="operator-topbar d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn d-md-none me-3" type="button" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="operator-topbar-title">@yield('page-title', 'Dashboard')</h1>
            </div>

            <div class="operator-user-menu">
                <div class="dropdown">
                    <button class="dropdown-toggle border-0 bg-transparent"
                            type="button"
                            id="userDropdown"
                            data-bs-toggle="dropdown"
                            aria-expanded="false">
                        <i class="fas fa-user-circle fa-lg me-2"></i>
                        {{ Auth::guard('operator')->user()->name }}
                        <i class="fas fa-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                        <li class="dropdown-header">
                            <strong>{{ Auth::guard('operator')->user()->tourOperator->getTranslatedName(session('locale', 'fr')) }}</strong>
                            <br>
                            <small class="text-muted">{{ Auth::guard('operator')->user()->position ?? 'Tour Operator' }}</small>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('operator.profile.show') }}">
                                <i class="fas fa-user me-2"></i>
                                Mon Profil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('operator.tour-operator.show') }}">
                                <i class="fas fa-building me-2"></i>
                                Mon Entreprise
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('operator.logout') }}" class="d-inline">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt me-2"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="operator-content">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="operator-alert alert-success mb-4 operator-fade-in">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="operator-alert alert-danger mb-4 operator-fade-in">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="operator-alert alert alert-warning mb-4 operator-fade-in">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="operator-alert alert-danger mb-4 operator-fade-in">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Erreurs détectées :</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Breadcrumb -->
            @if(isset($breadcrumbs))
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{ route('operator.dashboard') }}">
                                <i class="fas fa-home"></i>
                                Dashboard
                            </a>
                        </li>
                        @foreach($breadcrumbs as $breadcrumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ $breadcrumb['title'] }}
                                </li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $breadcrumb['url'] }}">{{ $breadcrumb['title'] }}</a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>
            @endif

            <!-- Page Content -->
            @yield('content')
        </div>
    </div>

    @vite(['resources/js/app.js'])

    <!-- Custom JavaScript -->
    <script>
        // Sidebar toggle for mobile
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.operator-alert').forEach(function(alert) {
                let bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Add loading state to buttons on form submit
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Traitement...';

                    // Re-enable after 10 seconds as fallback
                    setTimeout(function() {
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }, 10000);
                }
            });
        });

        // Confirm dialogs for delete actions
        document.querySelectorAll('.btn-danger, .text-danger').forEach(function(element) {
            if (element.textContent.includes('Supprimer') || element.textContent.includes('Annuler')) {
                element.addEventListener('click', function(e) {
                    if (!confirm('Êtes-vous sûr de vouloir effectuer cette action ?')) {
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>