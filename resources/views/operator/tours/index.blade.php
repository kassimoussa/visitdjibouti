@extends('operator.layouts.app')

@section('title', 'Tours Guidés')
@section('page-title', 'Gestion des Tours')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mes Tours Guidés</h2>
            <p class="text-muted mb-0">Organisez et gérez vos circuits touristiques</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.tours.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                   class="form-control"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nom du tour, description...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="">Tous</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Durée</label>
                        <select name="duration" class="form-control">
                            <option value="">Toutes</option>
                            <option value="half_day" {{ request('duration') == 'half_day' ? 'selected' : '' }}>Demi-journée</option>
                            <option value="full_day" {{ request('duration') == 'full_day' ? 'selected' : '' }}>Journée complète</option>
                            <option value="multi_day" {{ request('duration') == 'multi_day' ? 'selected' : '' }}>Plusieurs jours</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Région</label>
                        <select name="region" class="form-control">
                            <option value="">Toutes</option>
                            @foreach(['Djibouti', 'Ali Sabieh', 'Dikhil', 'Tadjourah', 'Obock', 'Arta'] as $region)
                                <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('operator.tours.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card small">
                <div class="stats-icon primary">
                    <i class="fas fa-route"></i>
                </div>
                <h4>{{ $statistics['total'] }}</h4>
                <p>Total Tours</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>{{ $statistics['active'] }}</h4>
                <p>Actifs</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card small">
                <div class="stats-icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h4>{{ $statistics['inactive'] }}</h4>
                <p>Inactifs</p>
            </div>
        </div>
    </div>

    <!-- Tours List -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-list me-2"></i>
                Liste des Tours
                <span class="badge bg-secondary ms-2">{{ $tours->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($tours->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tour</th>
                                <th>Durée & Prix</th>
                                <th>Région</th>
                                <th>Statut</th>
                                <th>Participants</th>
                                <th>Réservations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tours as $tour)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($tour->featuredImage)
                                                <img src="{{ $tour->featuredImage->getImageUrl() }}"
                                                     alt="{{ $tour->title }}"
                                                     class="rounded me-3"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-route text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $tour->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($tour->short_description, 60) }}</small>
                                                @if($tour->is_featured)
                                                    <br><small class="badge bg-warning">Mis en avant</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            @if($tour->duration_hours)
                                                <span class="badge bg-info">{{ $tour->duration_hours }}h</span>
                                                <br>
                                            @endif
                                            <strong>{{ number_format($tour->price, 0, ',', ' ') }} DJF</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($tour->region)
                                            <span class="badge bg-secondary">{{ $tour->region }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge status-{{ $tour->status }}">
                                            {{ ucfirst($tour->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-start">
                                            <strong>{{ $tour->current_participants ?? 0 }}</strong>
                                            <span class="text-muted">/ {{ $tour->max_participants ?? '∞' }}</span>
                                            <br>
                                            <small class="text-muted">
                                                @if($tour->max_participants)
                                                    {{ round(($tour->current_participants / $tour->max_participants) * 100) }}% rempli
                                                @else
                                                    capacité illimitée
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong>{{ $tour->reservations()->count() }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $tour->confirmedReservations()->count() }} confirmées
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operator.tours.show', $tour) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $tours->firstItem() }} à {{ $tours->lastItem() }} sur {{ $tours->total() }} tours
                    </div>
                    {{ $tours->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-route fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun tour trouvé</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'duration', 'region']))
                            Modifiez vos filtres pour voir plus de tours
                        @else
                            Créez votre premier tour guidé pour commencer
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection