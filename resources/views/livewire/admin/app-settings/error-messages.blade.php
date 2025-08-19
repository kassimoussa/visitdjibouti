{{-- Formulaire pour Messages d'Erreur --}}
<div class="border rounded p-3 mb-3">
    <h6 class="mb-3">
        <i class="fas fa-exclamation-triangle me-2"></i>
        Messages d'erreur personnalisés
    </h6>
    
    {{-- Erreur réseau --}}
    <div class="mb-4">
        <label class="form-label">Erreur de connexion réseau</label>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="text" 
                       class="form-control"
                       wire:model="errorMessages.network_error.{{ $locale }}"
                       placeholder="Message d'erreur réseau en {{ $locale }}">
            </div>
        @endforeach
    </div>
    
    {{-- Géolocalisation désactivée --}}
    <div class="mb-4">
        <label class="form-label">Géolocalisation désactivée</label>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="text" 
                       class="form-control"
                       wire:model="errorMessages.location_disabled.{{ $locale }}"
                       placeholder="Message géolocalisation en {{ $locale }}">
            </div>
        @endforeach
    </div>
    
    {{-- Erreur serveur --}}
    <div class="mb-4">
        <label class="form-label">Erreur serveur</label>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="text" 
                       class="form-control"
                       wire:model="errorMessages.server_error.{{ $locale }}"
                       placeholder="Message d'erreur serveur en {{ $locale }}">
            </div>
        @endforeach
    </div>
</div>