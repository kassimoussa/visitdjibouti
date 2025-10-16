@extends('operator.layouts.app')

@section('title', 'Créer un Tour')
@section('page-title', 'Nouveau Tour')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('operator.tours.index') }}">
                        <i class="fas fa-route me-1"></i>
                        Tours
                    </a>
                </li>
                <li class="breadcrumb-item active">Nouveau</li>
            </ol>
        </nav>
        <h2 class="mb-1">Créer un Nouveau Tour</h2>
        <p class="text-muted">Créez un circuit touristique pour vos clients.</p>
    </div>

    <form action="{{ route('operator.tours.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-info-circle me-2"></i>
                            Informations Générales
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label for="title" class="form-label">Titre du tour (en Français) *</label>
                                <input type="text"
                                       class="form-control @error('translations.fr.title') is-invalid @enderror"
                                       id="title"
                                       name="translations[fr][title]"
                                       value="{{ old('translations.fr.title') }}"
                                       required
                                       placeholder="Ex: Découverte du Désert de Grand Bara">
                                @error('translations.fr.title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-4">
                                <label for="description" class="form-label">Description complète (en Français)</label>
                                <textarea class="form-control @error('translations.fr.description') is-invalid @enderror"
                                          id="description"
                                          name="translations[fr][description]"
                                          rows="6"
                                          placeholder="Description détaillée du tour...">{{ old('translations.fr.description') }}</textarea>
                                @error('translations.fr.description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="type" class="form-label">Type de Tour *</label>
                                <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                    <option value="">Sélectionner un type</option>
                                    <option value="poi" {{ old('type') == 'poi' ? 'selected' : '' }}>Visite de site</option>
                                    <option value="event" {{ old('type') == 'event' ? 'selected' : '' }}>Accompagnement événement</option>
                                    <option value="mixed" {{ old('type') == 'mixed' ? 'selected' : '' }}>Circuit mixte</option>
                                    <option value="cultural" {{ old('type') == 'cultural' ? 'selected' : '' }}>Culturel</option>
                                    <option value="adventure" {{ old('type') == 'adventure' ? 'selected' : '' }}>Aventure</option>
                                    <option value="nature" {{ old('type') == 'nature' ? 'selected' : '' }}>Nature</option>
                                    <option value="gastronomic" {{ old('type') == 'gastronomic' ? 'selected' : '' }}>Gastronomique</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                             <div class="col-md-6 mb-4">
                                <label for="difficulty_level" class="form-label">Niveau de difficulté</label>
                                <select class="form-control @error('difficulty_level') is-invalid @enderror" id="difficulty_level" name="difficulty_level">
                                    <option value="easy" {{ old('difficulty_level') == 'easy' ? 'selected' : '' }}>Facile</option>
                                    <option value="moderate" {{ old('difficulty_level') == 'moderate' ? 'selected' : '' }}>Modéré</option>
                                    <option value="difficult" {{ old('difficulty_level') == 'difficult' ? 'selected' : '' }}>Difficile</option>
                                    <option value="expert" {{ old('difficulty_level') == 'expert' ? 'selected' : '' }}>Expert</option>
                                </select>
                                @error('difficulty_level')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Details -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-list-alt me-2"></i>
                            Détails du Tour
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="price" class="form-label">Prix (DJF)</label>
                                <input type="number"
                                       class="form-control @error('price') is-invalid @enderror"
                                       id="price"
                                       name="price"
                                       value="{{ old('price', 0) }}"
                                       min="0"
                                       step="100">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration_hours" class="form-label">Durée (en heures)</label>
                                <input type="number"
                                       class="form-control @error('duration_hours') is-invalid @enderror"
                                       id="duration_hours"
                                       name="duration_hours"
                                       value="{{ old('duration_hours') }}"
                                       min="1">
                                @error('duration_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                             <div class="col-md-12 mb-3">
                                <label for="max_participants" class="form-label">Nombre maximum de participants</label>
                                <input type="number"
                                       class="form-control @error('max_participants') is-invalid @enderror"
                                       id="max_participants"
                                       name="max_participants"
                                       value="{{ old('max_participants') }}"
                                       min="1">
                                @error('max_participants')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Laisser vide pour une capacité illimitée</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Publication Settings -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-cog me-2"></i>
                            Publication
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="form-control" id="status" name="status">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Créer le Tour
                            </button>
                            <a href="{{ route('operator.tours.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
