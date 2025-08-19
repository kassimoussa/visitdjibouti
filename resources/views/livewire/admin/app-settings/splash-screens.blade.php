{{-- Formulaire pour Splash Screens --}}
<div class="border rounded p-3 mb-3">
    <h6 class="mb-3">
        <i class="fas fa-mobile-alt me-2"></i>
        Configuration des écrans de démarrage
    </h6>
    
    {{-- Options générales --}}
    <div class="row mb-3">
        <div class="col-md-4">
            <label class="form-label">Durée totale (ms)</label>
            <input type="number" 
                   class="form-control"
                   wire:model="totalDuration"
                   min="1000"
                   step="500">
        </div>
        <div class="col-md-4">
            <div class="form-check mt-4">
                <input type="checkbox" 
                       class="form-check-input"
                       wire:model="skipEnabled"
                       id="skipEnabled">
                <label class="form-check-label" for="skipEnabled">
                    Permettre de passer
                </label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check mt-4">
                <input type="checkbox" 
                       class="form-check-input"
                       wire:model="autoAdvance"
                       id="autoAdvance">
                <label class="form-check-label" for="autoAdvance">
                    Avancement automatique
                </label>
            </div>
        </div>
    </div>
    
    {{-- Liste des écrans --}}
    <div class="mb-3">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <label class="form-label mb-0">Écrans de démarrage</label>
            <button type="button" 
                    class="btn btn-outline-primary btn-sm"
                    wire:click="addSplashScreen">
                <i class="fas fa-plus me-1"></i>
                Ajouter un écran
            </button>
        </div>
        
        @foreach($splashScreens as $index => $screen)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Écran {{ $index + 1 }}</h6>
                    @if(count($splashScreens) > 1)
                        <button type="button" 
                                class="btn btn-outline-danger btn-sm"
                                wire:click="removeSplashScreen({{ $index }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    {{-- Paramètres de l'écran --}}
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label class="form-label">Durée (ms)</label>
                            <input type="number" 
                                   class="form-control"
                                   wire:model="splashScreens.{{ $index }}.duration"
                                   min="500"
                                   step="250">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Animation</label>
                            <select class="form-select"
                                    wire:model="splashScreens.{{ $index }}.animation">
                                <option value="fade">Fondu</option>
                                <option value="slide_left">Glissement gauche</option>
                                <option value="slide_right">Glissement droite</option>
                                <option value="zoom">Zoom</option>
                                <option value="none">Aucune</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Couleur de fond</label>
                            <input type="color" 
                                   class="form-control"
                                   wire:model="splashScreens.{{ $index }}.background_color">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Couleur du texte</label>
                            <input type="color" 
                                   class="form-control"
                                   wire:model="splashScreens.{{ $index }}.text_color">
                        </div>
                    </div>
                    
                    {{-- Image de l'écran --}}
                    <div class="mb-3">
                        <label class="form-label">Image de fond (optionnel)</label>
                        <div class="d-flex align-items-center gap-3">
                            @if(isset($splashScreens[$index]['media_id']) && $splashScreens[$index]['media_id'])
                                @php
                                    $screenMedia = \App\Models\Media::find($splashScreens[$index]['media_id']);
                                @endphp
                                @if($screenMedia)
                                    <img src="{{ $screenMedia->thumbnail_url ?: $screenMedia->url }}" 
                                         alt="Image écran {{ $index + 1 }}" 
                                         class="rounded border"
                                         style="width: 60px; height: 40px; object-fit: cover;">
                                @endif
                            @endif
                            <button type="button" 
                                    class="btn btn-outline-primary btn-sm"
                                    wire:click="openMediaSelector"
                                    data-screen-index="{{ $index }}">
                                <i class="fas fa-image me-1"></i>
                                Sélectionner
                            </button>
                            @if(isset($splashScreens[$index]['media_id']) && $splashScreens[$index]['media_id'])
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm"
                                        wire:click="$set('splashScreens.{{ $index }}.media_id', null)">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    
                    {{-- Contenu multilingue --}}
                    <div class="mb-3">
                        <label class="form-label">Textes de l'écran</label>
                        
                        {{-- Onglets des langues --}}
                        <ul class="nav nav-pills nav-sm mb-3" role="tablist">
                            @foreach($availableLocales as $locale)
                                <li class="nav-item">
                                    <button class="nav-link {{ $locale === 'fr' ? 'active' : '' }} btn-sm"
                                            type="button"
                                            data-bs-toggle="pill"
                                            data-bs-target="#screen{{ $index }}-{{ $locale }}"
                                            role="tab">
                                        {{ strtoupper($locale) }}
                                    </button>
                                </li>
                            @endforeach
                        </ul>
                        
                        {{-- Contenu des onglets --}}
                        <div class="tab-content">
                            @foreach($availableLocales as $locale)
                                <div class="tab-pane fade {{ $locale === 'fr' ? 'show active' : '' }}"
                                     id="screen{{ $index }}-{{ $locale }}"
                                     role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label">Titre</label>
                                            <input type="text" 
                                                   class="form-control"
                                                   wire:model="splashScreens.{{ $index }}.translations.{{ $locale }}.title"
                                                   placeholder="Titre en {{ $locale }}">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">Sous-titre</label>
                                            <input type="text" 
                                                   class="form-control"
                                                   wire:model="splashScreens.{{ $index }}.translations.{{ $locale }}.subtitle"
                                                   placeholder="Sous-titre en {{ $locale }}">
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>