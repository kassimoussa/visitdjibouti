@extends('layouts.admin')

@section('title', 'Activités')
@section('page-title', 'Gestion des Activités')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Activités des Opérateurs</h2>
            <p class="text-muted mb-0">Gérez toutes les activités proposées par les opérateurs touristiques</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon secondary">
                    <i class="fas fa-list"></i>
                </div>
                <h4>{{ $statistics['total'] ?? 0 }}</h4>
                <p>Total</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card small">
                <div class="stats-icon warning">
                    <i class="fas fa-file-alt"></i>
                </div>
                <h4>{{ $statistics['draft'] ?? 0 }}</h4>
                <p>Brouillons</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>{{ $statistics['active'] ?? 0 }}</h4>
                <p>Actives</p>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stats-card small">
                <div class="stats-icon danger">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <h4>{{ $statistics['inactive'] ?? 0 }}</h4>
                <p>Inactives</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon primary">
                    <i class="fas fa-star"></i>
                </div>
                <h4>{{ $statistics['featured'] ?? 0 }}</h4>
                <p>Mises en avant</p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('activities.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Titre, description..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="">Tous</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Opérateur</label>
                        <select name="tour_operator_id" class="form-control">
                            <option value="">Tous les opérateurs</option>
                            @foreach($tourOperators as $operator)
                                <option value="{{ $operator->id }}" {{ request('tour_operator_id') == $operator->id ? 'selected' : '' }}>
                                    {{ $operator->name }}
                                </option>
                            @endforeach
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
                            <option value="Djibouti" {{ request('region') == 'Djibouti' ? 'selected' : '' }}>Djibouti</option>
                            <option value="Ali Sabieh" {{ request('region') == 'Ali Sabieh' ? 'selected' : '' }}>Ali Sabieh</option>
                            <option value="Dikhil" {{ request('region') == 'Dikhil' ? 'selected' : '' }}>Dikhil</option>
                            <option value="Tadjourah" {{ request('region') == 'Tadjourah' ? 'selected' : '' }}>Tadjourah</option>
                            <option value="Obock" {{ request('region') == 'Obock' ? 'selected' : '' }}>Obock</option>
                            <option value="Arta" {{ request('region') == 'Arta' ? 'selected' : '' }}>Arta</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('activities.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des activités -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-running me-2"></i>
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
                                <th>Opérateur</th>
                                <th>Prix</th>
                                <th>Difficulté</th>
                                <th>Participants</th>
                                <th>Statut</th>
                                <th>Stats</th>
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
                                            @endif
                                            <div>
                                                <strong>{{ Str::limit($activity->title, 40) }}</strong>
                                                @if($activity->is_featured)
                                                    <span class="badge bg-warning ms-2">
                                                        <i class="fas fa-star"></i> Mise en avant
                                                    </span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $activity->region ?? 'Non spécifié' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ $activity->tourOperator->name }}</strong>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($activity->price, 0, ',', ' ') }}</strong> {{ $activity->currency }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $activity->difficulty_level === 'easy' ? 'success' : ($activity->difficulty_level === 'moderate' ? 'warning' : 'danger') }}">
                                            {{ $activity->difficulty_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $activity->current_participants }}/{{ $activity->max_participants ?? '∞' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($activity->status === 'draft')
                                            <span class="badge bg-secondary">Brouillon</span>
                                        @elseif($activity->status === 'active')
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="fas fa-eye"></i> {{ $activity->views_count }}<br>
                                            <i class="fas fa-user-check"></i> {{ $activity->registrations_count }}
                                        </small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('activities.show', $activity) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($activity->status !== 'draft')
                                                <form action="{{ route('activities.toggle-status', $activity) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-{{ $activity->status === 'active' ? 'warning' : 'success' }}"
                                                            title="{{ $activity->status === 'active' ? 'Désactiver' : 'Activer' }}">
                                                        <i class="fas fa-{{ $activity->status === 'active' ? 'pause' : 'play' }}"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <form action="{{ route('activities.toggle-featured', $activity) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit"
                                                        class="btn btn-sm btn-outline-warning"
                                                        title="{{ $activity->is_featured ? 'Retirer mise en avant' : 'Mettre en avant' }}">
                                                    <i class="fas fa-star{{ $activity->is_featured ? '' : '-half-alt' }}"></i>
                                                </button>
                                            </form>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cette activité ?')) { document.getElementById('delete-form-{{ $activity->id }}').submit(); }"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-form-{{ $activity->id }}"
                                                  action="{{ route('activities.destroy', $activity) }}"
                                                  method="POST"
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
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
                    <p class="text-muted mb-0">
                        @if(request()->hasAny(['search', 'status', 'tour_operator_id', 'difficulty', 'region']))
                            Modifiez vos filtres pour voir plus d'activités
                        @else
                            Les activités créées par les opérateurs apparaîtront ici
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
