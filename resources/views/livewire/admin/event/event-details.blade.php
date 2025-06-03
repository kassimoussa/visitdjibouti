<div class="modern-event-view">
    <!-- En-tête avec boutons d'action et sélecteur de langue -->
    <div class="container-fluid mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="h3">{{ $event->translation($currentLocale)->title }}</h1>
            <div class="d-flex">
                <!-- Sélecteur de langue -->
                <div class="btn-group me-3" role="group">
                    @foreach ($availableLocales as $locale)
                        <button type="button"
                            class="btn {{ $currentLocale === $locale ? 'btn-primary' : 'btn-outline-primary' }}"
                            wire:click="changeLocale('{{ $locale }}')">
                            {{ strtoupper($locale) }}
                        </button>
                    @endforeach
                </div>

                <a href="{{ route('events.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                <a href="{{ route('events.edit', $event->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <!-- Onglets de navigation -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'details' ? 'active' : '' }}" 
                            wire:click="changeTab('details')" type="button">
                            <i class="fas fa-info-circle me-1"></i>Détails
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'registrations' ? 'active' : '' }}" 
                            wire:click="changeTab('registrations')" type="button">
                            <i class="fas fa-users me-1"></i>Inscriptions 
                            <span class="badge bg-primary ms-1">{{ $detailedStats['total_registrations'] }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $activeTab === 'statistics' ? 'active' : '' }}" 
                            wire:click="changeTab('statistics')" type="button">
                            <i class="fas fa-chart-bar me-1"></i>Statistiques
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Contenu des onglets -->
        @if ($activeTab === 'details')
            <!-- Onglet Détails (contenu existant) -->
        <div class="row mb-4">
            <!-- Image principale (colonne gauche) -->
            <div class="col-lg-9">
                <div class="position-relative">
                    @if ($event->featuredImage)
                        <img src="{{ asset($event->featuredImage->path) }}"
                            alt="{{ $event->featuredImage->getTranslation($currentLocale)->alt_text ?? $event->translation($currentLocale)->title }}"
                            class="w-100" style="max-height: 400px; object-fit: cover;">
                    @else
                        <div class="bg-light text-center py-5" style="height: 400px;">
                            <div class="d-flex flex-column justify-content-center align-items-center h-100">
                                <i class="fas fa-calendar-alt fa-4x text-muted mb-3"></i>
                                <p class="text-muted">Aucune image principale</p>
                            </div>
                        </div>
                    @endif

                    <!-- Statut et badges (positionnés au bas de l'image) -->
                    <div class="mev-badge-container my-3">
                        @if ($event->status === 'published')
                            <span class="mev-badge bg-success rounded-pill px-3 py-2 me-2">Publié</span>
                        @elseif($event->status === 'draft')
                            <span class="mev-badge bg-warning rounded-pill px-3 py-2 me-2">Brouillon</span>
                        @else
                            <span class="mev-badge bg-secondary rounded-pill px-3 py-2 me-2">Archivé</span>
                        @endif

                        <span class="mev-badge {{ $eventStatus['class'] }} rounded-pill px-3 py-2 me-2">
                            <i class="{{ $eventStatus['icon'] }} me-1"></i>{{ $eventStatus['label'] }}
                        </span>

                        @if ($event->is_featured)
                            <span class="mev-badge bg-info rounded-pill px-3 py-2 me-2">À la une</span>
                        @endif

                        @if ($eventStats['is_sold_out'])
                            <span class="mev-badge bg-danger rounded-pill px-3 py-2 me-2">Complet</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informations rapides (colonne droite) -->
            <div class="col-lg-3">
                <div class="mev-card shadow-sm h-100 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Informations rapides</h4>
                        
                        <!-- Dates -->
                        <div class="mev-info-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 mev-info-icon">
                                    <i class="fas fa-calendar-alt text-primary"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-medium">Dates</div>
                                    <div>{{ $formattedDateRange }}</div>
                                    @if ($startTime || $endTime)
                                        <div class="text-muted small">
                                            {{ $startTime ? $startTime : '' }}{{ $endTime ? ' - ' . $endTime : '' }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Prix -->
                        <div class="mev-info-item mb-3">
                            <div class="d-flex">
                                <div class="flex-shrink-0 mev-info-icon">
                                    <i class="fas fa-tag text-success"></i>
                                </div>
                                <div class="ms-3 flex-grow-1">
                                    <div class="fw-medium">Prix</div>
                                    <div class="text-success fw-bold">{{ $formattedPrice }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Participants -->
                        @if ($event->max_participants)
                            <div class="mev-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-users text-info"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Participants</div>
                                        <div>{{ $eventStats['total_participants'] }} / {{ $event->max_participants }}</div>
                                        @if ($eventStats['available_spots'] > 0)
                                            <div class="text-success small">{{ $eventStats['available_spots'] }} places disponibles</div>
                                        @else
                                            <div class="text-danger small">Complet</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="mev-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-users text-info"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Participants</div>
                                        <div>{{ $eventStats['total_participants'] }}</div>
                                        <div class="text-muted small">Nombre illimité</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Organisateur -->
                        @if ($event->organizer)
                            <div class="mev-info-item mb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-user-tie text-warning"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Organisateur</div>
                                        <div>{{ $event->organizer }}</div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Catégories -->
                        <div class="mev-info-item">
                            <h6 class="fw-medium mb-2">Catégories</h6>
                            <div class="d-flex flex-wrap gap-1">
                                @if ($event->categories->isNotEmpty())
                                    @foreach ($event->categories as $category)
                                        <span class="badge rounded-pill" 
                                            style="background-color: {{ $category->color ?? '#6c757d' }}; color: white;">
                                            <i class="{{ $category->icon ?? 'fas fa-folder' }} me-1"></i>
                                            {{ $category->translation($currentLocale)?->name ?: $category->translation('fr')->name }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted">Aucune catégorie</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        @elseif ($activeTab === 'registrations')
            <!-- Onglet Inscriptions -->
            <div class="row">
                <div class="col-12">
                    <!-- Résumé des inscriptions -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card text-center border-success">
                                <div class="card-body">
                                    <div class="h2 text-success mb-1">{{ $detailedStats['confirmed_registrations'] }}</div>
                                    <div class="text-muted">Confirmées</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-warning">
                                <div class="card-body">
                                    <div class="h2 text-warning mb-1">{{ $detailedStats['pending_registrations'] }}</div>
                                    <div class="text-muted">En attente</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-danger">
                                <div class="card-body">
                                    <div class="h2 text-danger mb-1">{{ $detailedStats['cancelled_registrations'] }}</div>
                                    <div class="text-muted">Annulées</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card text-center border-info">
                                <div class="card-body">
                                    <div class="h2 text-info mb-1">{{ $detailedStats['total_participants'] }}</div>
                                    <div class="text-muted">Participants</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Inscriptions confirmées -->
                    @if ($groupedRegistrations['confirmed']->isNotEmpty())
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Inscriptions confirmées ({{ $groupedRegistrations['confirmed']->count() }})</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Numéro</th>
                                                <th>Participant</th>
                                                <th>Email</th>
                                                <th>Téléphone</th>
                                                <th>Nb. participants</th>
                                                <th>Paiement</th>
                                                <th>Date d'inscription</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedRegistrations['confirmed'] as $registration)
                                                <tr>
                                                    <td><span class="badge bg-secondary">{{ $registration->registration_number }}</span></td>
                                                    <td>
                                                        <div class="fw-medium">{{ $registration->user_name }}</div>
                                                        @if ($registration->user)
                                                            <small class="text-muted">Utilisateur inscrit</small>
                                                        @else
                                                            <small class="text-muted">Invité</small>
                                                        @endif
                                                    </td>
                                                    <td>{{ $registration->user_email }}</td>
                                                    <td>{{ $registration->user_phone ?? '-' }}</td>
                                                    <td class="text-center">{{ $registration->participants_count }}</td>
                                                    <td>
                                                        @if ($registration->payment_amount)
                                                            <span class="badge bg-{{ $registration->payment_status === 'paid' ? 'success' : 'warning' }}">
                                                                {{ number_format($registration->payment_amount, 0, ',', ' ') }} DJF
                                                            </span>
                                                            <div class="small text-muted">{{ ucfirst($registration->payment_status) }}</div>
                                                        @else
                                                            <span class="badge bg-info">Gratuit</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Inscriptions en attente -->
                    @if ($groupedRegistrations['pending']->isNotEmpty())
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Inscriptions en attente ({{ $groupedRegistrations['pending']->count() }})</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Numéro</th>
                                                <th>Participant</th>
                                                <th>Email</th>
                                                <th>Nb. participants</th>
                                                <th>Montant</th>
                                                <th>Date d'inscription</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedRegistrations['pending'] as $registration)
                                                <tr>
                                                    <td><span class="badge bg-secondary">{{ $registration->registration_number }}</span></td>
                                                    <td>{{ $registration->user_name }}</td>
                                                    <td>{{ $registration->user_email }}</td>
                                                    <td class="text-center">{{ $registration->participants_count }}</td>
                                                    <td>
                                                        @if ($registration->payment_amount)
                                                            {{ number_format($registration->payment_amount, 0, ',', ' ') }} DJF
                                                        @else
                                                            Gratuit
                                                        @endif
                                                    </td>
                                                    <td>{{ $registration->created_at->format('d/m/Y H:i') }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Inscriptions annulées -->
                    @if ($groupedRegistrations['cancelled']->isNotEmpty())
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-danger text-white">
                                <h5 class="mb-0"><i class="fas fa-times-circle me-2"></i>Inscriptions annulées ({{ $groupedRegistrations['cancelled']->count() }})</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Numéro</th>
                                                <th>Participant</th>
                                                <th>Email</th>
                                                <th>Raison</th>
                                                <th>Date d'annulation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($groupedRegistrations['cancelled'] as $registration)
                                                <tr>
                                                    <td><span class="badge bg-secondary">{{ $registration->registration_number }}</span></td>
                                                    <td>{{ $registration->user_name }}</td>
                                                    <td>{{ $registration->user_email }}</td>
                                                    <td>{{ $registration->cancellation_reason ?? '-' }}</td>
                                                    <td>{{ $registration->cancelled_at ? $registration->cancelled_at->format('d/m/Y H:i') : '-' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($detailedStats['total_registrations'] === 0)
                        <div class="card shadow-sm">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucune inscription pour cet événement</h5>
                                <p class="text-muted">Les inscriptions apparaîtront ici une fois que les utilisateurs commenceront à s'inscrire.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

        @elseif ($activeTab === 'statistics')
            <!-- Onglet Statistiques -->
            <div class="row">
                <!-- Statistiques financières -->
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-euro-sign me-2"></i>Statistiques financières</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                        <div class="h4 mb-1 text-success">{{ number_format($detailedStats['total_revenue'], 0, ',', ' ') }} DJF</div>
                                        <div class="small text-muted">Revenus confirmés</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                        <div class="h4 mb-1 text-warning">{{ number_format($detailedStats['pending_payments'], 0, ',', ' ') }} DJF</div>
                                        <div class="small text-muted">Paiements en attente</div>
                                    </div>
                                </div>
                            </div>
                            
                            @if ($event->price)
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span>Prix unitaire :</span>
                                    <strong>{{ number_format($event->price, 0, ',', ' ') }} DJF</strong>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Revenus potentiels max :</span>
                                    <strong>{{ number_format(($event->max_participants ?? 0) * $event->price, 0, ',', ' ') }} DJF</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistiques de participation -->
                <div class="col-md-6">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Taux de participation</h5>
                        </div>
                        <div class="card-body">
                            @if ($event->max_participants)
                                @php
                                    $fillRate = ($detailedStats['total_participants'] / $event->max_participants) * 100;
                                @endphp
                                <div class="text-center mb-3">
                                    <div class="h2 mb-1">{{ round($fillRate, 1) }}%</div>
                                    <div class="text-muted">Taux de remplissage</div>
                                </div>
                                
                                <div class="progress mb-3" style="height: 20px;">
                                    <div class="progress-bar" role="progressbar" style="width: {{ $fillRate }}%" 
                                        aria-valuenow="{{ $fillRate }}" aria-valuemin="0" aria-valuemax="100">
                                        {{ $detailedStats['total_participants'] }} / {{ $event->max_participants }}
                                    </div>
                                </div>
                            @else
                                <div class="text-center mb-3">
                                    <div class="h2 mb-1">{{ $detailedStats['total_participants'] }}</div>
                                    <div class="text-muted">Participants (illimité)</div>
                                </div>
                            @endif

                            <div class="row g-2">
                                <div class="col-4 text-center">
                                    <div class="small text-success">{{ $detailedStats['confirmed_registrations'] }}</div>
                                    <div class="small text-muted">Confirmées</div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="small text-warning">{{ $detailedStats['pending_registrations'] }}</div>
                                    <div class="small text-muted">En attente</div>
                                </div>
                                <div class="col-4 text-center">
                                    <div class="small text-danger">{{ $detailedStats['cancelled_registrations'] }}</div>
                                    <div class="small text-muted">Annulées</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Évolution des inscriptions (placeholder pour futur graphique) -->
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Évolution des inscriptions</h5>
                        </div>
                        <div class="card-body">
                            <div class="text-center py-5 text-muted">
                                <i class="fas fa-chart-line fa-3x mb-3"></i>
                                <p>Graphique d'évolution des inscriptions</p>
                                <small>À implémenter : graphique montrant l'évolution des inscriptions dans le temps</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <!-- Colonne principale -->
            <div class="col-lg-8">
                <!-- Description -->
                <div class="mev-card shadow-sm mb-4 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Description</h4>

                        @if ($event->translation($currentLocale)->short_description)
                            <div class="alert alert-light border-start border-4 border-primary mb-4">
                                <div class="fw-medium">{{ $event->translation($currentLocale)->short_description }}</div>
                            </div>
                        @endif

                        <div class="mev-description">
                            {!! nl2br(e($event->translation($currentLocale)->description)) !!}
                        </div>
                    </div>
                </div>

                <!-- Programme -->
                @if ($event->translation($currentLocale)->program)
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">
                                <i class="fas fa-list-ul text-primary me-2"></i>Programme
                            </h4>
                            <div class="mev-program-content">
                                {!! nl2br(e($event->translation($currentLocale)->program)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Prérequis -->
                @if ($event->translation($currentLocale)->requirements)
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">
                                <i class="fas fa-exclamation-triangle text-warning me-2"></i>Prérequis
                            </h4>
                            <div class="mev-requirements-content">
                                {!! nl2br(e($event->translation($currentLocale)->requirements)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Galerie d'images -->
                @if ($event->media->isNotEmpty())
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">Galerie photos</h4>

                            <div class="row g-3 mev-gallery">
                                @foreach ($event->media as $mediaItem)
                                    <div class="col-md-3 col-6">
                                        <a href="{{ asset($mediaItem->path) }}" class="glightbox"
                                            data-gallery="event-gallery">
                                            <div class="mev-gallery-item">
                                                <img src="{{ asset($mediaItem->path) }}"
                                                    alt="{{ $mediaItem->getTranslation($currentLocale)->title ?? $mediaItem->original_name }}"
                                                    class="img-fluid">
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Informations additionnelles -->
                @if ($event->translation($currentLocale)->additional_info)
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">
                                <i class="fas fa-info-circle text-info me-2"></i>Informations additionnelles
                            </h4>
                            <div class="mev-additional-info-content">
                                {!! nl2br(e($event->translation($currentLocale)->additional_info)) !!}
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Colonne latérale -->
            <div class="col-lg-4">
                <!-- Statistiques -->
                <div class="mev-card shadow-sm mb-4 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Statistiques</h4>

                        <div class="row g-3">
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-1 text-primary">{{ $eventStats['confirmed_registrations'] }}</div>
                                    <div class="small text-muted">Inscriptions confirmées</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-1 text-warning">{{ $eventStats['pending_registrations'] }}</div>
                                    <div class="small text-muted">En attente</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    <div class="h4 mb-1 text-success">{{ $eventStats['approved_reviews'] }}</div>
                                    <div class="small text-muted">Avis approuvés</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-3 bg-light rounded">
                                    @if ($eventStats['average_rating'])
                                        <div class="h4 mb-1 text-info">{{ $eventStats['average_rating'] }}/5</div>
                                        <div class="small text-muted">Note moyenne</div>
                                    @else
                                        <div class="h4 mb-1 text-muted">-</div>
                                        <div class="small text-muted">Pas d'avis</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Localisation -->
                @if ($event->location || ($event->latitude && $event->longitude))
                    <div class="mev-card shadow-sm mb-4 border-0">
                        <div class="mev-card-body p-4">
                            <h4 class="mev-card-title border-bottom pb-3 mb-3">Localisation</h4>

                            <div class="mev-info-list mb-3">
                                @if ($event->location)
                                    <div class="mev-info-item mb-2">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 mev-info-icon">
                                                <i class="fas fa-map-marker-alt"></i>
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <div class="fw-medium">Lieu</div>
                                                <div>{{ $event->location }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($event->translation($currentLocale)->location_details)
                                    <div class="mev-info-item">
                                        <div class="d-flex">
                                            <div class="flex-shrink-0 mev-info-icon">
                                                <i class="fas fa-info-circle"></i>
                                            </div>
                                            <div class="ms-3 flex-grow-1">
                                                <div class="fw-medium">Détails</div>
                                                <div>{{ $event->translation($currentLocale)->location_details }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if ($event->latitude && $event->longitude)
                                <div wire:ignore>
                                    <div id="mev-map" class="rounded" style="height: 250px;"></div>
                                </div>

                                <div class="d-flex justify-content-between mt-3">
                                    <small class="text-muted">
                                        Latitude: {{ $event->latitude }}
                                    </small>
                                    <small class="text-muted">
                                        Longitude: {{ $event->longitude }}
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                <!-- Contact -->
                <div class="mev-card shadow-sm mb-4 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Contact et liens</h4>

                        <div class="mev-info-list">
                            @if ($event->contact_email)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-envelope"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Email</div>
                                            <div><a href="mailto:{{ $event->contact_email }}" class="text-decoration-none">{{ $event->contact_email }}</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->contact_phone)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-phone"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Téléphone</div>
                                            <div><a href="tel:{{ $event->contact_phone }}" class="text-decoration-none">{{ $event->contact_phone }}</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->website_url)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-globe"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Site web</div>
                                            <div><a href="{{ $event->website_url }}" target="_blank" class="text-decoration-none">{{ $event->website_url }}</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->ticket_url)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Billetterie</div>
                                            <div><a href="{{ $event->ticket_url }}" target="_blank" class="btn btn-sm btn-primary">Acheter des billets</a></div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informations sur la publication -->
                <div class="mev-card shadow-sm mb-4 border-0">
                    <div class="mev-card-body p-4">
                        <h4 class="mev-card-title border-bottom pb-3 mb-3">Détails de publication</h4>

                        <div class="mev-info-list">
                            <div class="mev-info-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-calendar-plus"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Créé le</div>
                                        <div>{{ $event->created_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mev-info-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 mev-info-icon">
                                        <i class="fas fa-edit"></i>
                                    </div>
                                    <div class="ms-3 flex-grow-1">
                                        <div class="fw-medium">Dernière mise à jour</div>
                                        <div>{{ $event->updated_at->format('d/m/Y à H:i') }}</div>
                                    </div>
                                </div>
                            </div>

                            @if ($event->creator)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Créé par</div>
                                            <div>{{ $event->creator->name }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($event->views_count)
                                <div class="mev-info-item">
                                    <div class="d-flex">
                                        <div class="flex-shrink-0 mev-info-icon">
                                            <i class="fas fa-eye"></i>
                                        </div>
                                        <div class="ms-3 flex-grow-1">
                                            <div class="fw-medium">Vues</div>
                                            <div>{{ number_format($event->views_count) }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    @if ($event->latitude && $event->longitude)
        <script>
            document.addEventListener('livewire:init', function() {
                // Déclaration des variables en portée globale
                let map = null;
                let marker = null;

                function createMap() {
                    // Coordonnées de l'événement
                    const lat = {{ $event->latitude }};
                    const lng = {{ $event->longitude }};
                    const name = "{{ $event->translation($currentLocale)->title }}";

                    // Détruire la carte existante si nécessaire
                    if (map) {
                        map.remove();
                        map = null;
                    }

                    // Créer une nouvelle carte
                    map = L.map('mev-map').setView([lat, lng], 12);

                    // Ajouter les tuiles OpenStreetMap
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    }).addTo(map);

                    // Ajouter un marqueur
                    marker = L.marker([lat, lng]).addTo(map)
                        .bindPopup(`<strong>${name}</strong>`)
                        .openPopup();

                    // S'assurer que la carte s'affiche correctement
                    setTimeout(() => map.invalidateSize(), 100);
                }

                // Initialiser la carte au chargement
                createMap();

                // Recréer la carte lorsque Livewire termine une mise à jour
                Livewire.on('event-locale-updated', () => {
                    setTimeout(createMap, 100);
                });
            });
        </script>
    @endif

    <script>
        document.addEventListener('livewire:init', function() {
            // Variable pour stocker l'instance de GLightbox
            let lightbox = null;

            function initLightbox() {
                // Détruire l'instance existante si elle existe
                if (lightbox) {
                    lightbox.destroy();
                    lightbox = null;
                }

                // Créer une nouvelle instance
                lightbox = GLightbox({
                    selector: '.glightbox',
                    touchNavigation: true,
                    loop: true,
                    preload: false,
                    zoomable: true,
                    draggable: true,
                    openEffect: 'zoom',
                    closeEffect: 'fade',
                    showTitle: false
                });
            }

            // Initialiser GLightbox au chargement
            initLightbox();

            // Réinitialiser GLightbox lorsque Livewire termine une mise à jour
            Livewire.on('event-locale-updated', () => {
                setTimeout(initLightbox, 100);
            });
        });
    </script>
@endpush