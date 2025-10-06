<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mot de passe oublié - Visit Djibouti</title>
    @vite(['resources/sass/operator.scss'])
</head>
<body class="operator-auth-body">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="operator-auth-container" style="padding: 40px; max-width: 500px; width: 100%;">
                    <div class="text-center mb-4">
                        <i class="fas fa-key fa-3x text-primary mb-3"></i>
                        <h3>Mot de passe oublié</h3>
                        <p class="text-muted">Entrez votre adresse email pour recevoir un lien de réinitialisation</p>
                    </div>

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

                    <form method="POST" action="{{ route('operator.password.email') }}">
                        @csrf

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

                        <button type="submit" class="operator-btn-primary w-100 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>
                            Envoyer le lien de réinitialisation
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