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
                                <th style="cursor: pointer;" onclick="window.location='{{ route('activities.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'id', 'direction' => request('sort') === 'id' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}'">
                                    ID
                                    @if(request('sort') === 'id')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th style="cursor: pointer;" onclick="window.location='{{ route('activities.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}'">
                                    Nom
                                    @if(request('sort') === 'name')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th style="cursor: pointer;" onclick="window.location='{{ route('activities.index', array_merge(request()->except('sort', 'direction'), ['sort' => 'operator', 'direction' => request('sort') === 'operator' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}'">
                                    Opérateur
                                    @if(request('sort') === 'operator')
                                        <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th>Prix</th>
                                <th>Participants</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activities as $activity)
                                <tr>
                                    <td>{{ $activity->id }}</td>
                                    <td>
                                        <div>
                                            <a href="{{ route('activities.show', $activity) }}" class="text-decoration-none">
                                                <strong>{{ Str::limit($activity->title, 50) }}</strong>
                                            </a>
                                            @if($activity->is_featured)
                                                <span class="badge bg-warning text-dark ms-1">
                                                    <i class="fas fa-star"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        {{ $activity->tourOperator->name }}
                                    </td>
                                    <td>
                                        {{ number_format($activity->price, 0, ',', ' ') }} {{ $activity->currency }}
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
                        @if(request()->hasAny(['search', 'status', 'tour_operator_id']))
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
