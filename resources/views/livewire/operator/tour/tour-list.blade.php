<div class="fade-in">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Mes Tours Guidés</h2>
            <p class="text-muted mb-0">Organisez et gérez vos circuits touristiques</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('operator.tour-reservations.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-clipboard-list me-2"></i>
                Voir les réservations
            </a>
            <a href="{{ route('operator.tours.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Créer un Tour
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters and Search -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Rechercher</label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text"
                               class="form-control"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Nom du tour, description...">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select wire:model.live="status" class="form-control">
                        <option value="">Tous</option>
                        <option value="draft">Brouillon</option>
                        <option value="pending_approval">En attente</option>
                        <option value="active">Actif</option>
                        <option value="rejected">Rejeté</option>
                        <option value="inactive">Inactif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Durée</label>
                    <select wire:model.live="duration" class="form-control">
                        <option value="">Toutes</option>
                        <option value="half_day">Demi-journée</option>
                        <option value="full_day">Journée complète</option>
                        <option value="multi_day">Plusieurs jours</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Région</label>
                    <select wire:model.live="region" class="form-control">
                        <option value="">Toutes</option>
                        @foreach(['Djibouti', 'Ali Sabieh', 'Dikhil', 'Tadjourah', 'Obock', 'Arta'] as $regionOption)
                            <option value="{{ $regionOption }}">{{ $regionOption }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-flex gap-2">
                        <button type="button" wire:click="resetFilters" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Réinitialiser
                        </button>
                    </div>
                </div>
            </div>
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
                <div class="stats-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>{{ $statistics['pending_approval'] ?? 0 }}</h4>
                <p>En Attente</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h4>{{ $statistics['approved'] ?? 0 }}</h4>
                <p>Approuvés</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card small">
                <div class="stats-icon danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h4>{{ $statistics['rejected'] ?? 0 }}</h4>
                <p>Rejetés</p>
            </div>
        </div>
    </div>

    <!-- Tours List -->
    <div class="card">
        <div class="card-header">
            <h5>
                <i class="fas fa-list me-2"></i>
                Liste des Tours
                <span class="badge bg-secondary ms-2">{{ $tours->total() }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($tours->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Tour</th>
                                <th>Durée & Prix</th>
                                <th>Région</th>
                                <th>Statut</th>
                                <th>Participants</th>
                                <th>Réservations</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tours as $tour)
                                <tr wire:key="tour-{{ $tour->id }}">
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
                                            @if($tour->duration_hours)
                                                <span class="badge bg-info">{{ $tour->duration_hours }}h</span>
                                                <br>
                                            @endif
                                            <strong>{{ number_format($tour->price, 0, ',', ' ') }} DJF</strong>
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
                                        {!! $tour->status_badge !!}
                                    </td>
                                    <td>
                                        <div class="text-start">
                                            <strong>{{ $tour->current_participants ?? 0 }}</strong>
                                            <span class="text-muted">/ {{ $tour->max_participants ?? '∞' }}</span>
                                            <br>
                                            <small class="text-muted">
                                                @if($tour->max_participants)
                                                    {{ round(($tour->current_participants / $tour->max_participants) * 100) }}% rempli
                                                @else
                                                    capacité illimitée
                                                @endif
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-start">
                                            <strong>{{ $tour->reservations()->count() }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                {{ $tour->confirmedReservations()->count() }} confirmées
                                            </small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('operator.tours.show', $tour) }}"
                                               class="btn btn-sm btn-outline-primary"
                                               title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if(in_array($tour->status, ['draft', 'rejected', 'active']))
                                                <a href="{{ route('operator.tours.edit', $tour) }}"
                                                   class="btn btn-sm btn-outline-warning"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endif
                                            @if(in_array($tour->status, ['draft', 'rejected']))
                                                <button type="button"
                                                        wire:click="deleteTour({{ $tour->id }})"
                                                        wire:confirm="Êtes-vous sûr de vouloir supprimer ce tour ? Cette action est irréversible."
                                                        class="btn btn-sm btn-outline-danger"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
                        @if($search || $status || $duration || $region)
                            Modifiez vos filtres pour voir plus de tours
                        @else
                            Créez votre premier tour guidé pour commencer
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
