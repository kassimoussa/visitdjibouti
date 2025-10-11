@extends('operator.layouts.app')

@section('title', 'Modifier l\'Événement')
@section('page-title', 'Modifier l\'Événement')

@section('content')
<div class="fade-in">
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
                <li class="breadcrumb-item">
                    <a href="{{ route('operator.events.show', $event) }}">
                        {{ Str::limit($event->title, 30) }}
                    </a>
                </li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </nav>
        <h2 class="mb-1">Modifier l'Événement</h2>
        <p class="text-muted">{{ $event->title }}</p>
    </div>

    <form action="{{ route('operator.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                <!-- Multilingual Content -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-language me-2"></i>
                            Contenu Multilingue
                        </h5>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#fr-tab" type="button">
                                    🇫🇷 Français
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#en-tab" type="button">
                                    🇬🇧 English
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content">
                            @foreach(['fr' => 'Français', 'en' => 'English'] as $locale => $language)
                                @php
                                    $translation = $event->translations->where('locale', $locale)->first();
                                @endphp
                                <div class="tab-pane fade {{ $locale == 'fr' ? 'show active' : '' }}" id="{{ $locale }}-tab">
                                    <div class="mb-3">
                                        <label class="form-label">Titre ({{ $language}}) *</label>
                                        <input type="text"
                                               class="form-control @error("translations.$locale.title") is-invalid @enderror"
                                               name="translations[{{ $locale }}][title]"
                                               value="{{ old("translations.$locale.title", $translation->title ?? '') }}"
                                               required>
                                        @error("translations.$locale.title")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description courte ({{ $language }})</label>
                                        <textarea class="form-control @error("translations.$locale.short_description") is-invalid @enderror"
                                                  name="translations[{{ $locale }}][short_description]"
                                                  rows="2">{{ old("translations.$locale.short_description", $translation->short_description ?? '') }}</textarea>
                                        @error("translations.$locale.short_description")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Description complète ({{ $language }})</label>
                                        <textarea class="form-control @error("translations.$locale.description") is-invalid @enderror"
                                                  name="translations[{{ $locale }}][description]"
                                                  rows="6">{{ old("translations.$locale.description", $translation->description ?? '') }}</textarea>
                                        @error("translations.$locale.description")
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-address-card me-2"></i>
                            Informations de Contact
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_email" class="form-label">Email de contact</label>
                                <input type="email"
                                       class="form-control @error('contact_email') is-invalid @enderror"
                                       id="contact_email"
                                       name="contact_email"
                                       value="{{ old('contact_email', $event->contact_email) }}">
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_phone" class="form-label">Téléphone de contact</label>
                                <input type="tel"
                                       class="form-control @error('contact_phone') is-invalid @enderror"
                                       id="contact_phone"
                                       name="contact_phone"
                                       value="{{ old('contact_phone', $event->contact_phone) }}">
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="website_url" class="form-label">Site web</label>
                                <input type="url"
                                       class="form-control @error('website_url') is-invalid @enderror"
                                       id="website_url"
                                       name="website_url"
                                       value="{{ old('website_url', $event->website_url) }}"
                                       placeholder="https://">
                                @error('website_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="organizer" class="form-label">Organisateur</label>
                                <input type="text"
                                       class="form-control @error('organizer') is-invalid @enderror"
                                       id="organizer"
                                       name="organizer"
                                       value="{{ old('organizer', $event->organizer) }}">
                                @error('organizer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5>
                            <i class="fas fa-tags me-2"></i>
                            Tarification
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="price" class="form-label">Prix (DJF)</label>
                                <input type="number"
                                       class="form-control @error('price') is-invalid @enderror"
                                       id="price"
                                       name="price"
                                       value="{{ old('price', $event->price) }}"
                                       min="0"
                                       step="100">
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-8 mb-3">
                                <label for="max_participants" class="form-label">Nombre maximum de participants</label>
                                <input type="number"
                                       class="form-control @error('max_participants') is-invalid @enderror"
                                       id="max_participants"
                                       name="max_participants"
                                       value="{{ old('max_participants', $event->max_participants) }}"
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
                                <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Publié</option>
                                <option value="cancelled" {{ old('status', $event->status) == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Current Image -->
                @if($event->featuredImage)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5>
                                <i class="fas fa-image me-2"></i>
                                Image actuelle
                            </h5>
                        </div>
                        <div class="card-body">
                            <img src="{{ $event->featuredImage->getImageUrl() }}"
                                 alt="{{ $event->title }}"
                                 class="img-fluid rounded mb-2">
                            <small class="text-muted">Téléchargez une nouvelle image pour la remplacer</small>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Enregistrer les modifications
                            </button>
                            <a href="{{ route('operator.events.show', $event) }}" class="btn btn-outline-secondary">
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
