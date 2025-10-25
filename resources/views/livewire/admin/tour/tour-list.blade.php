<div>
    <div class="container-fluid">
        <!-- En-tête avec bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Liste des Tours</h1>
            <a href="{{ route('tours.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Nouveau Tour
            </a>
        </div>

        <!-- Messages Flash -->
        @if (session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if (session()->has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Filtres -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="search" class="form-label">Recherche</label>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                            id="search" placeholder="Rechercher un tour...">
                    </div>
                    <div class="col-md-2">
                        <label for="statusFilter" class="form-label">Statut</label>
                        <select wire:model.live="statusFilter" class="form-select" id="statusFilter">
                            <option value="">Tous les statuts</option>
                            <option value="active">Actif</option>
                            <option value="suspended">Suspendu</option>
                            <option value="archived">Archivé</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="operatorFilter" class="form-label">Opérateur</label>
                        <select wire:model.live="operatorFilter" class="form-select" id="operatorFilter">
                            <option value="">Tous les opérateurs</option>
                            @foreach($tourOperators as $operator)
                                <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="typeFilter" class="form-label">Type</label>
                        <select wire:model.live="typeFilter" class="form-select" id="typeFilter">
                            <option value="">Tous les types</option>
                            <option value="poi">Visite POI</option>
                            <option value="event">Événement</option>
                            <option value="mixed">Circuit mixte</option>
                            <option value="cultural">Culturel</option>
                            <option value="adventure">Aventure</option>
                            <option value="nature">Nature</option>
                            <option value="gastronomic">Gastronomique</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="difficultyFilter" class="form-label">Difficulté</label>
                        <select wire:model.live="difficultyFilter" class="form-select" id="difficultyFilter">
                            <option value="">Toutes</option>
                            <option value="easy">Facile</option>
                            <option value="moderate">Modéré</option>
                            <option value="difficult">Difficile</option>
                            <option value="expert">Expert</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des tours -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50" style="cursor: pointer;" wire:click="sortBy('id')">
                                    #
                                    @if($sortField === 'id')
                                        @if($sortDirection === 'asc')
                                            <i class="fas fa-sort-up"></i>
                                        @else
                                            <i class="fas fa-sort-down"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th style="cursor: pointer;" wire:click="sortBy('title')">
                                    Tour
                                    @if($sortField === 'title')
                                        @if($sortDirection === 'asc')
                                            <i class="fas fa-sort-up"></i>
                                        @else
                                            <i class="fas fa-sort-down"></i>
                                        @endif
                                    @else
                                        <i class="fas fa-sort text-muted"></i>
                                    @endif
                                </th>
                                <th>Opérateur</th>
                                <th>Type</th>
                                <th>Difficulté</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tours as $tour)
                                <tr>
                                    <td>{{ $tour->id }}</td>
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
                                                    <i class="fas fa-map-signs text-muted"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <h6 class="mb-0">
                                                    <a href="{{ route('tours.show', $tour) }}" class="text-decoration-none">
                                                        {{ $tour->title }}
                                                    </a>
                                                </h6>
                                                <small class="text-muted">{{ Str::limit($tour->short_description, 50) }}</small>
                                                @if($tour->is_featured)
                                                    <span class="badge bg-warning ms-1">Mis en avant</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $tour->tourOperator->name }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $tour->type_label }}</span>
                                    </td>
                                    <td>
                                        <span class="badge
                                            @if($tour->difficulty_level === 'easy') bg-success
                                            @elseif($tour->difficulty_level === 'moderate') bg-warning
                                            @elseif($tour->difficulty_level === 'difficult') bg-danger
                                            @else bg-dark @endif">
                                            {{ $tour->difficulty_label }}
                                        </span>
                                    </td>
                                    <td>{{ $tour->formatted_price }}</td>
                                    <td>
                                        <span class="badge
                                            @if($tour->status === 'active') bg-success
                                            @elseif($tour->status === 'suspended') bg-warning
                                            @else bg-secondary @endif">
                                            {{ ucfirst($tour->status) }}
                                        </span>
                                    </td> 
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tours.show', $tour) }}"
                                               class="btn btn-sm btn-outline-info"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tours.edit', $tour) }}"
                                               class="btn btn-sm btn-outline-secondary"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                wire:click="deleteTour({{ $tour->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir supprimer ce tour ?"
                                                title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-map-signs fa-3x mb-3"></i>
                                            <p>Aucun tour trouvé</p>
                                            @if(empty($search) && empty($statusFilter) && empty($operatorFilter) && empty($typeFilter) && empty($difficultyFilter))
                                                <a href="{{ route('tours.create') }}" class="btn btn-primary">
                                                    <i class="fas fa-plus-circle me-1"></i> Créer le premier tour
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $tours->links() }}
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .table thead th[wire\:click] {
                user-select: none;
                transition: background-color 0.2s;
            }

            .table thead th[wire\:click]:hover {
                background-color: #e9ecef !important;
            }

            .table thead th i.fa-sort,
            .table thead th i.fa-sort-up,
            .table thead th i.fa-sort-down {
                font-size: 0.85em;
                margin-left: 5px;
            }
        </style>
    @endpush
</div>