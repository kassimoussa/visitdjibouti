{{-- Formulaire pour Onboarding --}}
<div class="border rounded p-3 mb-3">
    <h6 class="mb-3">
        <i class="fas fa-route me-2"></i>
        Introduction guidée (onboarding)
    </h6>
    
    {{-- Bouton ajouter étape --}}
    <div class="mb-3">
        <button type="button" 
                class="btn btn-outline-primary btn-sm"
                wire:click="addOnboardingStep">
            <i class="fas fa-plus me-1"></i>
            Ajouter une étape
        </button>
    </div>
    
    {{-- Liste des étapes --}}
    @foreach($onboardingSteps as $index => $step)
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="mb-0">Étape {{ $index + 1 }}</h6>
                @if(count($onboardingSteps) > 1)
                    <button type="button" 
                            class="btn btn-outline-danger btn-sm"
                            wire:click="removeOnboardingStep({{ $index }})">
                        <i class="fas fa-trash"></i>
                    </button>
                @endif
            </div>
            <div class="card-body">
                {{-- Icône --}}
                <div class="mb-3">
                    <label class="form-label">Icône FontAwesome</label>
                    <input type="text" 
                           class="form-control"
                           wire:model="onboardingSteps.{{ $index }}.icon"
                           placeholder="ex: map-marker, calendar, heart">
                    <div class="form-text">
                        Nom de l'icône FontAwesome (sans le préfixe "fa-")
                        <i class="fas fa-{{ $step['icon'] ?? 'info' }} ms-2"></i>
                    </div>
                </div>
                
                {{-- Contenu multilingue --}}
                <div class="mb-3">
                    <label class="form-label">Contenu de l'étape</label>
                    
                    {{-- Onglets des langues --}}
                    <ul class="nav nav-pills nav-sm mb-3" role="tablist">
                        @foreach($availableLocales as $locale)
                            <li class="nav-item">
                                <button class="nav-link {{ $locale === 'fr' ? 'active' : '' }} btn-sm"
                                        type="button"
                                        data-bs-toggle="pill"
                                        data-bs-target="#step{{ $index }}-{{ $locale }}"
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
                                 id="step{{ $index }}-{{ $locale }}"
                                 role="tabpanel">
                                <div class="mb-3">
                                    <label class="form-label">Titre</label>
                                    <input type="text" 
                                           class="form-control"
                                           wire:model="onboardingSteps.{{ $index }}.translations.{{ $locale }}.title"
                                           placeholder="Titre de l'étape en {{ $locale }}">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control"
                                              wire:model="onboardingSteps.{{ $index }}.translations.{{ $locale }}.description"
                                              rows="3"
                                              placeholder="Description de l'étape en {{ $locale }}"></textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>