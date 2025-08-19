{{-- Formulaire pour Templates de Notifications --}}
<div class="border rounded p-3 mb-3">
    <h6 class="mb-3">
        <i class="fas fa-bell me-2"></i>
        Templates de notifications push
    </h6>
    
    {{-- Template bienvenue --}}
    <div class="mb-4">
        <label class="form-label">Notification de bienvenue</label>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="text" 
                       class="form-control"
                       wire:model="notificationTemplates.welcome.{{ $locale }}"
                       placeholder="Message de bienvenue en {{ $locale }}">
            </div>
        @endforeach
    </div>
    
    {{-- Template nouvel événement --}}
    <div class="mb-4">
        <label class="form-label">Nouvel événement</label>
        <small class="text-muted">Variables disponibles: {{event_name}}</small>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="text" 
                       class="form-control"
                       wire:model="notificationTemplates.new_event.{{ $locale }}"
                       placeholder="Nouvel événement en {{ $locale }}">
            </div>
        @endforeach
    </div>
    
    {{-- Template rappel événement --}}
    <div class="mb-4">
        <label class="form-label">Rappel d'événement</label>
        <small class="text-muted">Variables: {{event_name}}, {{time_remaining}}</small>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="text" 
                       class="form-control"
                       wire:model="notificationTemplates.event_reminder.{{ $locale }}"
                       placeholder="Rappel d'événement en {{ $locale }}">
            </div>
        @endforeach
    </div>
    
    {{-- Template POI à proximité --}}
    <div class="mb-4">
        <label class="form-label">POI à proximité</label>
        <small class="text-muted">Variables disponibles: {{poi_name}}</small>
        @foreach($availableLocales as $locale)
            <div class="mb-2">
                <label class="form-label small">{{ strtoupper($locale) }}</label>
                <input type="text" 
                       class="form-control"
                       wire:model="notificationTemplates.nearby_poi.{{ $locale }}"
                       placeholder="POI à proximité en {{ $locale }}">
            </div>
        @endforeach
    </div>
</div>