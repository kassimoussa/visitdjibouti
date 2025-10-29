@extends('operator.layouts.app')

@section('title', 'Inscriptions aux Activités')
@section('page-title', 'Gestion des Inscriptions')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Inscriptions aux Activités</h2>
            <p class="text-muted mb-0">Gérez toutes les inscriptions à vos activités</p>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('operator.activity-registrations.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Activité</label>
                        <select name="activity_id" class="form-control">
                            <option value="">Toutes les activités</option>
                            @foreach($activities as $activity)
                                <option value="{{ $activity->id }}" {{ request('activity_id') == $activity->id ? 'selected' : '' }}>
                                    {{ $activity->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="">Tous</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmé</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                            <option value="cancelled_by_user" {{ request('status') == 'cancelled_by_user' ? 'selected' : '' }}>Annulé (user)</option>
                            <option value="cancelled_by_operator" {{ request('status') == 'cancelled_by_operator' ? 'selected' : '' }}>Annulé (op.)</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Paiement</label>
                        <select name="payment_status" class="form-control">
                            <option value="">Tous</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Payé</option>
                            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Remboursé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date début</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Date fin</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fas fa-filter"></i>
                            </button>
                            <a href="{{ route('operator.activity-registrations.index') }}" class="btn btn-outline-secondary">
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
                    <i class="fas fa-list"></i>
                </div>
                <h4>{{ $statistics['total'] ?? 0 }}</h4>
                <p>Total</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>{{ $statistics['pending'] ?? 0 }}</h4>
                <p>En Attente</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>{{ $statistics['confirmed'] ?? 0 }}</h4>
                <p>Confirmées</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon info">
                    <i class="fas fa-flag-checkered"></i>
                </div>
                <h4>{{ $statistics['completed'] ?? 0 }}</h4>
                <p>Terminées</p>
            </div>
        </div>
    </div>

    <!-- Registrations List -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-list me-2"></i>
                Liste des Inscriptions
                <span class="badge bg-secondary ms-2">{{ $registrations->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($registrations->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Client</th>
                                <th>Activité</th>
                                <th>Participants</th>
                                <th>Date inscription</th>
                                <th>Statut</th>
                                <th>Paiement</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $registration)
                                <tr>
                                    <td><strong>#{{ $registration->id }}</strong></td>
                                    <td>
                                        <div>
                                            <strong>{{ $registration->customer_name }}</strong><br>
                                            <small class="text-muted">{{ $registration->customer_email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            {{ Str::limit($registration->activity->title, 30) }}<br>
                                            <small class="text-muted">
                                                {{ number_format($registration->activity->price, 0, ',', ' ') }} DJF
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            {{ $registration->number_of_people }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>
                                            {{ $registration->created_at->format('d/m/Y') }}<br>
                                            <small class="text-muted">{{ $registration->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td>{!! $registration->status_badge !!}</td>
                                    <td>
                                        @php
                                            $paymentBadge = match($registration->payment_status) {
                                                'paid' => 'bg-success',
                                                'refunded' => 'bg-info',
                                                default => 'bg-warning'
                                            };
                                        @endphp
                                        <span class="badge {{ $paymentBadge }}">
                                            {{ $registration->payment_status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('operator.activity-registrations.show', $registration) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($registration->status === 'pending')
                                                <form action="{{ route('operator.activity-registrations.confirm', $registration) }}"
                                                      method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success"
                                                            title="Confirmer"
                                                            onclick="return confirm('Confirmer cette inscription ?')">
                                                        <i class="fas fa-check"></i>
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
                        Affichage de {{ $registrations->firstItem() }} à {{ $registrations->lastItem() }} sur {{ $registrations->total() }} inscriptions
                    </div>
                    {{ $registrations->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-user-check fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucune inscription trouvée</h5>
                    <p class="text-muted mb-0">
                        @if(request()->hasAny(['activity_id', 'status', 'payment_status', 'date_from', 'date_to']))
                            Modifiez vos filtres pour voir plus d'inscriptions
                        @else
                            Les inscriptions à vos activités apparaîtront ici
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
