<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Informations Générales</h5>
                    </div>
                    <div class="card-body">
                        <!-- Champs pour les traductions (title, description, etc.) -->
                        <div class="mb-3">
                            <label class="form-label">Titre (FR)</label>
                            <input type="text" class="form-control" wire:model="translations.fr.title">
                            @error('translations.fr.title') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description (FR)</label>
                            <textarea class="form-control" rows="5" wire:model="translations.fr.description"></textarea>
                            @error('translations.fr.description') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <!-- ... autres langues si nécessaire -->
                    </div>
                </div>

                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title">Détails du Tour</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de début</label>
                                <input type="date" class="form-control" wire:model="tour.start_date">
                                @error('tour.start_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date de fin</label>
                                <input type="date" class="form-control" wire:model="tour.end_date">
                                @error('tour.end_date') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Heure de début</label>
                                <input type="time" class="form-control" wire:model="tour.start_time">
                                @error('tour.start_time') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Heure de fin</label>
                                <input type="time" class="form-control" wire:model="tour.end_time">
                                @error('tour.end_time') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Adresse du point de rendez-vous</label>
                            <input type="text" class="form-control" wire:model="tour.meeting_point_address">
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Organisation</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Statut</label>
                            <select class="form-select" wire:model="tour.status">
                                <option value="active">Actif</option>
                                <option value="suspended">Suspendu</option>
                                <option value="archived">Archivé</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tour Opérateur</label>
                            <select class="form-select" wire:model="tour.tour_operator_id">
                                <option value="">Sélectionner...</option>
                                @foreach($tourOperators as $operator)
                                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                @endforeach
                            </select>
                            @error('tour.tour_operator_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix</label>
                            <input type="number" step="any" class="form-control" wire:model="tour.price">
                            @error('tour.price') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Participants max.</label>
                            <input type="number" class="form-control" wire:model="tour.max_participants">
                            @error('tour.max_participants') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Niveau de difficulté</label>
                            <select class="form-select" wire:model="tour.difficulty_level">
                                <option value="easy">Facile</option>
                                <option value="moderate">Modéré</option>
                                <option value="difficult">Difficile</option>
                                <option value="expert">Expert</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" wire:model="tour.is_featured">
                            <label class="form-check-label" for="is_featured">Mettre en avant</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Sauvegarder</button>
            <a href="{{ route('tours.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>
