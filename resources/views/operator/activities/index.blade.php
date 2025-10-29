@extends('operator.layouts.app')

@section('title', 'Activités')
@section('page-title', 'Gestion des Activités')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mes Activités</h2>
            <p class="text-muted mb-0">Organisez et gérez vos activités touristiques</p>
        </div>
        <a href="{{ route('operator.activities.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Créer une Activité
        </a>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.activities.index') }}">
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
                                   placeholder="Nom, description...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="">Tous</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Difficulté</label>
                        <select name="difficulty" class="form-control">
                            <option value="">Toutes</option>
                            <option value="easy" {{ request('difficulty') == 'easy' ? 'selected' : '' }}>Facile</option>
                            <option value="moderate" {{ request('difficulty') == 'moderate' ? 'selected' : '' }}>Modéré</option>
                            <option value="difficult" {{ request('difficulty') == 'difficult' ? 'selected' : '' }}>Difficile</option>
                            <option value="expert" {{ request('difficulty') == 'expert' ? 'selected' : '' }}>Expert</option>
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
                            <a href="{{ route('operator.activities.index') }}" class="btn btn-outline-secondary">
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
            <div class="stats-card small">
                <div class="stats-icon secondary">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h4>{{ $statistics['draft'] ?? 0 }}</h4>
                <p>Brouillons</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>{{ $statistics['active'] ?? 0 }}</h4>
                <p>Actifs</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon warning">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <h4>{{ $statistics['inactive'] ?? 0 }}</h4>
                <p>Inactifs</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon primary">
                    <i class="fas fa-running"></i>
                </div>
                <h4>{{ $statistics['total'] ?? 0 }}</h4>
                <p>Total</p>
            </div>
        </div>
    </div>

    <!-- Activities List -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-list me-2"></i>
                Liste des Activités
                <span class="badge bg-secondary ms-2">{{ $activities->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($activities->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Activité</th>
                                <th>Difficulté & Prix</th>
                                <th>Région</th>
                                <th>Statut</th>
                                <th>Participants</th>
                                <th>Inscriptions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($activity->featuredImage)
                                                <img src="{{ asset($activity->featuredImage->thumbnail_path ?? $activity->featuredImage->path) }}"
                                                     alt="{{ $activity->title }}"
                                                     class="rounded me-3"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-running text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $activity->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($activity->short_description, 60) }}</small>
                                                @if($activity->is_featured)
                                                    <br><small class="badge bg-warning">Mis en avant</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <span class="badge
                                                @if($activity->difficulty_level === 'easy') bg-success
                                                @elseif($activity->difficulty_level === 'moderate') bg-warning
                                                @elseif($activity->difficulty_level === 'difficult') bg-danger
                                                @else bg-dark @endif">
                                                {{ $activity->difficulty_label }}
                                            </span>
                                            <br>
                                            <strong>{{ number_format($activity->price, 0, ',', ' ') }} DJF</strong>
                                        </div>
                                    </td>
                                    <td>
                                        @if($activity->region)
                                            <span class="badge bg-secondary">{{ $activity->region }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        {!! $activity->status_badge !!}
                                    </td>
                                    <td>
                                        <div class="text-start">
                                            <strong>{{ $activity->current_participants ?? 0 }}</strong>
                                            <span class="text-muted">/ {{ $activity->max_participants ?? '∞' }}</span>
                                            <br>
                                            <small class="text-muted">
                                                @if($activity->max_participants)
                                                    {{ round(($activity->current_participants / $activity->max_participants) * 100) }}% rempli
                                                @else
                                                    capacité illimitée
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-start">
                                            <strong>{{ $activity->registrations()->count() }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $activity->confirmedRegistrations()->count() }} confirmées
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('operator.activities.show', $activity) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($activity->status === 'draft')
                                                <a href="{{ route('operator.activities.edit', $activity) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if($activity->status !== 'draft')
                                                <form action="{{ route('operator.activities.toggle-status', $activity) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-{{ $activity->status === 'active' ? 'secondary' : 'success' }}"
                                                            title="{{ $activity->status === 'active' ? 'Désactiver' : 'Activer' }}">
                                                        <i class="fas fa-{{ $activity->status === 'active' ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
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
                        Affichage de {{ $activities->firstItem() }} à {{ $activities->lastItem() }} sur {{ $activities->total() }} activités
                    </div>
                    {{ $activities->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-running fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune activité trouvée</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'difficulty', 'region']))
                            Modifiez vos filtres pour voir plus d'activités
                        @else
                            Créez votre première activité pour commencer
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'status', 'difficulty', 'region']))
                        <a href="{{ route('operator.activities.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Créer une Activité
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
