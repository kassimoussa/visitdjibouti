@props(['comments', 'title' => 'Commentaires'])

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">
            <i class="fas fa-comments me-2"></i>
            {{ $title }}
        </h5>
        <span class="badge bg-primary rounded-pill">{{ $comments->count() }}</span>
    </div>
    <div class="card-body">
        @if($comments->count() > 0)
            <div class="comments-list">
                @foreach($comments as $comment)
                    <div class="comment-item mb-4 pb-4 {{ !$loop->last ? 'border-bottom' : '' }}">
                        <div class="d-flex align-items-start">
                            <!-- Avatar -->
                            <div class="flex-shrink-0 me-3">
                                <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                     style="width: 45px; height: 45px; border-radius: 50%; font-size: 18px; font-weight: bold;">
                                    {{ strtoupper(substr($comment->author_name, 0, 1)) }}
                                </div>
                            </div>

                            <!-- Comment Content -->
                            <div class="flex-grow-1">
                                <!-- Author & Date -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <strong class="text-dark">{{ $comment->author_name }}</strong>
                                        @if($comment->appUser)
                                            <span class="badge bg-success ms-2" style="font-size: 0.7rem;">
                                                <i class="fas fa-check-circle"></i> Utilisateur Vérifié
                                            </span>
                                        @else
                                            <span class="badge bg-secondary ms-2" style="font-size: 0.7rem;">
                                                <i class="fas fa-user"></i> Invité
                                            </span>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $comment->created_at->diffForHumans() }}
                                        </small>
                                    </div>

                                    <!-- Approval Status -->
                                    @if(!$comment->is_approved)
                                        <span class="badge bg-warning">
                                            <i class="fas fa-hourglass-half me-1"></i>
                                            En attente d'approbation
                                        </span>
                                    @endif
                                </div>

                                <!-- Comment Text -->
                                <div class="comment-text bg-light p-3 rounded">
                                    <p class="mb-0">{{ $comment->comment }}</p>
                                </div>

                                <!-- Comment Stats -->
                                <div class="d-flex align-items-center mt-2">
                                    @if($comment->likes_count > 0)
                                        <small class="text-muted me-3">
                                            <i class="fas fa-heart text-danger me-1"></i>
                                            {{ $comment->likes_count }} {{ Str::plural('like', $comment->likes_count) }}
                                        </small>
                                    @endif

                                    @if($comment->replies && $comment->replies->count() > 0)
                                        <small class="text-muted">
                                            <i class="fas fa-reply me-1"></i>
                                            {{ $comment->replies->count() }} {{ Str::plural('réponse', $comment->replies->count()) }}
                                        </small>
                                    @endif
                                </div>

                                <!-- Replies -->
                                @if($comment->replies && $comment->replies->where('is_approved', true)->count() > 0)
                                    <div class="mt-3 ms-4 ps-3 border-start border-2 border-primary">
                                        @foreach($comment->replies->where('is_approved', true) as $reply)
                                            <div class="reply-item mb-3">
                                                <div class="d-flex align-items-start">
                                                    <div class="flex-shrink-0 me-2">
                                                        <div class="avatar-circle bg-secondary text-white d-flex align-items-center justify-content-center"
                                                             style="width: 35px; height: 35px; border-radius: 50%; font-size: 14px; font-weight: bold;">
                                                            {{ strtoupper(substr($reply->author_name, 0, 1)) }}
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="d-flex justify-content-between align-items-start mb-1">
                                                            <div>
                                                                <strong class="text-dark" style="font-size: 0.9rem;">{{ $reply->author_name }}</strong>
                                                                @if($reply->appUser)
                                                                    <span class="badge bg-success ms-1" style="font-size: 0.65rem;">
                                                                        <i class="fas fa-check-circle"></i> Vérifié
                                                                    </span>
                                                                @endif
                                                                <br>
                                                                <small class="text-muted" style="font-size: 0.75rem;">
                                                                    <i class="far fa-clock me-1"></i>
                                                                    {{ $reply->created_at->diffForHumans() }}
                                                                </small>
                                                            </div>
                                                        </div>
                                                        <div class="reply-text bg-white p-2 rounded border" style="font-size: 0.9rem;">
                                                            <p class="mb-0">{{ $reply->comment }}</p>
                                                        </div>
                                                        @if($reply->likes_count > 0)
                                                            <small class="text-muted mt-1 d-inline-block" style="font-size: 0.75rem;">
                                                                <i class="fas fa-heart text-danger me-1"></i>
                                                                {{ $reply->likes_count }}
                                                            </small>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination if needed -->
            @if(method_exists($comments, 'links'))
                <div class="mt-4">
                    {{ $comments->links() }}
                </div>
            @endif
        @else
            <div class="text-center py-5">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun commentaire</h5>
                <p class="text-muted mb-0">Les commentaires des utilisateurs apparaîtront ici</p>
            </div>
        @endif
    </div>
</div>

<style>
    .comment-item:hover {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin: -15px;
        margin-bottom: 15px;
    }

    .comment-text {
        word-wrap: break-word;
        white-space: pre-wrap;
    }

    .reply-item {
        transition: all 0.2s ease;
    }

    .reply-item:hover {
        transform: translateX(5px);
    }

    .avatar-circle {
        transition: transform 0.2s ease;
    }

    .avatar-circle:hover {
        transform: scale(1.1);
    }
</style>
