{{-- Formulaire pour Configuration de l'Application --}}
<div class="border rounded p-3 mb-3">
    <h6 class="mb-3">
        <i class="fas fa-cogs me-2"></i>
        Configuration générale de l'application
    </h6>
    
    {{-- Configuration du thème --}}
    <div class="mb-4">
        <h6 class="mb-3">
            <i class="fas fa-palette me-2"></i>
            Thème et couleurs
        </h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Couleur principale</label>
                <input type="color" 
                       class="form-control"
                       wire:model="appConfigTheme.primary_color">
                <div class="form-text">{{ $appConfigTheme['primary_color'] ?? '#1E88E5' }}</div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Couleur secondaire</label>
                <input type="color" 
                       class="form-control"
                       wire:model="appConfigTheme.secondary_color">
                <div class="form-text">{{ $appConfigTheme['secondary_color'] ?? '#00ACC1' }}</div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Couleur d'accent</label>
                <input type="color" 
                       class="form-control"
                       wire:model="appConfigTheme.accent_color">
                <div class="form-text">{{ $appConfigTheme['accent_color'] ?? '#FFC107' }}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Couleur de fond</label>
                <input type="color" 
                       class="form-control"
                       wire:model="appConfigTheme.background_color">
                <div class="form-text">{{ $appConfigTheme['background_color'] ?? '#F5F5F5' }}</div>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Couleur du texte</label>
                <input type="color" 
                       class="form-control"
                       wire:model="appConfigTheme.text_color">
                <div class="form-text">{{ $appConfigTheme['text_color'] ?? '#212121' }}</div>
            </div>
        </div>
    </div>
    
    {{-- Fonctionnalités --}}
    <div class="mb-4">
        <h6 class="mb-3">
            <i class="fas fa-toggle-on me-2"></i>
            Fonctionnalités activées
        </h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input"
                           wire:model="appConfigFeatures.offline_mode"
                           id="offline_mode">
                    <label class="form-check-label" for="offline_mode">
                        <strong>Mode hors ligne</strong>
                        <div class="form-text">Permettre l'utilisation sans connexion</div>
                    </label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input"
                           wire:model="appConfigFeatures.push_notifications"
                           id="push_notifications">
                    <label class="form-check-label" for="push_notifications">
                        <strong>Notifications push</strong>
                        <div class="form-text">Envoyer des notifications à l'utilisateur</div>
                    </label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input"
                           wire:model="appConfigFeatures.location_tracking"
                           id="location_tracking">
                    <label class="form-check-label" for="location_tracking">
                        <strong>Géolocalisation</strong>
                        <div class="form-text">Utiliser la position de l'utilisateur</div>
                    </label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input"
                           wire:model="appConfigFeatures.favorites_sync"
                           id="favorites_sync">
                    <label class="form-check-label" for="favorites_sync">
                        <strong>Synchronisation favoris</strong>
                        <div class="form-text">Synchroniser les favoris dans le cloud</div>
                    </label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" 
                           class="form-check-input"
                           wire:model="appConfigFeatures.dark_mode"
                           id="dark_mode">
                    <label class="form-check-label" for="dark_mode">
                        <strong>Mode sombre</strong>
                        <div class="form-text">Interface en mode sombre</div>
                    </label>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Paramètres API --}}
    <div class="mb-4">
        <h6 class="mb-3">
            <i class="fas fa-server me-2"></i>
            Paramètres API
        </h6>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Durée du cache (secondes)</label>
                <input type="number" 
                       class="form-control"
                       wire:model="appConfigApi.cache_duration"
                       min="60"
                       step="60">
                <div class="form-text">Durée de mise en cache des données</div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Qualité des images</label>
                <select class="form-select"
                        wire:model="appConfigApi.image_quality">
                    <option value="low">Basse</option>
                    <option value="medium">Moyenne</option>
                    <option value="high">Haute</option>
                    <option value="original">Originale</option>
                </select>
                <div class="form-text">Qualité des images téléchargées</div>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Timeout (secondes)</label>
                <input type="number" 
                       class="form-control"
                       wire:model="appConfigApi.timeout"
                       min="5"
                       max="120"
                       step="5">
                <div class="form-text">Délai d'attente pour les requêtes</div>
            </div>
        </div>
    </div>
</div>