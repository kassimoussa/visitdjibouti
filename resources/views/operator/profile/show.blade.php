@extends('operator.layouts.app')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('content')
<div class="fade-in">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="mb-1">Mon Profil</h2>
        <p class="text-muted">Gérez vos informations personnelles et préférences de compte</p>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <!-- Personal Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-user me-2"></i>
                        Informations Personnelles
                    </h5>
                    <a href="{{ route('operator.profile.edit') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-edit me-1"></i>
                        Modifier
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('operator.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Avatar -->
                            <div class="col-12 mb-4">
                                <label class="form-label">Photo de profil</label>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($user->avatar)
                                            <img src="{{ Storage::url($user->avatar) }}"
                                                 alt="Avatar"
                                                 class="rounded-circle"
                                                 style="width: 80px; height: 80px; object-fit: cover;">
                                        @else
                                            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 80px; height: 80px;">
                                                <i class="fas fa-user fa-2x text-white"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <input type="file"
                                               class="form-control @error('avatar') is-invalid @enderror"
                                               name="avatar"
                                               accept="image/*">
                                        @error('avatar')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <small class="text-muted">JPG, PNG. Maximum 2MB.</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Nom complet *</label>
                                <input type="text"
                                       class="form-control @error('name') is-invalid @enderror"
                                       id="name"
                                       name="name"
                                       value="{{ old('name', $user->name) }}"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Adresse email *</label>
                                <input type="email"
                                       class="form-control @error('email') is-invalid @enderror"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Phone -->
                            <div class="col-md-6 mb-3">
                                <label for="phone_number" class="form-label">Numéro de téléphone</label>
                                <input type="tel"
                                       class="form-control @error('phone_number') is-invalid @enderror"
                                       id="phone_number"
                                       name="phone_number"
                                       value="{{ old('phone_number', $user->phone_number) }}"
                                       placeholder="+253 XX XX XX XX">
                                @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Position -->
                            <div class="col-md-6 mb-3">
                                <label for="position" class="form-label">Poste/Fonction</label>
                                <input type="text"
                                       class="form-control @error('position') is-invalid @enderror"
                                       id="position"
                                       name="position"
                                       value="{{ old('position', $user->position) }}"
                                       placeholder="Ex: Directeur, Guide, Manager">
                                @error('position')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Language Preference -->
                            <div class="col-md-6 mb-3">
                                <label for="language_preference" class="form-label">Langue préférée</label>
                                <select class="form-control @error('language_preference') is-invalid @enderror"
                                        id="language_preference"
                                        name="language_preference">
                                    <option value="fr" {{ old('language_preference', $user->language_preference) == 'fr' ? 'selected' : '' }}>Français</option>
                                    <option value="en" {{ old('language_preference', $user->language_preference) == 'en' ? 'selected' : '' }}>English</option>
                                </select>
                                @error('language_preference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Sauvegarder les modifications
                            </button>
                            <a href="{{ route('operator.profile.show') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Change Password -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-lock me-2"></i>
                        Modifier le Mot de Passe
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('operator.profile.password') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="current_password" class="form-label">Mot de passe actuel *</label>
                                <input type="password"
                                       class="form-control @error('current_password') is-invalid @enderror"
                                       id="current_password"
                                       name="current_password"
                                       required>
                                @error('current_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="password" class="form-label">Nouveau mot de passe *</label>
                                <input type="password"
                                       class="form-control @error('password') is-invalid @enderror"
                                       id="password"
                                       name="password"
                                       required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Minimum 8 caractères</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="password_confirmation" class="form-label">Confirmer le mot de passe *</label>
                                <input type="password"
                                       class="form-control"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       required>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>
                            Modifier le mot de passe
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Account Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-info-circle me-2"></i>
                        Informations du Compte
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Statut du compte</strong>
                        <br>
                        @if($user->is_active)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-danger">Inactif</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>Membre depuis</strong>
                        <br>
                        <span class="text-muted">{{ $user->created_at->format('d/m/Y') }}</span>
                    </div>

                    <div class="mb-3">
                        <strong>Dernière connexion</strong>
                        <br>
                        <span class="text-muted">
                            @if($user->last_login_at)
                                {{ $user->last_login_at->diffForHumans() }}
                            @else
                                Jamais
                            @endif
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>Tour Opérateur</strong>
                        <br>
                        <a href="{{ route('operator.tour-operator.show') }}" class="text-decoration-none">
                            {{ $user->tourOperator->getTranslatedName(session('locale', 'fr')) }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Summary -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <i class="fas fa-chart-pie me-2"></i>
                        Activité Récente
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h4 text-primary mb-1">{{ $user->managedEvents()->count() }}</div>
                            <small class="text-muted">Événements</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-success mb-1">{{ $user->managedReservations()->count() }}</div>
                            <small class="text-muted">Réservations</small>
                        </div>
                        <div class="col-4">
                            <div class="h4 text-info mb-1">{{ $user->managedTours()->count() }}</div>
                            <small class="text-muted">Tours</small>
                        </div>
                    </div>

                    <hr>

                    <div class="recent-activity">
                        <h6 class="mb-3">Actions récentes</h6>
                        @php
                            $recentEvents = $user->managedEvents()->latest()->take(3)->get();
                        @endphp
                        @if($recentEvents->count() > 0)
                            @foreach($recentEvents as $event)
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-alt text-primary me-2"></i>
                                    <div class="flex-grow-1">
                                        <small>
                                            <strong>{{ Str::limit($event->title, 25) }}</strong>
                                            <br>
                                            <span class="text-muted">{{ $event->created_at->diffForHumans() }}</span>
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted small mb-0">Aucune activité récente</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection