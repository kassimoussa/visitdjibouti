@extends('layouts.admin')

@section('title', 'Modération des Commentaires')
@section('page-title', 'Modération des Commentaires')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Modération des Commentaires</h2>
            <p class="text-muted mb-0">Gérez et modérez les commentaires sur POIs, Events, Tours, Opérateurs et Activités</p>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('moderation.comments') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Recherche</label>
                        <input type="text" name="search" class="form-control" placeholder="Commentaire, nom..." value="{{ request('search') }}" onchange="this.form.submit()">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Statut</label>
                        <select name="status" class="form-control" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvés</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type de contenu</label>
                        <select name="type" class="form-control" onchange="this.form.submit()">
                            <option value="">Tous</option>
                            <option value="poi" {{ request('type') == 'poi' ? 'selected' : '' }}>POI</option>
                            <option value="event" {{ request('type') == 'event' ? 'selected' : '' }}>Event</option>
                            <option value="tour" {{ request('type') == 'tour' ? 'selected' : '' }}>Tour</option>
                            <option value="tour_operator" {{ request('type') == 'tour_operator' ? 'selected' : '' }}>Opérateur</option>
                            <option value="activity" {{ request('type') == 'activity' ? 'selected' : '' }}>Activité</option>
                        </select>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des commentaires -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-comments me-2"></i>
                Liste des Commentaires
                <span class="badge bg-secondary ms-2">{{ $comments->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($comments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Auteur</th>
                                <th>Type</th>
                                <th>Contenu</th>
                                <th>Commentaire</th>
                                <th>Date</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($comments as $comment)
                                <tr>
                                    <td>
                                        @if($comment->appUser)
                                            <div>
                                                <strong>{{ $comment->appUser->name }}</strong><br>
                                                <small class="text-muted">{{ $comment->appUser->email }}</small>
                                            </div>
                                        @else
                                            <div>
                                                <strong>{{ $comment->guest_name }}</strong><br>
                                                <small class="text-muted">{{ $comment->guest_email }}</small>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $typeLabel = match(class_basename($comment->commentable_type)) {
                                                'Poi' => 'POI',
                                                'Event' => 'Event',
                                                'Tour' => 'Tour',
                                                'TourOperator' => 'Opérateur',
                                                'Activity' => 'Activité',
                                                default => 'Inconnu'
                                            };
                                            $typeIcon = match(class_basename($comment->commentable_type)) {
                                                'Poi' => 'map-marker-alt',
                                                'Event' => 'calendar-alt',
                                                'Tour' => 'map-signs',
                                                'TourOperator' => 'route',
                                                'Activity' => 'running',
                                                default => 'question'
                                            };
                                        @endphp
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-{{ $typeIcon }} me-1"></i>{{ $typeLabel }}
                                        </span>
                                    </td>
                                    <td style="max-width: 200px;">
                                        @if($comment->commentable)
                                            @php
                                                $name = match(class_basename($comment->commentable_type)) {
                                                    'Poi' => $comment->commentable->name ?? 'N/A',
                                                    'Event' => $comment->commentable->title ?? 'N/A',
                                                    'Tour' => $comment->commentable->title ?? 'N/A',
                                                    'TourOperator' => $comment->commentable->name ?? 'N/A',
                                                    'Activity' => $comment->commentable->title ?? 'N/A',
                                                    default => 'N/A'
                                                };
                                            @endphp
                                            {{ Str::limit($name, 30) }}
                                        @else
                                            <span class="text-muted">Contenu supprimé</span>
                                        @endif
                                    </td>
                                    <td style="max-width: 300px;">
                                        {{ Str::limit($comment->comment, 100) }}
                                        @if($comment->likes_count > 0)
                                            <br>
                                            <span class="badge bg-light text-dark mt-1">
                                                <i class="fas fa-heart"></i> {{ $comment->likes_count }} like(s)
                                            </span>
                                        @endif
                                        @if($comment->replies && $comment->replies->count() > 0)
                                            <br>
                                            <span class="badge bg-info mt-1">
                                                <i class="fas fa-reply"></i> {{ $comment->replies->count() }} réponse(s)
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $comment->created_at->format('d/m/Y') }}<br>
                                        <small class="text-muted">{{ $comment->created_at->format('H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($comment->is_approved)
                                            <span class="badge bg-success">Approuvé</span>
                                        @else
                                            <span class="badge bg-warning">En attente</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if(!$comment->is_approved)
                                                <form action="{{ route('moderation.comments.approve', $comment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-success" title="Approuver">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('moderation.comments.reject', $comment) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Rejeter">
                                                        <i class="fas fa-ban"></i>
                                                    </button>
                                                </form>
                                            @endif
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#commentModal{{ $comment->id }}"
                                                    title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-danger"
                                                    onclick="if(confirm('Êtes-vous sûr de vouloir supprimer ce commentaire ?')) { document.getElementById('delete-comment-{{ $comment->id }}').submit(); }"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <form id="delete-comment-{{ $comment->id }}"
                                                  action="{{ route('moderation.comments.delete', $comment) }}"
                                                  method="POST"
                                                  style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal détails -->
                                <div class="modal fade" id="commentModal{{ $comment->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Détails du commentaire</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>Type de contenu :</strong>
                                                    <p>
                                                        <span class="badge bg-secondary">
                                                            <i class="fas fa-{{ $typeIcon }} me-1"></i>{{ $typeLabel }}
                                                        </span>
                                                    </p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Contenu concerné :</strong>
                                                    <p>
                                                        @if($comment->commentable)
                                                            {{ $name }}
                                                        @else
                                                            <span class="text-muted">Contenu supprimé</span>
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Auteur :</strong>
                                                    <p>{{ $comment->appUser ? $comment->appUser->name : $comment->guest_name }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Commentaire :</strong>
                                                    <p>{{ $comment->comment }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Date :</strong>
                                                    <p>{{ $comment->created_at->format('d/m/Y H:i') }}</p>
                                                </div>
                                                <div class="mb-3">
                                                    <strong>Likes :</strong>
                                                    <p>{{ $comment->likes_count }} personne(s)</p>
                                                </div>
                                                @if($comment->replies && $comment->replies->count() > 0)
                                                    <div class="mb-3">
                                                        <strong>Réponses :</strong>
                                                        <div class="mt-2">
                                                            @foreach($comment->replies as $reply)
                                                                <div class="card mb-2">
                                                                    <div class="card-body">
                                                                        <div class="d-flex justify-content-between mb-2">
                                                                            <strong>{{ $reply->appUser ? $reply->appUser->name : $reply->guest_name }}</strong>
                                                                            <small class="text-muted">{{ $reply->created_at->format('d/m/Y H:i') }}</small>
                                                                        </div>
                                                                        <p class="mb-0">{{ $reply->comment }}</p>
                                                                        @if(!$reply->is_approved)
                                                                            <span class="badge bg-warning mt-2">En attente</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
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
                        Affichage de {{ $comments->firstItem() }} à {{ $comments->lastItem() }} sur {{ $comments->total() }} commentaires
                    </div>
                    {{ $comments->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Aucun commentaire trouvé</h5>
                    <p class="text-muted mb-0">
                        @if(request()->hasAny(['search', 'status', 'type']))
                            Modifiez vos filtres pour voir plus de commentaires
                        @else
                            Les commentaires laissés par les utilisateurs apparaîtront ici
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
