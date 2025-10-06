<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion Tour Operator - Visit Djibouti</title>
    @vite(['resources/sass/operator.scss'])
</head>
<body class="operator-auth-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-6">
                <div class="operator-auth-container">
                    <div class="row g-0">
                        <div class="col-md-6">
                            <div class="operator-auth-brand">
                                <i class="fas fa-plane-departure fa-3x mb-3"></i>
                                <h3>Visit Djibouti</h3>
                                <p class="mb-0">Espace Tour Operators</p>
                                <small class="opacity-75">Gérez vos événements et réservations</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="operator-auth-form">
                                <h4 class="mb-4 text-center">Connexion</h4>

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        {{ $errors->first() }}
                                    </div>
                                @endif

                                @if (session('status'))
                                    <div class="alert alert-success">
                                        <i class="fas fa-check-circle me-2"></i>
                                        {{ session('status') }}
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('operator.login.post') }}">
                                    @csrf

                                    <div class="mb-4">
                                        <label for="email" class="form-label">
                                            <i class="fas fa-envelope me-2 text-primary"></i>
                                            Email
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
                                            Mot de passe
                                        </label>
                                        <input type="password"
                                               class="operator-form-control @error('password') is-invalid @enderror"
                                               id="password"
                                               name="password"
                                               required
                                               autocomplete="current-password"
                                               placeholder="••••••••">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-4">
                                        <div class="form-check">
                                            <input class="form-check-input"
                                                   type="checkbox"
                                                   name="remember"
                                                   id="remember"
                                                   {{ old('remember') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="remember">
                                                Se souvenir de moi
                                            </label>
                                        </div>
                                    </div>

                                    <button type="submit" class="operator-btn-primary w-100 mb-3">
                                        <i class="fas fa-sign-in-alt me-2"></i>
                                        Se connecter
                                    </button>

                                    <div class="text-center">
                                        <a href="{{ route('operator.password.request') }}" class="text-decoration-none">
                                            <i class="fas fa-question-circle me-1"></i>
                                            Mot de passe oublié ?
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-center mt-4">
                    <a href="/" class="text-white text-decoration-none">
                        <i class="fas fa-arrow-left me-2"></i>
                        Retour au site principal
                    </a>
                </div>
            </div>
        </div>
    </div>

    @vite(['resources/js/app.js'])
</body>
</html>