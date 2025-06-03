@extends('layouts.admin')

@section('title', 'Mon Profil')

@section('content')
    <div class="container">
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        <div class="row">
            <!-- Section Informations personnelles -->
            <div class="col-md-6">
                <div class="content-card">
                    <h2 class="card-title">Informations personnelles</h2>
                    
                    <form action="{{ route('profile.update.info') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="name">Nom complet</label>
                            <input type="text" id="name" name="name" class="form-control" value="{{ $admin->name }}" required>
                            @error('name')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Adresse email</label>
                            <input type="email" id="email" name="email" class="form-control" value="{{ $admin->email }}" required>
                            @error('email')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="phone_number">Numéro de téléphone</label>
                            <input type="text" id="phone_number" name="phone_number" class="form-control" value="{{ $admin->phone_number }}">
                            @error('phone_number')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-2">Mettre à jour</button>
                    </form>
                </div>
            </div>
            
            <!-- Section Sécurité -->
            <div class="col-md-6">
                <div class="content-card">
                    <h2 class="card-title">Sécurité</h2>
                    
                    <form action="{{ route('profile.update.password') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="form-group">
                            <label for="current_password">Mot de passe actuel</label>
                            <input type="password" id="current_password" name="current_password" class="form-control" required>
                            @error('current_password')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Nouveau mot de passe</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                            @error('password')
                                <span class="form-error">{{ $message }}</span>
                            @enderror
                            <p class="form-hint">Minimum 8 caractères</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirmation">Confirmer le mot de passe</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>
                        
                        <button type="submit" class="btn btn-primary mt-2 ">Changer le mot de passe</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Section Avatar -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="content-card">
                    <h2 class="card-title">Photo de profil</h2>
                    
                    <div class="d-flex align-items-center">
                        <div class="avatar-container mr-4">
                            @if($admin->avatar)
                                <img src="{{ asset('storage/' . $admin->avatar) }}" alt="Avatar de {{ $admin->name }}" class="avatar-img">
                            @else
                                <div class="avatar-placeholder">{{ substr($admin->name, 0, 1) }}</div>
                            @endif
                        </div>
                        
                        <div class="avatar-upload">
                            <form action="{{ route('profile.update.avatar') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <label for="avatar" class="btn btn-outline-secondary">Choisir une image</label>
                                <input type="file" id="avatar" name="avatar" class="d-none" onchange="this.form.submit()">
                                
                                @error('avatar')
                                    <span class="form-error d-block mt-2">{{ $message }}</span>
                                @enderror
                                
                                <p class="form-hint mt-2">Formats acceptés: JPG, PNG. Taille max: 1Mo</p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script pour prévisualiser l'image avant upload
        document.getElementById('avatar').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const avatarContainer = document.querySelector('.avatar-container');
                    
                    if (avatarContainer.querySelector('img')) {
                        avatarContainer.querySelector('img').src = e.target.result;
                    } else {
                        const placeholder = avatarContainer.querySelector('.avatar-placeholder');
                        if (placeholder) placeholder.remove();
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.alt = 'Aperçu de l\'avatar';
                        img.classList.add('avatar-img');
                        avatarContainer.appendChild(img);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection

@section('styles')
<style>
    .avatar-container {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        overflow: hidden;
        background-color: #e2e8f0;
    }
    
    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 36px;
        color: #64748b;
    }
    
    .form-hint {
        font-size: 12px;
        color: #64748b;
    }
    
    .form-error {
        color: #ef4444;
        font-size: 13px;
        margin-top: 5px;
    }
</style>
@endsection