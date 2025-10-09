@extends('layouts.auth')

@section('title', 'Connexion - Visit Djibouti')

@section('page-title', 'Connexion')

@section('content')
    <form method="POST" action="{{ route('auth.login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Adresse email</label>
            <input id="email" type="email" class="form-input" name="email" value="{{ old('email') }}" required autofocus>
            @error('email')
                <span class="form-error">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Mot de passe</label>
            <input id="password" type="password" class="form-input" name="password" required>
            @error('password')
                <span class="form-error">{{ $message }}</span>
            @enderror
            <a href="{{ route('password.request') }}" class="form-forgot">Mot de passe oublié?</a>
        </div>

        <div class="remember-me">
            <input type="checkbox" id="remember" name="remember" {{ old('remember') ? 'checked' : '' }}>
            <label for="remember">Se souvenir de moi</label>
        </div>

        <button type="submit" class="submit-btn">Se connecter</button>
    </form>
@endsection