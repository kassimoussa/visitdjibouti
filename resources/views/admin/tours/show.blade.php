@extends('layouts.admin')

@section('title', 'Détails du Tour')
@section('page-title', 'Détails du Tour')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-start mb-4">
            <div>
                <h1 class="h3 mb-2">{{ $tour->title }}</h1>
                <div class="d-flex align-items-center gap-2">
                    {!! $tour->status_badge !!}
                    @if($tour->is_featured)
                        <span class="badge bg-warning"><i class="fas fa-star me-1"></i>Mis en avant</span>
                    @endif
                    <small class="text-muted">
                        <i class="fas fa-eye me-1"></i>{{ number_format($tour->views_count) }} vues
                    </small>
                </div>
            </div>
            <div class="btn-group">
                <a href="{{ route('tours.edit', $tour->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                @if($tour->status === 'pending_approval')
                    <a href="{{ route('tours.approvals') }}" class="btn btn-success">
                        <i class="fas fa-check me-1"></i> Gérer l'Approbation
                    </a>
                @endif
                <a href="{{ route('tours.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>
        </div>

        <!-- Workflow Info if operator-created -->
        @if($tour->created_by_operator_user_id)
        <div class="alert alert-{{ $tour->status === 'approved' ? 'success' : ($tour->status === 'rejected' ? 'danger' : 'warning') }} mb-4">
            <h5 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informations du Workflow d'Approbation</h5>
            <hr>
            <div class="row">
                <div class="col-md-3">
                    <strong>Créé par:</strong><br>
                    {{ $tour->createdBy->name ?? 'N/A' }}<br>
                    <small class="text-muted">{{ $tour->createdBy->email ?? '' }}</small>
                </div>
                <div class="col-md-3">
                    <strong>Tour Operator:</strong><br>
                    {{ $tour->tourOperator->name ?? 'N/A' }}
                </div>
                @if($tour->submitted_at)
                <div class="col-md-3">
                    <strong>Soumis le:</strong><br>
                    {{ $tour->submitted_at->format('d/m/Y à H:i') }}
                </div>
                @endif
                @if($tour->approved_at)
                <div class="col-md-3">
                    <strong>Approuvé le:</strong><br>
                    {{ $tour->approved_at->format('d/m/Y à H:i') }}<br>
                    <small class="text-muted">Par: {{ $tour->approvedBy->name ?? 'N/A' }}</small>
                </div>
                @endif
            </div>
            @if($tour->rejection_reason)
                <hr>
                <strong>Raison du rejet:</strong>
                <p class="mb-0">{{ $tour->rejection_reason }}</p>
            @endif
        </div>
        @endif

        <div class="row">
            <!-- Informations principales -->
            <div class="col-lg-8">
                <!-- Image principale -->
                @if($tour->featuredImage)
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-0">
                        <img src="{{ $tour->featuredImage->getImageUrl() }}"
                             alt="{{ $tour->title }}"
                             class="img-fluid rounded"
                             style="width: 100%; height: 300px; object-fit: cover;">
                    </div>
                </div>
                @endif

                <!-- Galerie d'images -->
                @if($tour->media->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Galerie ({{ $tour->media->count() }} images)</h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($tour->media as $media)
                                <div class="col-md-3">
                                    <img src="{{ $media->getImageUrl() }}"
                                         alt="{{ $media->translation('fr')->alt_text ?? $tour->title }}"
                                         class="img-fluid rounded"
                                         style="width: 100%; height: 120px; object-fit: cover;">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations générales</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Opérateur:</dt>
                                    <dd class="col-sm-8">{{ $tour->tourOperator->name }}</dd>

                                    <dt class="col-sm-4">Type:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge bg-info">{{ $tour->type_label }}</span>
                                    </dd>

                                    <dt class="col-sm-4">Difficulté:</dt>
                                    <dd class="col-sm-8">
                                        <span class="badge
                                            @if($tour->difficulty_level === 'easy') bg-success
                                            @elseif($tour->difficulty_level === 'moderate') bg-warning
                                            @elseif($tour->difficulty_level === 'difficult') bg-danger
                                            @else bg-dark @endif">
                                            {{ $tour->difficulty_label }}
                                        </span>
                                    </dd>

                                    <dt class="col-sm-4">Prix:</dt>
                                    <dd class="col-sm-8">{{ $tour->formatted_price }}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                <dl class="row">
                                    <dt class="col-sm-4">Dates:</dt>
                                    <dd class="col-sm-8">{{ $tour->formatted_date_range }}</dd>

                                    <dt class="col-sm-4">Durée:</dt>
                                    <dd class="col-sm-8">{{ $tour->formatted_duration }}</dd>

                                    <dt class="col-sm-4">Participants:</dt>
                                    <dd class="col-sm-8">
                                        {{ $tour->min_participants }} - {{ $tour->max_participants ?? '∞' }}
                                    </dd>

                                    <dt class="col-sm-4">Devise:</dt>
                                    <dd class="col-sm-8">{{ $tour->currency ?? 'DJF' }}</dd>

                                    <dt class="col-sm-4">Vues:</dt>
                                    <dd class="col-sm-8">{{ number_format($tour->views_count) }}</dd>
                                </dl>
                            </div>
                        </div>

                        @if($tour->short_description)
                            <div class="mt-3">
                                <h6>Description courte</h6>
                                <p class="text-muted">{{ $tour->short_description }}</p>
                            </div>
                        @endif

                        @if($tour->description)
                            <div class="mt-3">
                                <h6>Description complète</h6>
                                <div>{!! nl2br(e($tour->description)) !!}</div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Itinéraire -->
                @if($tour->translation()->itinerary)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Itinéraire</h5>
                    </div>
                    <div class="card-body">
                        {!! nl2br(e($tour->translation()->itinerary)) !!}
                    </div>
                </div>
                @endif

                <!-- Créneaux -->
                {{-- <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Créneaux ({{ $tour->schedules->count() }})</h5>
                        <small class="text-muted">{{ $tour->schedules->where('status', 'available')->count() }} disponibles</small>
                    </div>
                    <div class="card-body">
                        @if($tour->schedules->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Horaire</th>
                                            <th>Places</th>
                                            <th>Guide</th>
                                            <th>Statut</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tour->schedules as $schedule)
                                            <tr>
                                                <td>{{ $schedule->formatted_date_range }}</td>
                                                <td>{{ $schedule->formatted_time_range }}</td>
                                                <td>
                                                    <span class="badge bg-primary">{{ $schedule->booked_spots }}/{{ $schedule->available_spots }}</span>
                                                </td>
                                                <td>{{ $schedule->guide_name ?: '-' }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $schedule->status_badge_class }}">
                                                        {{ $schedule->status_label }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Aucun créneau défini pour ce tour.</p>
                        @endif
                    </div>
                </div> --}}
            </div>

            <!-- Informations complémentaires -->
            <div class="col-lg-4">
                <!-- Cible -->
                @if($tour->target)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Cible du tour</h5>
                    </div>
                    <div class="card-body">
                        <h6>{{ $tour->target->title ?? $tour->target->name }}</h6>
                        <small class="text-muted">{{ class_basename($tour->target_type) }}</small>
                    </div>
                </div>
                @endif

                <!-- Point de rendez-vous -->
                @if($tour->meeting_point_address)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Point de rendez-vous</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-1">{{ $tour->meeting_point_address }}</p>
                        @if($tour->translation()->meeting_point_description)
                            <small class="text-muted">{{ $tour->translation()->meeting_point_description }}</small>
                        @endif
                        @if($tour->meeting_point_latitude && $tour->meeting_point_longitude)
                            <div class="mt-2">
                                <small class="text-muted">
                                    📍 {{ $tour->meeting_point_latitude }}, {{ $tour->meeting_point_longitude }}
                                </small>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <!-- Inclusions -->
                @if($tour->includes && count($tour->includes) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Inclus</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($tour->includes as $include)
                                <li>{{ $include }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Prérequis -->
                @if($tour->requirements && count($tour->requirements) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Prérequis</h5>
                    </div>
                    <div class="card-body">
                        <ul class="mb-0">
                            @foreach($tour->requirements as $requirement)
                                <li>{{ $requirement }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif

                <!-- Restrictions d'âge -->
                @if($tour->has_age_restrictions)
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Restrictions d'âge</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $tour->age_restrictions_text }}</p>
                    </div>
                </div>
                @endif

                <!-- Options -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Options</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" {{ $tour->weather_dependent ? 'checked' : '' }} disabled>
                            <label class="form-check-label">Dépendant de la météo</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" {{ $tour->is_recurring ? 'checked' : '' }} disabled>
                            <label class="form-check-label">Tour récurrent</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection