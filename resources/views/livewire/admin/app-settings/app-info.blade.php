{{-- Formulaire pour Informations de l'Application --}}
<div class="border rounded p-3 mb-3">
    <h6 class="mb-3">
        <i class="fas fa-info-circle me-2"></i>
        Informations de l'application
    </h6>
    
    {{-- Informations techniques --}}
    <div class="mb-4">
        <h6 class="mb-3">Informations techniques</h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Version</label>
                <input type="text" 
                       class="form-control"
                       wire:model="appInfo.version"
                       placeholder="ex: 1.0.0">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Build Number</label>
                <input type="text" 
                       class="form-control"
                       wire:model="appInfo.build_number"
                       placeholder="ex: 1">
            </div>
        </div>
    </div>
    
    {{-- Support --}}
    <div class="mb-4">
        <h6 class="mb-3">Support et contact</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Email de support</label>
                <input type="email" 
                       class="form-control"
                       wire:model="appInfo.support_email"
                       placeholder="support@visitdjibouti.dj">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Téléphone de support</label>
                <input type="tel" 
                       class="form-control"
                       wire:model="appInfo.support_phone"
                       placeholder="+253 21 35 00 00">
            </div>
        </div>
    </div>
    
    {{-- Texte "À propos" --}}
    <div class="mb-4">
        <label class="form-label">Texte "À propos"</label>
        @foreach($availableLocales as $locale)
            <div class="mb-3">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <textarea class="form-control"
                          wire:model="appInfo.about_text.{{ $locale }}"
                          rows="3"
                          placeholder="Texte à propos en {{ $locale }}"></textarea>
            </div>
        @endforeach
    </div>
    
    {{-- URLs Politique de confidentialité --}}
    <div class="mb-4">
        <label class="form-label">URLs Politique de confidentialité</label>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="url" 
                       class="form-control"
                       wire:model="appInfo.privacy_policy_url.{{ $locale }}"
                       placeholder="https://visitdjibouti.dj/privacy-{{ $locale }}">
            </div>
        @endforeach
    </div>
    
    {{-- URLs Conditions d'utilisation --}}
    <div class="mb-4">
        <label class="form-label">URLs Conditions d'utilisation</label>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="url" 
                       class="form-control"
                       wire:model="appInfo.terms_url.{{ $locale }}"
                       placeholder="https://visitdjibouti.dj/terms-{{ $locale }}">
            </div>
        @endforeach
    </div>
</div>