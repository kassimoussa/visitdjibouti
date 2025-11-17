@extends('operator.layouts.app')

@section('title', 'Mon Entreprise')
@section('page-title', 'Mon Entreprise')

@section('content')
<div class="fade-in">
    <!-- Messages flash -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h2 class="mb-1">{{ $tourOperator->getTranslatedName(session('locale', 'fr')) }}</h2>
            <p class="text-muted mb-0">Informations et paramètres de votre entreprise</p>
        </div>
        <a href="{{ route('operator.tour-operator.edit') }}" class="btn btn-primary">
            <i class="fas fa-edit me-2"></i>Modifier les informations
        </a>
    </div>

    <div class="row">
        <!-- Company Information -->
        <div class="col-lg-8">
            <!-- Basic Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-building me-2"></i>
                        Informations Générales
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Logo and Name -->
                        <div class="col-12 mb-4">
                            <div class="d-flex align-items-center">
                                @if($tourOperator->logo)
                                    <img src="{{ asset($tourOperator->logo->path) }}"
                                         alt="Logo {{ $tourOperator->getTranslatedName(session('locale', 'fr')) }}"
                                         class="me-3 rounded"
                                         style="width: 100px; height: 100px; object-fit: contain; border: 1px solid #dee2e6;">
                                @else
                                    <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                         style="width: 100px; height: 100px; border: 1px solid #dee2e6;">
                                        <i class="fas fa-building fa-2x text-muted"></i>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="mb-1">{{ $tourOperator->getTranslatedName(session('locale', 'fr')) }}</h4>
                                    @if($tourOperator->business_license)
                                        <div class="mb-2">
                                            <span class="badge bg-success">
                                                <i class="fas fa-certificate me-1"></i>
                                                Licence: {{ $tourOperator->business_license }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="d-flex align-items-center gap-3">
                                        <span class="badge status-{{ $tourOperator->is_active ? 'published' : 'cancelled' }}">
                                            {{ $tourOperator->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                        @if($tourOperator->certification_level)
                                            <span class="badge bg-warning">
                                                <i class="fas fa-star me-1"></i>
                                                {{ ucfirst($tourOperator->certification_level) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        @if($tourOperator->getTranslatedDescription(session('locale', 'fr')))
                            <div class="col-12 mb-4">
                                <h6><i class="fas fa-align-left me-2 text-primary"></i>Description</h6>
                                <div class="bg-light p-3 rounded">
                                    {{ $tourOperator->getTranslatedDescription(session('locale', 'fr')) }}
                                </div>
                            </div>
                        @endif

                        <!-- Contact Information -->
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-envelope me-2 text-primary"></i>Contact</h6>
                            @if($tourOperator->email)
                                <div class="mb-2">
                                    <a href="mailto:{{ $tourOperator->email }}" class="text-decoration-none">
                                        <i class="fas fa-envelope me-1"></i>
                                        {{ $tourOperator->email }}
                                    </a>
                                </div>
                            @endif
                            @if($tourOperator->phone)
                                <div class="mb-2">
                                    <a href="tel:{{ $tourOperator->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $tourOperator->phone }}
                                    </a>
                                </div>
                            @endif
                            @if($tourOperator->whatsapp)
                                <div class="mb-2">
                                    <a href="https://wa.me/{{ $tourOperator->whatsapp }}" class="text-decoration-none text-success" target="_blank">
                                        <i class="fab fa-whatsapp me-1"></i>
                                        {{ $tourOperator->whatsapp }}
                                    </a>
                                </div>
                            @endif
                        </div>

                        <!-- Address -->
                        <div class="col-md-6 mb-3">
                            <h6><i class="fas fa-map-marker-alt me-2 text-primary"></i>Adresse</h6>
                            @if($tourOperator->address)
                                <div class="mb-2">{{ $tourOperator->address }}</div>
                            @endif
                            @if($tourOperator->region)
                                <div class="mb-2">
                                    <span class="badge bg-secondary">{{ $tourOperator->region }}</span>
                                </div>
                            @endif
                            @if($tourOperator->latitude && $tourOperator->longitude)
                                <div>
                                    <a href="https://maps.google.com/?q={{ $tourOperator->latitude }},{{ $tourOperator->longitude }}"
                                       target="_blank"
                                       class="text-decoration-none">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        Voir sur Google Maps
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Services Offered -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-concierge-bell me-2"></i>
                        Services Proposés
                    </h5>
                </div>
                <div class="card-body">
                    @if($tourOperator->services_offered && count($tourOperator->services_offered) > 0)
                        <div class="row">
                            @foreach($tourOperator->services_offered as $service)
                                <div class="col-md-4 mb-3">
                                    <div class="d-flex align-items-center p-3 bg-light rounded">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        <span>{{ ucfirst($service) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-info-circle text-muted fa-2x mb-2"></i>
                            <p class="text-muted mb-0">Aucun service spécifié</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Languages Spoken -->
            @if($tourOperator->languages_spoken && count($tourOperator->languages_spoken) > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-language me-2"></i>
                            Langues Parlées
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($tourOperator->languages_spoken as $language)
                                <span class="badge bg-info fs-6">{{ ucfirst($language) }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Operating Hours -->
            @if($tourOperator->operating_hours)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-clock me-2"></i>
                            Heures d'Ouverture
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="bg-light p-3 rounded">
                            <pre class="mb-0">{{ $tourOperator->operating_hours }}</pre>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Stats -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-line me-2"></i>
                        Statistiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="h3 text-primary mb-1">{{ $statistics['total_tours'] ?? 0 }}</div>
                            <small class="text-muted">Tours</small>
                        </div>
                        <div class="col-6">
                            <div class="h3 text-warning mb-1">{{ $statistics['total_activities'] ?? 0 }}</div>
                            <small class="text-muted">Activités</small>
                        </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="h3 text-success mb-1">{{ $statistics['total_reservations'] ?? 0 }}</div>
                            <small class="text-muted">Réservations</small>
                        </div>
                        <div class="col-6">
                            <div class="h3 text-info mb-1">{{ $tourOperator->users()->count() }}</div>
                            <small class="text-muted">Employés</small>
                        </div>
                    </div>
                    <hr>
                    <div class="text-center">
                        <div class="h4 text-success mb-1">{{ number_format($statistics['total_revenue'], 0, ',', ' ') }} DJF</div>
                        <small class="text-muted">Revenus Total</small>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            @if($tourOperator->website || $tourOperator->facebook || $tourOperator->instagram)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-share-alt me-2"></i>
                            Présence en Ligne
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            @if($tourOperator->website)
                                <a href="{{ $tourOperator->website }}"
                                   target="_blank"
                                   class="btn btn-outline-primary w-100">
                                    <i class="fas fa-globe me-2"></i>
                                    Site Web
                                </a>
                            @endif
                            @if($tourOperator->facebook)
                                <a href="{{ $tourOperator->facebook }}"
                                   target="_blank"
                                   class="btn btn-outline-primary w-100">
                                    <i class="fab fa-facebook me-2"></i>
                                    Facebook
                                </a>
                            @endif
                            @if($tourOperator->instagram)
                                <a href="{{ $tourOperator->instagram }}"
                                   target="_blank"
                                   class="btn btn-outline-primary w-100">
                                    <i class="fab fa-instagram me-2"></i>
                                    Instagram
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Team Members -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-users me-2"></i>
                        Équipe
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $teamMembers = $tourOperator->users()->orderBy('name')->get();
                    @endphp
                    @if($teamMembers->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($teamMembers as $member)
                                <div class="list-group-item px-0 py-2">
                                    <div class="d-flex align-items-center">
                                        @if($member->avatar)
                                            <img src="{{ Storage::url($member->avatar) }}"
                                                 alt="{{ $member->name }}"
                                                 class="rounded-circle me-3"
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $member->name }}</h6>
                                            @if($member->position)
                                                <small class="text-muted">{{ $member->position }}</small>
                                            @endif
                                        </div>
                                        @if($member->is_active)
                                            <i class="fas fa-circle text-success" title="Actif"></i>
                                        @else
                                            <i class="fas fa-circle text-muted" title="Inactif"></i>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-3">
                            <i class="fas fa-users fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-0">Aucun membre d'équipe</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Account Status -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-info-circle me-2"></i>
                        Statut du Compte
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Statut d'activité</strong>
                        <br>
                        @if($tourOperator->is_active)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Date de création</strong>
                        <br>
                        <span class="text-muted">{{ $tourOperator->created_at->format('d/m/Y') }}</span>
                    </div>

                    @if($tourOperator->certification_level)
                        <div class="mb-3">
                            <strong>Niveau de certification</strong>
                            <br>
                            <span class="badge bg-warning">{{ ucfirst($tourOperator->certification_level) }}</span>
                        </div>
                    @endif

                    @if($tourOperator->business_license)
                        <div class="mb-3">
                            <strong>Licence commerciale</strong>
                            <br>
                            <code>{{ $tourOperator->business_license }}</code>
                        </div>
                    @endif

                    <div class="mb-0">
                        <strong>Dernière mise à jour</strong>
                        <br>
                        <span class="text-muted">{{ $tourOperator->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection