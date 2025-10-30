@extends('layouts.admin')

@section('title', 'Inscriptions aux Activités')
@section('page-title', 'Gestion des Inscriptions aux Activités')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Inscriptions aux Activités</h2>
            <p class="text-muted mb-0">Gérez toutes les inscriptions aux activités proposées</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('activity-registrations.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Nom, email..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Activité</label>
                        <select name="activity_id" class="form-control">
                            <option value="">Toutes les activités</option>
                            @foreach($activities as $activity)
                                <option value="{{ $activity->id }}" {{ request('activity_id') == $activity->id ? 'selected' : '' }}>
                                    {{ Str::limit($activity->title, 40) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="">Tous</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                            <option value="cancelled_by_user" {{ request('status') == 'cancelled_by_user' ? 'selected' : '' }}>Annulée (utilisateur)</option>
                            <option value="cancelled_by_operator" {{ request('status') == 'cancelled_by_operator' ? 'selected' : '' }}>Annulée (opérateur)</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                    <a href="{{ route('activity-registrations.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des inscriptions -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-list-alt me-2"></i>
                Liste des Inscriptions
                <span class="badge bg-secondary ms-2">{{ $registrations->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($registrations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Participant</th>
                                <th>Activité</th>
                                <th>Opérateur</th>
                                <th>Participants</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Paiement</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                                <tr>
                                    <td>
                                        @if($registration->appUser)
                                            <div>
                                                <strong>{{ $registration->appUser->name }}</strong><br>
                                                <small class="text-muted">{{ $registration->appUser->email }}</small>
                                            </div>
                                        @else
                                            <div>
                                                <strong>{{ $registration->guest_name }}</strong><br>
                                                <small class="text-muted">{{ $registration->guest_email }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('activities.show', $registration->activity) }}">
                                            {{ Str::limit($registration->activity->title, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('tour-operators.show', $registration->activity->tour_operator_id) }}">
                                            {{ Str::limit($registration->activity->tourOperator->name, 20) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">{{ $registration->number_of_participants }}</span>
                                    </td>
                                    <td>
                                        {{ $registration->created_at->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $registration->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($registration->status === 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($registration->status === 'confirmed')
                                            <span class="badge bg-success">Confirmée</span>
                                        @elseif($registration->status === 'completed')
                                            <span class="badge bg-primary">Terminée</span>
                                        @elseif($registration->status === 'cancelled_by_user')
                                            <span class="badge bg-danger">Annulée (user)</span>
                                        @else
                                            <span class="badge bg-danger">Annulée (op)</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($registration->payment_status === 'pending')
                                            <span class="badge bg-warning">En attente</span>
                                        @elseif($registration->payment_status === 'paid')
                                            <span class="badge bg-success">Payé</span>
                                        @else
                                            <span class="badge bg-info">Remboursé</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('activity-registrations.show', $registration) }}"
                                           class="btn btn-sm btn-outline-primary"
                                           title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $registrations->firstItem() }} à {{ $registrations->lastItem() }} sur {{ $registrations->total() }} inscriptions
                    </div>
                    {{ $registrations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune inscription trouvée</h5>
                    <p class="text-muted mb-0">
                        @if(request()->hasAny(['search', 'activity_id', 'tour_operator_id', 'status', 'payment_status', 'date_from', 'date_to']))
                            Modifiez vos filtres pour voir plus d'inscriptions
                        @else
                            Les inscriptions aux activités apparaîtront ici
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
