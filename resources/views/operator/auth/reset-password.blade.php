<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialiser le mot de passe - Visit Djibouti</title>
    @vite(['resources/sass/operator.scss'])
</head>
<body class="operator-auth-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="operator-auth-container" style="padding: 40px; max-width: 500px; width: 100%;">
                    <div class="text-center mb-4">
                        <i class="fas fa-lock fa-3x text-primary mb-3"></i>
                        <h3>Nouveau mot de passe</h3>
                        <p class="text-muted">Créez un nouveau mot de passe sécurisé</p>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('operator.password.update') }}">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="mb-4">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope me-2 text-primary"></i>
                                Adresse email
                            </label>
                            <input type="email"
                                   class="operator-form-control @error('email') is-invalid @enderror"
                                   id="email"
                                   name="email"
                                   value="{{ old('email') }}"
                                   required
                                   autocomplete="email"
                                   autofocus
                                   placeholder="votre@email.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="password" class="form-label">
                                <i class="fas fa-lock me-2 text-primary"></i>
                                Nouveau mot de passe
                            </label>
                            <input type="password"
                                   class="operator-form-control @error('password') is-invalid @enderror"
                                   id="password"
                                   name="password"
                                   required
                                   autocomplete="new-password"
                                   placeholder="••••••••">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="password-requirements mt-2">
                                <small>
                                    <i class="fas fa-info-circle me-1"></i>
                                    Minimum 8 caractères avec majuscules, minuscules et chiffres
                                </small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">
                                <i class="fas fa-lock me-2 text-primary"></i>
                                Confirmer le mot de passe
                            </label>
                            <input type="password"
                                   class="operator-form-control"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   required
                                   autocomplete="new-password"
                                   placeholder="••••••••">
                        </div>

                        <button type="submit" class="operator-btn-primary w-100 mb-3">
                            <i class="fas fa-check me-2"></i>
                            Réinitialiser le mot de passe
                        </button>

                        <div class="text-center">
                            <a href="{{ route('operator.login') }}" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i>
                                Retour à la connexion
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>