@extends('layouts.admin')

@section('title', $tour->title)
@section('page-title', 'Détails du Tour')

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('tours.index') }}">Tours</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($tour->title, 50) }}</li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-2">{{ $tour->title }}</h2>
            <div class="d-flex gap-2 align-items-center flex-wrap">
                @php
                    $statusColors = [
                        'draft' => 'secondary',
                        'pending_approval' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        'active' => 'success',
                        'inactive' => 'secondary',
                    ];
                    $statusLabels = [
                        'draft' => 'Brouillon',
                        'pending_approval' => 'En attente d\'approbation',
                        'approved' => 'Approuvé',
                        'rejected' => 'Rejeté',
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                    ];
                @endphp
                <span class="badge bg-{{ $statusColors[$tour->status] ?? 'secondary' }} fs-6 px-3 py-2">
                    {{ $statusLabels[$tour->status] ?? ucfirst($tour->status) }}
                </span>

                @if($tour->created_by_operator_user_id)
                    <!-- Operator info badge -->
                    <span class="badge bg-secondary fs-6 px-3 py-2">
                        <i class="fas fa-building me-1"></i>
                        {{ $tour->tourOperator->name ?? 'N/A' }}
                    </span>

                    <!-- Workflow status badge for rejected tours only -->
                    @if($tour->status === 'rejected' && $tour->rejection_reason)
                        <span class="badge bg-danger fs-6 px-3 py-2"
                              data-bs-toggle="tooltip"
                              title="{{ $tour->rejection_reason }}">
                            <i class="fas fa-exclamation-circle me-1"></i>
                            Raison du rejet
                        </span>
                    @endif
                @endif

                @if($tour->is_featured)
                    <span class="badge bg-warning text-dark fs-6 px-3 py-2">
                        <i class="fas fa-star me-1"></i>Mis en avant
                    </span>
                @endif

                <span class="badge bg-{{ $tour->difficulty_level === 'easy' ? 'success' : ($tour->difficulty_level === 'moderate' ? 'warning' : ($tour->difficulty_level === 'difficult' ? 'danger' : 'dark')) }}">
                    {{ $tour->difficulty_label }}
                </span>

                @if($tour->weather_dependent)
                    <span class="badge bg-info">
                        <i class="fas fa-cloud-sun"></i> Dépend de la météo
                    </span>
                @endif

                <small class="text-muted">
                    <i class="fas fa-eye me-1"></i>{{ number_format($tour->views_count) }} vues
                </small>
            </div>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
            <a href="{{ route('tours.edit', $tour) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            @if($tour->status === 'pending_approval')
                <a href="{{ route('tours.approvals') }}" class="btn btn-success">
                    <i class="fas fa-check me-2"></i>Gérer l'Approbation
                </a>
            @endif
            <button type="button" class="btn btn-outline-danger"
                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce tour ?')) { document.getElementById('delete-form').submit(); }">
                <i class="fas fa-trash me-2"></i>Supprimer
            </button>
            <form id="delete-form" action="{{ route('tours.destroy', $tour) }}" method="POST" style="display: none;">
                @csrf
                @method('DELETE')
            </form>
        </div>
    </div>

    @php
        $translationFr = $tour->translations->where('locale', 'fr')->first();
        $translationEn = $tour->translations->where('locale', 'en')->first();
    @endphp

    <div class="row">
        <!-- Colonne principale -->
        <div class="col-md-8">
            <!-- Image principale -->
            @if($tour->featuredImage)
            <div class="card mb-4">
                <div class="card-body p-0">
                    <img src="{{ asset($tour->featuredImage->path) }}"
                         alt="{{ $tour->title }}"
                         class="img-fluid w-100 cursor-pointer"
                         style="max-height: 400px; object-fit: cover;"
                         data-bs-toggle="modal"
                         data-bs-target="#imageModal{{ $tour->featuredImage->id }}">
                </div>
            </div>

            <!-- Modal pour l'image principale -->
            <div class="modal fade" id="imageModal{{ $tour->featuredImage->id }}" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body p-0">
                            <img src="{{ asset($tour->featuredImage->path) }}"
                                 alt="{{ $tour->title }}"
                                 class="img-fluid w-100">
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Onglets de langue pour le contenu -->
            <div class="card mb-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#content-fr" type="button">
                                <i class="fas fa-flag me-1"></i> Français
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#content-en" type="button">
                                <i class="fas fa-flag me-1"></i> English
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content">
                        <!-- Contenu FR -->
                        <div class="tab-pane fade show active" id="content-fr" role="tabpanel">
                            @if($translationFr)
                                <h3 class="mb-3">{{ $translationFr->title }}</h3>

                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-align-left me-2 text-primary"></i>Description</h5>
                                    <div class="text-muted">{!! nl2br(e($translationFr->description)) !!}</div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Aucune traduction française disponible
                                </div>
                            @endif
                        </div>

                        <!-- Contenu EN -->
                        <div class="tab-pane fade" id="content-en" role="tabpanel">
                            @if($translationEn)
                                <h3 class="mb-3">{{ $translationEn->title }}</h3>

                                <div class="mb-4">
                                    <h5 class="mb-3"><i class="fas fa-align-left me-2 text-primary"></i>Description</h5>
                                    <div class="text-muted">{!! nl2br(e($translationEn->description)) !!}</div>
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    No English translation available
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Détails du tour -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations pratiques
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-tag fa-2x text-primary me-3"></i>
                                <div>
                                    <small class="text-muted">Prix</small>
                                    <h5 class="mb-0 text-primary">{{ $tour->formatted_price }}</h5>
                                </div>
                            </div>
                        </div>
                        @if($tour->max_participants)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-users fa-2x text-info me-3"></i>
                                <div>
                                    <small class="text-muted">Participants max</small>
                                    <h5 class="mb-0 text-info">{{ $tour->max_participants }}</h5>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($tour->start_date || $tour->end_date)
                        <div class="col-md-6 mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-calendar fa-2x text-success me-3"></i>
                                <div>
                                    <small class="text-muted">Dates</small>
                                    <h6 class="mb-0">
                                        @if($tour->start_date)
                                            {{ $tour->start_date->format('d/m/Y') }}
                                        @endif
                                        @if($tour->end_date)
                                            - {{ $tour->end_date->format('d/m/Y') }}
                                        @endif
                                    </h6>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    @if($tour->meeting_point_address)
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Point de rendez-vous</h6>
                    <p class="text-muted">{{ $tour->meeting_point_address }}</p>
                    @endif

                    @if($tour->cancellation_policy)
                    <hr>
                    <h6 class="mb-3"><i class="fas fa-ban me-2 text-danger"></i>Politique d'annulation</h6>
                    <p class="text-muted mb-0">{!! nl2br(e($tour->cancellation_policy)) !!}</p>
                    @endif
                </div>
            </div>

            <!-- Galerie d'images -->
            @if($tour->media->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-images me-2"></i>Galerie ({{ $tour->media->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($tour->media as $media)
                            <div class="col-md-4">
                                <img src="{{ asset($media->thumbnail_path ?? $media->path) }}"
                                     alt="{{ $media->original_name ?? 'Image' }}"
                                     class="img-fluid rounded shadow-sm cursor-pointer"
                                     style="width: 100%; height: 150px; object-fit: cover;"
                                     data-bs-toggle="modal"
                                     data-bs-target="#imageModal{{ $media->id }}">
                            </div>

                            <!-- Modal pour chaque image -->
                            <div class="modal fade" id="imageModal{{ $media->id }}" tabindex="-1">
                                <div class="modal-dialog modal-xl modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-body p-0">
                                            <img src="{{ asset($media->path) }}"
                                                 alt="{{ $media->original_name ?? 'Image' }}"
                                                 class="img-fluid w-100">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Opérateur -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary bg-opacity-10">
                    <h6 class="mb-0 text-primary">
                        <i class="fas fa-route me-2"></i>Opérateur de Tour
                    </h6>
                </div>
                <div class="card-body">
                    <h5>{{ $tour->tourOperator->name }}</h5>
                    @if($tour->tourOperator->email)
                        <p class="mb-2">
                            <i class="fas fa-envelope me-2 text-primary"></i>
                            <a href="mailto:{{ $tour->tourOperator->email }}">{{ $tour->tourOperator->email }}</a>
                        </p>
                    @endif
                    @if($tour->tourOperator->phone)
                        <p class="mb-2">
                            <i class="fas fa-phone me-2 text-success"></i>
                            <a href="tel:{{ $tour->tourOperator->phone }}">{{ $tour->tourOperator->phone }}</a>
                        </p>
                    @endif
                    <a href="{{ route('tour-operators.show', $tour->tourOperator->id) }}" class="btn btn-sm btn-outline-primary mt-2 w-100">
                        <i class="fas fa-eye me-2"></i>Voir l'opérateur
                    </a>
                </div>
            </div>

            <!-- Créé par -->
            @if($tour->createdBy)
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-info bg-opacity-10">
                    <h6 class="mb-0 text-info">
                        <i class="fas fa-user-tie me-2"></i>Créé par
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-1"><strong>{{ $tour->createdBy->first_name }} {{ $tour->createdBy->last_name }}</strong></p>
                    <p class="mb-0 text-muted">{{ $tour->createdBy->email }}</p>
                </div>
            </div>
            @endif

            <!-- Statistiques -->
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success bg-opacity-10">
                    <h6 class="mb-0 text-success">
                        <i class="fas fa-chart-bar me-2"></i>Statistiques
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-eye me-2 text-secondary"></i>Vues</span>
                        <strong>{{ number_format($tour->views_count ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-calendar-check me-2 text-primary"></i>Réservations</span>
                        <strong>{{ $tour->reservations_count ?? 0 }}</strong>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary bg-opacity-10">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Dates importantes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Créé le</small>
                        <strong>{{ $tour->created_at->format('d/m/Y à H:i') }}</strong>
                        <br><small class="text-muted">{{ $tour->created_at->diffForHumans() }}</small>
                    </div>
                    <hr>
                    <div>
                        <small class="text-muted d-block mb-1">Modifié le</small>
                        <strong>{{ $tour->updated_at->format('d/m/Y à H:i') }}</strong>
                        <br><small class="text-muted">{{ $tour->updated_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cursor-pointer {
    cursor: pointer;
}
</style>
@endsection