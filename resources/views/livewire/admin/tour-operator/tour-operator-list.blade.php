<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Opérateurs de Tour</h1>
        <a href="{{ route('tour-operators.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nouvel opérateur
        </a>
    </div>

    <!-- Messages de succès/erreur -->
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtres et recherche -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Recherche</label>
                    <input type="text" class="form-control" id="search" wire:model.live="search" placeholder="Nom, description...">
                </div>
                <div class="col-md-3">
                    <label for="filterFeatured" class="form-label">Mis en avant</label>
                    <select class="form-select" id="filterFeatured" wire:model.live="filterFeatured">
                        <option value="">Tous</option>
                        <option value="1">Oui</option>
                        <option value="0">Non</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="filterLocale" class="form-label">Langue d'affichage</label>
                    <select class="form-select" id="filterLocale" wire:model.live="filterLocale">
                        @foreach($availableLocales as $locale)
                            <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des opérateurs -->
    <div class="card">
        <div class="card-body">
            @if($tourOperators->count() > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Logo</th>
                                <th>Nom</th>
                                <th>Contact</th>
                                <th>Adresse</th>
                                <th>Statut</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tourOperators as $operator)
                                @php
                                    $translation = $operator->translations->firstWhere('locale', $filterLocale) 
                                        ?? $operator->translations->firstWhere('locale', 'fr') 
                                        ?? $operator->translations->first();
                                @endphp
                                <tr>
                                    <td>
                                        @if($operator->logo)
                                            <img src="{{ $operator->logo->thumbnail_url ?: $operator->logo->url }}" 
                                                 alt="Logo" class="rounded" style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="fas fa-building text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $translation->name ?? 'Sans nom' }}</strong>
                                            @if($operator->featured)
                                                <span class="badge bg-warning text-dark ms-1">
                                                    <i class="fas fa-star"></i> Mis en avant
                                                </span>
                                            @endif
                                        </div>
                                        @if($translation->description)
                                            <small class="text-muted">{{ Str::limit($translation->description, 100) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($operator->first_phone)
                                            <div><i class="fas fa-phone text-primary"></i> {{ $operator->first_phone }}</div>
                                        @endif
                                        @if($operator->first_email)
                                            <div><i class="fas fa-envelope text-primary"></i> {{ $operator->first_email }}</div>
                                        @endif
                                        @if($operator->website)
                                            <div>
                                                <i class="fas fa-globe text-primary"></i> 
                                                <a href="{{ $operator->website_url }}" target="_blank" class="text-decoration-none">
                                                    {{ $operator->website }}
                                                </a>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($translation->address_translated)
                                            <div>{{ Str::limit($translation->address_translated, 80) }}</div>
                                        @elseif($operator->address)
                                            <div class="text-muted">{{ Str::limit($operator->address, 80) }}</div>
                                        @endif
                                        @if($operator->latitude && $operator->longitude)
                                            <small class="text-success">
                                                <i class="fas fa-map-marker-alt"></i> 
                                                {{ number_format($operator->latitude, 4) }}, {{ number_format($operator->longitude, 4) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($operator->is_active)
                                            <span class="badge bg-success">Actif</span>
                                        @else
                                            <span class="badge bg-secondary">Inactif</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('tour-operators.show', $operator->id) }}" 
                                               class="btn btn-sm btn-outline-info" title="Voir détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tour-operators.edit', $operator->id) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-{{ $operator->is_active ? 'warning' : 'success' }}" 
                                                    wire:click="toggleStatus({{ $operator->id }})" 
                                                    title="{{ $operator->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $operator->is_active ? 'power-off' : 'power-off' }}"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-{{ $operator->featured ? 'secondary' : 'warning' }}" 
                                                    wire:click="toggleFeatured({{ $operator->id }})" 
                                                    title="{{ $operator->featured ? 'Retirer de la mise en avant' : 'Mettre en avant' }}">
                                                <i class="fas fa-star"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    wire:click="delete({{ $operator->id }})" 
                                                    onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet opérateur ?')" 
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center">
                    {{ $tourOperators->links() }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h5>Aucun opérateur trouvé</h5>
                    <p class="text-muted">Aucun opérateur ne correspond à vos critères de recherche.</p>
                    <a href="{{ route('tour-operators.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Créer le premier opérateur
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>