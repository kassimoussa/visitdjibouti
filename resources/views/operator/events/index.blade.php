@extends('operator.layouts.app')

@section('title', 'Événements')
@section('page-title', 'Gestion des Événements')

@section('content')
<div class="fade-in">
    <!-- Header with actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mes Événements</h2>
            <p class="text-muted mb-0">Gérez et organisez vos événements touristiques</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.events.index') }}">
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
                                   placeholder="Titre, description...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="">Tous</option>
                            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publié</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Catégorie</label>
                        <select name="category" class="form-control">
                            <option value="">Toutes</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->translation(session('locale', 'fr'))->name ?? $category->name }}
                                </option>
                            @endforeach
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
                            <a href="{{ route('operator.events.index') }}" class="btn btn-outline-secondary">
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
                <div class="stats-icon primary">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <h4>{{ $statistics['total'] }}</h4>
                <p>Total Événements</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-eye"></i>
                </div>
                <h4>{{ $statistics['published'] }}</h4>
                <p>Publiés</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon warning">
                    <i class="fas fa-edit"></i>
                </div>
                <h4>{{ $statistics['draft'] }}</h4>
                <p>Brouillons</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon info">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <h4>{{ $statistics['upcoming'] }}</h4>
                <p>À venir</p>
            </div>
        </div>
    </div>

    <!-- Events List -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-list me-2"></i>
                Liste des Événements
                <span class="badge bg-secondary ms-2">{{ $events->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($events->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Événement</th>
                                <th>Date</th>
                                <th>Catégorie</th>
                                <th>Statut</th>
                                <th>Participants</th>
                                <th>Revenus</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($events as $event)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($event->featuredImage)
                                                <img src="{{ $event->featuredImage->getImageUrl() }}"
                                                     alt="{{ $event->title }}"
                                                     class="rounded me-3"
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center"
                                                     style="width: 60px; height: 60px;">
                                                    <i class="fas fa-calendar-alt text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-1">{{ $event->title }}</h6>
                                                <small class="text-muted">{{ Str::limit($event->short_description, 60) }}</small>
                                                @if($event->is_featured)
                                                    <br><small class="badge bg-warning">Mis en avant</small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $event->start_date->format('d/m/Y') }}</strong>
                                            @if($event->start_time)
                                                <br><small class="text-muted">{{ $event->start_time->format('H:i') }}</small>
                                            @endif
                                            @if($event->end_date && $event->end_date != $event->start_date)
                                                <br><small class="text-muted">au {{ $event->end_date->format('d/m/Y') }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        @if($event->category)
                                            <span class="badge bg-secondary">
                                                {{ $event->category->translation(session('locale', 'fr'))->name ?? $event->category->name }}
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge status-{{ $event->status }}">
                                            {{ ucfirst($event->status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-center">
                                            <strong>{{ $event->current_participants }}</strong>
                                            @if($event->max_participants)
                                                / {{ $event->max_participants }}
                                                <div class="progress mt-1" style="height: 4px;">
                                                    <div class="progress-bar"
                                                         style="width: {{ $event->max_participants > 0 ? ($event->current_participants / $event->max_participants * 100) : 0 }}%"></div>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <strong>{{ number_format($event->registrations()->where('status', 'confirmed')->sum('payment_amount'), 0, ',', ' ') }} DJF</strong>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('operator.events.show', $event) }}"
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
                        Affichage de {{ $events->firstItem() }} à {{ $events->lastItem() }} sur {{ $events->total() }} événements
                    </div>
                    {{ $events->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun événement trouvé</h5>
                    <p class="text-muted mb-4">
                        @if(request()->hasAny(['search', 'status', 'category', 'region']))
                            Modifiez vos filtres pour voir plus d'événements
                        @else
                            Créez votre premier événement pour commencer
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection