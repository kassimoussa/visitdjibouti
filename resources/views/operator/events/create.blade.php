@extends('operator.layouts.app')

@section('title', 'Créer un Événement')
@section('page-title', 'Nouvel Événement')

@section('content')
<div class="operator-fade-in">
    <!-- Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb" class="mb-2">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="{{ route('operator.events.index') }}">
                        <i class="fas fa-calendar-alt me-1"></i>
                        Événements
                    </a>
                </li>
                <li class="breadcrumb-item active">Nouveau</li>
            </ol>
        </nav>
        <h2 class="mb-1">Créer un Nouvel Événement</h2>
        <p class="text-muted">Organisez un événement touristique attractif pour vos clients</p>
    </div>

    <form action="{{ route('operator.events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="operator-card mb-4">
                    <div class="operator-card-header">
                        <h5>
                            <i class="fas fa-info-circle me-2"></i>
                            Informations Générales
                        </h5>
                    </div>
                    <div class="operator-card-body">
                        <div class="row">
                            <div class="col-12 mb-4">
                                <label for="title" class="form-label">Titre de l'événement *</label>
                                <input type="text"
                                       class="operator-form-control @error('title') is-invalid @enderror"
                                       id="title"
                                       name="title"
                                       value="{{ old('title') }}"
                                       required
                                       placeholder="Ex: Excursion au Lac Assal">
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-4">
                                <label for="short_description" class="form-label">Description courte</label>
                                <textarea class="operator-form-control @error('short_description') is-invalid @enderror"
                                          id="short_description"
                                          name="short_description"
                                          rows="2"
                                          placeholder="Résumé attrayant de votre événement (150 caractères max)">{{ old('short_description') }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Cette description apparaîtra dans les listes d'événements</small>
                            </div>

                            <div class="col-12 mb-4">
                                <label for="description" class="form-label">Description complète</label>
                                <textarea class="operator-form-control @error('description') is-invalid @enderror"
                                          id="description"
                                          name="description"
                                          rows="6"
                                          placeholder="Description détaillée de l'événement, activités incluses, informations pratiques...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="category_id" class="form-label">Catégorie</label>
                                <select class="operator-form-control @error('category_id') is-invalid @enderror"
                                        id="category_id"
                                        name="category_id">
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->translation(session('locale', 'fr'))->name ?? $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label for="region" class="form-label">Région</label>
                                <select class="operator-form-control @error('region') is-invalid @enderror"
                                        id="region"
                                        name="region">
                                    <option value="">Sélectionner une région</option>
                                    @foreach(['Djibouti', 'Ali Sabieh', 'Dikhil', 'Tadjourah', 'Obock', 'Arta'] as $region)
                                        <option value="{{ $region }}" {{ old('region') == $region ? 'selected' : '' }}>{{ $region }}</option>
                                    @endforeach
                                </select>
                                @error('region')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-4">
                                <label for="location" class="form-label">Lieu de l'événement</label>
                                <input type="text"
                                       class="operator-form-control @error('location') is-invalid @enderror"
                                       id="location"
                                       name="location"
                                       value="{{ old('location') }}"
                                       placeholder="Ex: Lac Assal, Centre de Djibouti">
                                @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="operator-card mb-4">
                    <div class="operator-card-header">
                        <h5>
                            <i class="fas fa-calendar-alt me-2"></i>
                            Dates et Horaires
                        </h5>
                    </div>
                    <div class="operator-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Date de début *</label>
                                <input type="date"
                                       class="operator-form-control @error('start_date') is-invalid @enderror"
                                       id="start_date"
                                       name="start_date"
                                       value="{{ old('start_date') }}"
                                       required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="start_time" class="form-label">Heure de début</label>
                                <input type="time"
                                       class="operator-form-control @error('start_time') is-invalid @enderror"
                                       id="start_time"
                                       name="start_time"
                                       value="{{ old('start_time') }}">
                                @error('start_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">Date de fin</label>
                                <input type="date"
                                       class="operator-form-control @error('end_date') is-invalid @enderror"
                                       id="end_date"
                                       name="end_date"
                                       value="{{ old('end_date') }}">
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Laisser vide si l'événement dure une seule journée</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_time" class="form-label">Heure de fin</label>
                                <input type="time"
                                       class="operator-form-control @error('end_time') is-invalid @enderror"
                                       id="end_time"
                                       name="end_time"
                                       value="{{ old('end_time') }}">
                                @error('end_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="operator-card mb-4">
                    <div class="operator-card-header">
                        <h5>
                            <i class="fas fa-tags me-2"></i>
                            Tarification
                        </h5>
                    </div>
                    <div class="operator-card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="price_adult" class="form-label">Prix Adulte (DJF) *</label>
                                <input type="number"
                                       class="operator-form-control @error('price_adult') is-invalid @enderror"
                                       id="price_adult"
                                       name="price_adult"
                                       value="{{ old('price_adult') }}"
                                       min="0"
                                       step="100"
                                       required>
                                @error('price_adult')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price_child" class="form-label">Prix Enfant (DJF)</label>
                                <input type="number"
                                       class="operator-form-control @error('price_child') is-invalid @enderror"
                                       id="price_child"
                                       name="price_child"
                                       value="{{ old('price_child') }}"
                                       min="0"
                                       step="100">
                                @error('price_child')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price_group" class="form-label">Prix Groupe (DJF)</label>
                                <input type="number"
                                       class="operator-form-control @error('price_group') is-invalid @enderror"
                                       id="price_group"
                                       name="price_group"
                                       value="{{ old('price_group') }}"
                                       min="0"
                                       step="100">
                                @error('price_group')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Prix pour groupes de 10+ personnes</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Publication Settings -->
                <div class="operator-card mb-4">
                    <div class="operator-card-header">
                        <h5>
                            <i class="fas fa-cog me-2"></i>
                            Publication
                        </h5>
                    </div>
                    <div class="operator-card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Statut</label>
                            <select class="operator-form-control" id="status" name="status">
                                <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié</option>
                            </select>
                            <small class="text-muted">Vous pouvez publier plus tard</small>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                <i class="fas fa-star text-warning me-1"></i>
                                Mettre en avant
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Image Upload -->
                <div class="operator-card mb-4">
                    <div class="operator-card-header">
                        <h5>
                            <i class="fas fa-image me-2"></i>
                            Image de couverture
                        </h5>
                    </div>
                    <div class="operator-card-body">
                        <div class="mb-3">
                            <input type="file"
                                   class="operator-form-control @error('featured_image') is-invalid @enderror"
                                   id="featured_image"
                                   name="featured_image"
                                   accept="image/*">
                            @error('featured_image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">JPG, PNG. Taille recommandée: 1200x600px</small>
                        </div>

                        <div id="image-preview" class="d-none">
                            <img id="preview-img" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>
                </div>

                <!-- Capacity Settings -->
                <div class="operator-card mb-4">
                    <div class="operator-card-header">
                        <h5>
                            <i class="fas fa-users me-2"></i>
                            Capacité
                        </h5>
                    </div>
                    <div class="operator-card-body">
                        <div class="mb-3">
                            <label for="max_participants" class="form-label">Nombre maximum de participants</label>
                            <input type="number"
                                   class="operator-form-control @error('max_participants') is-invalid @enderror"
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

                <!-- Action Buttons -->
                <div class="operator-card">
                    <div class="operator-card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="operator-btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Créer l'événement
                            </button>
                            <a href="{{ route('operator.events.index') }}" class="operator-btn btn-outline-secondary">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image preview
    const imageInput = document.getElementById('featured_image');
    const previewDiv = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    imageInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.classList.remove('d-none');
            };
            reader.readAsDataURL(file);
        } else {
            previewDiv.classList.add('d-none');
        }
    });

    // Auto-set end date to start date if not specified
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');

    startDate.addEventListener('change', function() {
        if (!endDate.value) {
            endDate.value = startDate.value;
        }
    });
});
</script>
@endsection