{{-- Formulaire pour Messages de l'Application --}}
<div class="border rounded p-3 mb-3">
    <h6 class="mb-3">
        <i class="fas fa-comments me-2"></i>
        Messages et slogans de l'application
    </h6>
    
    {{-- Slogan de l'application --}}
    <div class="mb-4">
        <label class="form-label">Slogan de l'application</label>
        <div class="row">
            @foreach($availableLocales as $locale)
                <div class="col-md-4 mb-2">
                    <label class="form-label small">{{ strtoupper($locale) }}</label>
                    <input type="text" 
                           class="form-control"
                           wire:model="appMessages.app_slogan.{{ $locale }}"
                           placeholder="Slogan en {{ $locale }}">
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Message de bienvenue --}}
    <div class="mb-4">
        <label class="form-label">Message de bienvenue</label>
        <div class="row">
            @foreach($availableLocales as $locale)
                <div class="col-md-12 mb-3">
                    <label class="form-label small">{{ strtoupper($locale) }}</label>
                    <textarea class="form-control"
                              wire:model="appMessages.welcome_message.{{ $locale }}"
                              rows="2"
                              placeholder="Message de bienvenue en {{ $locale }}"></textarea>
                </div>
            @endforeach
        </div>
    </div>
    
    {{-- Message d'accueil page d'accueil --}}
    <div class="mb-4">
        <label class="form-label">Message d'accueil (page d'accueil)</label>
        <div class="row">
            @foreach($availableLocales as $locale)
                <div class="col-md-12 mb-3">
                    <label class="form-label small">{{ strtoupper($locale) }}</label>
                    <input type="text" 
                           class="form-control"
                           wire:model="appMessages.home_greeting.{{ $locale }}"
                           placeholder="Message d'accueil en {{ $locale }}">
                </div>
            @endforeach
        </div>
    </div>
</div>