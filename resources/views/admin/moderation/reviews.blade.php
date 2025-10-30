@extends('layouts.admin')

@section('title', 'Modération des Avis')
@section('page-title', 'Modération des Avis')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Modération des Avis</h2>
            <p class="text-muted mb-0">Gérez et modérez les avis laissés par les utilisateurs sur les POIs</p>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon secondary">
                    <i class="fas fa-star"></i>
                </div>
                <h4>{{ $stats['total'] ?? 0 }}</h4>
                <p>Total des avis</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>{{ $stats['approved'] ?? 0 }}</h4>
                <p>Approuvés</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>{{ $stats['pending'] ?? 0 }}</h4>
                <p>En attente</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon primary">
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <h4>{{ $stats['average_rating'] ?? 0 }}/5</h4>
                <p>Note moyenne</p>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.moderation.reviews') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Titre, commentaire, nom..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control">
                            <option value="">Tous</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvés</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Note</label>
                        <select name="rating" class="form-control">
                            <option value="">Toutes</option>
                            <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 étoiles</option>
                            <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 étoiles</option>
                            <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 étoiles</option>
                            <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 étoiles</option>
                            <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 étoile</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('admin.moderation.reviews') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des avis -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-star me-2"></i>
                Liste des Avis
                <span class="badge bg-secondary ms-2">{{ $reviews->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($reviews->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Auteur</th>
                                <th>POI</th>
                                <th>Note</th>
                                <th>Avis</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reviews as $review)
                                <tr>
                                    <td>
                                        @if($review->appUser)
                                            <div>
                                                <strong>{{ $review->appUser->name }}</strong>
                                                @if($review->is_verified)
                                                    <span class="badge bg-info" title="Visite vérifiée">
                                                        <i class="fas fa-check-circle"></i>
                                                    </span>
                                                @endif
                                                <br>
                                                <small class="text-muted">{{ $review->appUser->email }}</small>
                                            </div>
                                        @else
                                            <div>
                                                <strong>{{ $review->guest_name }}</strong><br>
                                                <small class="text-muted">{{ $review->guest_email }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('pois.show', $review->poi) }}" target="_blank">
                                            {{ Str::limit($review->poi->name, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $review->rating)
                                                    <i class="fas fa-star text-warning"></i>
                                                @else
                                                    <i class="far fa-star text-muted"></i>
                                                @endif
                                            @endfor
                                            <span class="ms-2">{{ $review->rating }}/5</span>
                                        </div>
                                    </td>
                                    <td style="max-width: 300px;">
                                        @if($review->title)
                                            <strong>{{ Str::limit($review->title, 40) }}</strong><br>
                                        @endif
                                        @if($review->comment)
                                            <small class="text-muted">{{ Str::limit($review->comment, 80) }}</small>
                                        @endif
                                        @if($review->helpful_count > 0)
                                            <br>
                                            <span class="badge bg-light text-dark mt-1">
                                                <i class="fas fa-thumbs-up"></i> {{ $review->helpful_count }} utile(s)
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $review->created_at->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $review->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($review->is_approved)
                                            <span class="badge bg-success">Approuvé</span>
                                        @else
                                            <span class="badge bg-warning">En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(!$review->is_approved)
                                                <form action="{{ route('admin.moderation.reviews.approve', $review) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Approuver">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('admin.moderation.reviews.reject', $review) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Rejeter">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#reviewModal{{ $review->id }}"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer cet avis ?')) { document.getElementById('delete-review-{{ $review->id }}').submit(); }"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-review-{{ $review->id }}"
                                                  action="{{ route('admin.moderation.reviews.delete', $review) }}"
                                                  method="POST"
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal détails -->
                                <div class="modal fade" id="reviewModal{{ $review->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Détails de l'avis</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>POI :</strong>
                                                    <p>{{ $review->poi->name }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Auteur :</strong>
                                                    <p>
                                                        {{ $review->appUser ? $review->appUser->name : $review->guest_name }}
                                                        @if($review->is_verified)
                                                            <span class="badge bg-info">Visite vérifiée</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Note :</strong>
                                                    <div>
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->rating)
                                                                <i class="fas fa-star text-warning"></i>
                                                            @else
                                                                <i class="far fa-star text-muted"></i>
                                                            @endif
                                                        @endfor
                                                        <span class="ms-2">{{ $review->rating }}/5</span>
                                                    </div>
                                                </div>
                                                @if($review->title)
                                                    <div class="mb-3">
                                                        <strong>Titre :</strong>
                                                        <p>{{ $review->title }}</p>
                                                    </div>
                                                @endif
                                                @if($review->comment)
                                                    <div class="mb-3">
                                                        <strong>Commentaire :</strong>
                                                        <p>{{ $review->comment }}</p>
                                                    </div>
                                                @endif
                                                @if($review->operator_response)
                                                    <div class="mb-3">
                                                        <strong>Réponse de l'opérateur :</strong>
                                                        <div class="alert alert-info">
                                                            {{ $review->operator_response }}
                                                            <br>
                                                            <small class="text-muted">{{ $review->operator_response_at->format('d/m/Y H:i') }}</small>
                                                        </div>
                                                    </div>
                                                @endif
                                                <div class="mb-3">
                                                    <strong>Date :</strong>
                                                    <p>{{ $review->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Utile :</strong>
                                                    <p>{{ $review->helpful_count }} personne(s)</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="text-muted">
                        Affichage de {{ $reviews->firstItem() }} à {{ $reviews->lastItem() }} sur {{ $reviews->total() }} avis
                    </div>
                    {{ $reviews->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-star fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun avis trouvé</h5>
                    <p class="text-muted mb-0">
                        @if(request()->hasAny(['search', 'status', 'rating']))
                            Modifiez vos filtres pour voir plus d'avis
                        @else
                            Les avis laissés par les utilisateurs apparaîtront ici
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
