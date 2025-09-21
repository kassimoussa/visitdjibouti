<div>
    <div class="container-fluid">
        <!-- En-tête avec bouton d'ajout -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Gestion des Tours</h1>
            <button type="button" class="btn btn-primary" wire:click="openCreateModal">
                <i class="fas fa-plus-circle me-1"></i> Nouveau Tour
            </button>
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
                                <th>Tour</th>
                                <th>Opérateur</th>
                                <th>Type</th>
                                <th>Difficulté</th>
                                <th>Prix</th>
                                <th>Statut</th>
                                <th>Créneaux</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tours as $tour)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <h6 class="mb-0">{{ $tour->title }}</h6>
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
                                        <span class="badge bg-primary">{{ $tour->schedules->count() }}</span>
                                        @if($tour->activeSchedules->count() > 0)
                                            <small class="text-success">{{ $tour->activeSchedules->count() }} actifs</small>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                wire:click="openScheduleModal({{ $tour->id }})"
                                                title="Gérer les créneaux">
                                                <i class="fas fa-calendar-plus"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                wire:click="openEditModal({{ $tour->id }})"
                                                title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
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

    <!-- Modal de création/édition de tour -->
    <div class="modal fade @if($showCreateModal || $showEditModal) show @endif"
         style="display: @if($showCreateModal || $showEditModal) block @else none @endif"
         tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        @if($showCreateModal) Nouveau Tour @else Modifier le Tour @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModals"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="@if($showCreateModal) createTour @else updateTour @endif">
                        <!-- Informations de base -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="tour_operator_id" class="form-label">Opérateur de tour *</label>
                                <select wire:model="tour_operator_id" class="form-select @error('tour_operator_id') is-invalid @enderror">
                                    <option value="">Sélectionner un opérateur</option>
                                    @foreach($tourOperators as $operator)
                                        <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                    @endforeach
                                </select>
                                @error('tour_operator_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="type" class="form-label">Type de tour *</label>
                                <select wire:model.live="type" class="form-select @error('type') is-invalid @enderror">
                                    <option value="poi">Visite POI</option>
                                    <option value="event">Événement</option>
                                    <option value="mixed">Circuit mixte</option>
                                    <option value="cultural">Culturel</option>
                                    <option value="adventure">Aventure</option>
                                    <option value="nature">Nature</option>
                                    <option value="gastronomic">Gastronomique</option>
                                </select>
                                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <!-- Cible du tour -->
                        @if($type === 'poi' || $type === 'event')
                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label for="target_id" class="form-label">
                                    @if($type === 'poi') Point d'Intérêt @else Événement @endif cible *
                                </label>
                                <select wire:model="target_id" class="form-select @error('target_id') is-invalid @enderror">
                                    <option value="">
                                        Sélectionner @if($type === 'poi') un POI @else un événement @endif
                                    </option>
                                    @if($type === 'poi')
                                        @foreach($pois as $poi)
                                            <option value="{{ $poi->id }}">{{ $poi->title }}</option>
                                        @endforeach
                                    @else
                                        @foreach($events as $event)
                                            <option value="{{ $event->id }}">{{ $event->title }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('target_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                        @endif

                        <!-- Traductions -->
                        <div class="mb-4">
                            <h6>Traductions</h6>
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#fr-tab">Français</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#en-tab">English</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" data-bs-toggle="tab" data-bs-target="#ar-tab">العربية</button>
                                </li>
                            </ul>
                            <div class="tab-content border border-top-0 p-3">
                                @foreach(['fr', 'en', 'ar'] as $locale)
                                <div class="tab-pane fade @if($locale === 'fr') show active @endif" id="{{ $locale }}-tab">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">Titre @if($locale === 'fr') * @endif</label>
                                            <input wire:model="translations.{{ $locale }}.title" type="text"
                                                class="form-control @error('translations.'.$locale.'.title') is-invalid @enderror">
                                            @error('translations.'.$locale.'.title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Description courte</label>
                                            <textarea wire:model="translations.{{ $locale }}.short_description"
                                                class="form-control" rows="2"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Description @if($locale === 'fr') * @endif</label>
                                            <textarea wire:model="translations.{{ $locale }}.description"
                                                class="form-control @error('translations.'.$locale.'.description') is-invalid @enderror"
                                                rows="4"></textarea>
                                            @error('translations.'.$locale.'.description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Paramètres du tour -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <label for="difficulty_level" class="form-label">Difficulté</label>
                                <select wire:model="difficulty_level" class="form-select">
                                    <option value="easy">Facile</option>
                                    <option value="moderate">Modéré</option>
                                    <option value="difficult">Difficile</option>
                                    <option value="expert">Expert</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="price" class="form-label">Prix</label>
                                <input wire:model="price" type="number" step="0.01" min="0" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="min_participants" class="form-label">Min participants</label>
                                <input wire:model="min_participants" type="number" min="1" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label for="max_participants" class="form-label">Max participants</label>
                                <input wire:model="max_participants" type="number" min="1" class="form-control">
                            </div>
                        </div>

                        <!-- Options -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input wire:model="is_featured" type="checkbox" class="form-check-input" id="is_featured">
                                    <label class="form-check-label" for="is_featured">Mis en avant</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check">
                                    <input wire:model="weather_dependent" type="checkbox" class="form-check-input" id="weather_dependent">
                                    <label class="form-check-label" for="weather_dependent">Dépendant météo</label>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="status" class="form-label">Statut</label>
                                <select wire:model="status" class="form-select">
                                    <option value="active">Actif</option>
                                    <option value="suspended">Suspendu</option>
                                    <option value="archived">Archivé</option>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModals">Annuler</button>
                    <button type="button" class="btn btn-primary"
                        wire:click="@if($showCreateModal) createTour @else updateTour @endif">
                        @if($showCreateModal) Créer @else Modifier @endif
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de gestion des créneaux -->
    <div class="modal fade @if($showScheduleModal) show @endif"
         style="display: @if($showScheduleModal) block @else none @endif"
         tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Ajouter un Créneau</h5>
                    <button type="button" class="btn-close" wire:click="closeModals"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="createSchedule">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="schedule_start_date" class="form-label">Date de début *</label>
                                <input wire:model="schedule_start_date" type="date" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="schedule_end_date" class="form-label">Date de fin</label>
                                <input wire:model="schedule_end_date" type="date" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="schedule_start_time" class="form-label">Heure de début</label>
                                <input wire:model="schedule_start_time" type="time" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="schedule_end_time" class="form-label">Heure de fin</label>
                                <input wire:model="schedule_end_time" type="time" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label for="schedule_available_spots" class="form-label">Places disponibles *</label>
                                <input wire:model="schedule_available_spots" type="number" min="1" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label for="schedule_guide_name" class="form-label">Nom du guide</label>
                                <input wire:model="schedule_guide_name" type="text" class="form-control">
                            </div>
                            <div class="col-12">
                                <label for="schedule_special_notes" class="form-label">Notes spéciales</label>
                                <textarea wire:model="schedule_special_notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModals">Annuler</button>
                    <button type="button" class="btn btn-primary" wire:click="createSchedule">Créer le Créneau</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Backdrop pour les modals -->
    @if($showCreateModal || $showEditModal || $showScheduleModal)
        <div class="modal-backdrop fade show"></div>
    @endif
</div>