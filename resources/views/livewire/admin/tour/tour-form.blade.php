<div>
    <form wire:submit.prevent="save">
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">
                            <i class="fas fa-language me-2"></i>Informations Générales
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Onglets pour les langues -->
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#tab-tour-fr" role="tab">
                                    FR
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#tab-tour-en" role="tab">
                                    EN
                                </a>
                            </li>
                        </ul>

                        <!-- Contenu des onglets -->
                        <div class="tab-content">
                            <!-- Français -->
                            <div class="tab-pane fade show active" id="tab-tour-fr" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Titre *</label>
                                    <input type="text" class="form-control @error('translations.fr.title') is-invalid @enderror"
                                           wire:model="translations.fr.title"
                                           placeholder="Titre du tour en français">
                                    @error('translations.fr.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description *</label>
                                    <textarea class="form-control @error('translations.fr.description') is-invalid @enderror"
                                              rows="8"
                                              wire:model="translations.fr.description"
                                              placeholder="Description détaillée du tour"></textarea>
                                    @error('translations.fr.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- English -->
                            <div class="tab-pane fade" id="tab-tour-en" role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Title</label>
                                    <input type="text" class="form-control @error('translations.en.title') is-invalid @enderror"
                                           wire:model="translations.en.title"
                                           placeholder="Tour title in English">
                                    @error('translations.en.title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control @error('translations.en.description') is-invalid @enderror"
                                              rows="8"
                                              wire:model="translations.en.description"
                                              placeholder="Detailed tour description"></textarea>
                                    @error('translations.en.description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
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
                            <select class="form-select" wire:model.blur="tour.tour_operator_id">
                                <option value="">Sélectionner...</option>
                                @foreach($tourOperators as $operator)
                                    <option value="{{ $operator->id }}">{{ $operator->name }}</option>
                                @endforeach
                            </select>
                            @error('tour.tour_operator_id') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Prix</label>
                            <input type="number" step="any" class="form-control" wire:model.blur="tour.price">
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

                <!-- Image principale -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-image me-2"></i>Image principale
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary"
                            wire:click="openFeaturedImageSelector">
                            <i class="fas fa-images me-1"></i>Choisir une image
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($featuredImageId && $allMedia)
                            <div class="text-center">
                                @php $featuredImage = collect($allMedia)->firstWhere('id', $featuredImageId); @endphp
                                @if ($featuredImage)
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ asset($featuredImage->thumbnail_path ?? $featuredImage->path) }}"
                                            alt="{{ $featuredImage->original_name }}"
                                            class="img-fluid rounded border shadow-sm"
                                            style="max-height: 200px; max-width: 100%;">
                                        <button type="button"
                                            class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                            wire:click="$set('featuredImageId', null)"
                                            title="Supprimer l'image">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            {{ $featuredImage->original_name }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4 border rounded" style="border: 2px dashed #dee2e6;">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Aucune image sélectionnée</p>
                                <button type="button" class="btn btn-outline-primary"
                                    wire:click="openFeaturedImageSelector">
                                    <i class="fas fa-plus me-1"></i>Sélectionner une image
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Galerie d'images -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-images me-2"></i>Galerie d'images ({{ count($selectedMedia) }})
                        </h6>
                        <button type="button" class="btn btn-sm btn-success"
                            wire:click="openGallerySelector">
                            <i class="fas fa-plus me-1"></i>Ajouter des images
                        </button>
                    </div>
                    <div class="card-body">
                        @if (count($selectedMedia) > 0 && $allMedia)
                            <div class="row g-3">
                                @foreach ($selectedMedia as $index => $mediaId)
                                    @php $mediaItem = collect($allMedia)->firstWhere('id', $mediaId); @endphp
                                    @if ($mediaItem)
                                        <div class="col-6 col-md-4 col-lg-3">
                                            <div class="position-relative">
                                                <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                    alt="{{ $mediaItem->original_name }}"
                                                    class="img-fluid rounded border shadow-sm"
                                                    style="height: 120px; width: 100%; object-fit: cover;">
                                                <div class="position-absolute top-0 end-0 m-2 d-flex gap-1">
                                                    <button type="button"
                                                        class="btn btn-sm btn-light btn-sm"
                                                        title="Définir comme image principale"
                                                        wire:click="$set('featuredImageId', {{ $mediaId }})">
                                                        <i class="fas fa-star {{ $featuredImageId == $mediaId ? 'text-warning' : 'text-muted' }}"></i>
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-danger btn-sm"
                                                        title="Supprimer de la galerie"
                                                        wire:click="removeMediaFromGallery({{ $index }})">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div>
                                                <div class="mt-2 text-center">
                                                    <small class="text-muted d-block text-truncate">
                                                        {{ $mediaItem->original_name }}
                                                    </small>
                                                    <span class="badge bg-primary">{{ $index + 1 }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4 border rounded" style="border: 2px dashed #dee2e6;">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Aucune image dans la galerie</p>
                                <button type="button" class="btn btn-outline-success"
                                    wire:click="openGallerySelector">
                                    <i class="fas fa-plus me-1"></i>Ajouter des images
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <button type="submit" class="btn btn-primary">Sauvegarder</button>
            <a href="{{ route('tours.index') }}" class="btn btn-secondary">Annuler</a>
        </div>
    </form>

    <!-- Modal de sélection de médias -->
    @livewire('admin.media-selector-modal')
</div>

@push('styles')
<style>
    .border-dashed {
        border-style: dashed !important;
    }
</style>
@endpush
