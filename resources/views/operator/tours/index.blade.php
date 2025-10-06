@extends('operator.layouts.app')

@section('title', 'Tours Guidés')
@section('page-title', 'Gestion des Tours')

@section('content')
<div class="operator-fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mes Tours Guidés</h2>
            <p class="text-muted mb-0">Organisez et gérez vos circuits touristiques</p>
        </div>
        @if($user->canManageTours())
            <a href="{{ route('operator.tours.create') }}" class="operator-btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Nouveau Tour
            </a>
        @endif
    </div>

    <!-- Filters and Search -->
    <div class="operator-card mb-4">
        <div class="operator-card-body">
            <form method="GET" action="{{ route('operator.tours.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Rechercher</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                   class="operator-form-control"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="Nom du tour, description...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="operator-form-control">
                            <option value="">Tous</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publié</option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Durée</label>
                        <select name="duration" class="operator-form-control">
                            <option value="">Toutes</option>
                            <option value="half_day" {{ request('duration') == 'half_day' ? 'selected' : '' }}>Demi-journée</option>
                            <option value="full_day" {{ request('duration') == 'full_day' ? 'selected' : '' }}>Journée complète</option>
                            <option value="multi_day" {{ request('duration') == 'multi_day' ? 'selected' : '' }}>Plusieurs jours</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Région</label>
                        <select name="region" class="operator-form-control">
                            <option value="">Toutes</option>
                            @foreach(['Djibouti', 'Ali Sabieh', 'Dikhil', 'Tadjourah', 'Obock', 'Arta'] as $region)
                                <option value="{{ $region }}" {{ request('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="operator-btn btn-outline-primary">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('operator.tours.index') }}" class="operator-btn btn-outline-secondary">
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
        <div class="col-md-3">
            <div class="operator-stats-card small">
                <div class="operator-stats-icon primary">
                    <i class="fas fa-route"></i>
                </div>
                <h4>{{ $statistics['total'] }}</h4>
                <p>Total Tours</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="operator-stats-card small">
                <div class="operator-stats-icon success">
                    <i class="fas fa-eye"></i>
                </div>
                <h4>{{ $statistics['published'] }}</h4>
                <p>Publiés</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="operator-stats-card small">
                <div class="operator-stats-icon warning">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h4>{{ $statistics['active_schedules'] }}</h4>
                <p>Horaires Actifs</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="operator-stats-card small">
                <div class="operator-stats-icon info">
                    <i class="fas fa-users"></i>
                </div>
                <h4>{{ $statistics['total_bookings'] }}</h4>
                <p>Réservations</p>
            </div>
        </div>
    </div>

    <!-- Tours List -->
    <div class="operator-card">
        <div class="operator-card-header">
            <h5>
                <i class="fas fa-list me-2"></i>
                Liste des Tours
                <span class="badge bg-secondary ms-2">{{ $tours->total() }}</span>
            </h5>
            <div class="operator-card-actions">
                <a href="{{ route('operator.tours.export') }}" class="btn btn-sm btn-outline-success">
                    <i class="fas fa-download me-1"></i>
                    Exporter
                </a>
            </div>
        </div>
        <div class="operator-card-body">
            @if($tours->count() > 0)
                <div class="operator-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tour</th>
                                <th>Durée & Prix</th>
                                <th>Région</th>
                                <th>Statut</th>
                                <th>Horaires</th>
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
                                            <span class="badge bg-info">
                                                @switch($tour->duration_type)
                                                    @case('half_day')
                                                        Demi-journée
                                                        @break
                                                    @case('full_day')
                                                        Journée complète
                                                        @break
                                                    @case('multi_day')
                                                        {{ $tour->duration_days }}j
                                                        @break
                                                    @default
                                                        Non spécifié
                                                @endswitch
                                            </span>
                                            <br>
                                            <strong>{{ number_format($tour->price_adult, 0, ',', ' ') }} DJF</strong>
                                            @if($tour->price_child)
                                                <br><small class="text-muted">Enfant: {{ number_format($tour->price_child, 0, ',', ' ') }} DJF</small>
                                            @endif
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
                                        <span class="operator-badge status-{{ $tour->status }}">
                                            {{ ucfirst($tour->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong>{{ $tour->schedules()->count() }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $tour->schedules()->where('status', 'active')->count() }} actifs
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong>{{ $tour->totalBookings() }}</strong>
                                            <br>
                                            <small class="text-success">
                                                {{ number_format($tour->totalRevenue(), 0, ',', ' ') }} DJF
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
                                            @if($user->canManageTours())
                                                <a href="{{ route('operator.tours.edit', $tour) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('operator.tours.schedules', $tour) }}"
                                                   class="btn btn-sm btn-outline-info"
                                                   title="Gérer les horaires">
                                                    <i class="fas fa-calendar-alt"></i>
                                                </a>
                                            @endif
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
                    @if($user->canManageTours())
                        <a href="{{ route('operator.tours.create') }}" class="operator-btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Créer un tour
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection