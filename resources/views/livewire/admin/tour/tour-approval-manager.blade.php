<div>
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-clock me-2"></i>En Attente</h5>
                    <h2 class="mb-0">{{ $statistics['pending'] }}</h2>
                    <small>Tours à examiner</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-check-circle me-2"></i>Approuvés</h5>
                    <h2 class="mb-0">{{ $statistics['approved'] }}</h2>
                    <small>Tours approuvés</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-times-circle me-2"></i>Rejetés</h5>
                    <h2 class="mb-0">{{ $statistics['rejected'] }}</h2>
                    <small>Tours rejetés</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Rechercher un tour...">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Statut</label>
                    <select wire:model.live="filterStatus" class="form-select">
                        <option value="all">Tous</option>
                        <option value="pending_approval">En attente</option>
                        <option value="approved">Approuvés</option>
                        <option value="rejected">Rejetés</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Tours Table --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Tours Soumis pour Approbation</h5>
        </div>
        <div class="card-body">
            @if($tours->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Tour Operator</th>
                                <th>Créé par</th>
                                <th>Soumis le</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tours as $tour)
                                <tr>
                                    <td>
                                        <strong>{{ $tour->title }}</strong>
                                        @if($tour->featuredImage)
                                            <br><small class="text-muted"><i class="fas fa-image me-1"></i>Image présente</small>
                                        @endif
                                    </td>
                                    <td>{{ $tour->tourOperator->name ?? 'N/A' }}</td>
                                    <td>
                                        @if($tour->createdBy)
                                            {{ $tour->createdBy->name }}<br>
                                            <small class="text-muted">{{ $tour->createdBy->email }}</small>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($tour->submitted_at)
                                            {{ $tour->submitted_at->format('d/m/Y') }}<br>
                                            <small class="text-muted">{{ $tour->submitted_at->format('H:i') }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{!! $tour->status_badge !!}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.tours.show', $tour->id) }}" class="btn btn-info" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($tour->status === 'pending_approval')
                                                <button wire:click="openApprovalModal({{ $tour->id }})" class="btn btn-success" title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button wire:click="openRejectionModal({{ $tour->id }})" class="btn btn-danger" title="Rejeter">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $tours->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Aucun tour trouvé.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Approval Modal --}}
    @if($showApprovalModal && $selectedTour)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title"><i class="fas fa-check-circle me-2"></i>Approuver le Tour</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeApprovalModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            Vous êtes sur le point d'approuver ce tour. Il sera visible publiquement après approbation.
                        </div>

                        <h6 class="mb-3"><strong>Détails du Tour</strong></h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th style="width: 30%;">Titre</th>
                                <td>{{ $selectedTour->title }}</td>
                            </tr>
                            <tr>
                                <th>Tour Operator</th>
                                <td>{{ $selectedTour->tourOperator->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Créé par</th>
                                <td>
                                    @if($selectedTour->createdBy)
                                        {{ $selectedTour->createdBy->name }} ({{ $selectedTour->createdBy->email }})
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Prix</th>
                                <td>{{ $selectedTour->formatted_price }}</td>
                            </tr>
                            <tr>
                                <th>Durée</th>
                                <td>{{ $selectedTour->formatted_duration }}</td>
                            </tr>
                            <tr>
                                <th>Date de soumission</th>
                                <td>{{ $selectedTour->submitted_at ? $selectedTour->submitted_at->format('d/m/Y à H:i') : 'N/A' }}</td>
                            </tr>
                        </table>

                        <h6 class="mt-3"><strong>Description</strong></h6>
                        <p class="text-muted">{{ $selectedTour->short_description ?: $selectedTour->description }}</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeApprovalModal">Annuler</button>
                        <button type="button" class="btn btn-success" wire:click="approveTour">
                            <i class="fas fa-check me-2"></i>Approuver le Tour
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Rejection Modal --}}
    @if($showRejectionModal && $selectedTour)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Rejeter le Tour</h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeRejectionModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Vous êtes sur le point de rejeter ce tour. L'opérateur recevra un email avec la raison du rejet.
                        </div>

                        <h6 class="mb-3"><strong>Détails du Tour</strong></h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th style="width: 30%;">Titre</th>
                                <td>{{ $selectedTour->title }}</td>
                            </tr>
                            <tr>
                                <th>Tour Operator</th>
                                <td>{{ $selectedTour->tourOperator->name ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th>Créé par</th>
                                <td>
                                    @if($selectedTour->createdBy)
                                        {{ $selectedTour->createdBy->name }} ({{ $selectedTour->createdBy->email }})
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <div class="mb-3">
                            <label class="form-label"><strong>Raison du Rejet <span class="text-danger">*</span></strong></label>
                            <textarea wire:model="rejectionReason" class="form-control @error('rejectionReason') is-invalid @enderror" rows="4" placeholder="Expliquez pourquoi ce tour est rejeté... (minimum 10 caractères)"></textarea>
                            @error('rejectionReason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">L'opérateur recevra cette raison par email.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeRejectionModal">Annuler</button>
                        <button type="button" class="btn btn-danger" wire:click="rejectTour">
                            <i class="fas fa-times me-2"></i>Rejeter le Tour
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
