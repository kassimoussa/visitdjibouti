<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Universal Media Selector - Simple</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h4><i class="fas fa-info-circle me-2"></i>Test Universal Media Selector - Version Simple</h4>
                    <p>Cette version utilise des données de test pour éviter les problèmes de base de données.</p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-images me-2"></i>Test avec données simulées</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="openSimpleSelector()">
                            <i class="fas fa-image me-2"></i>Ouvrir le sélecteur (Avec données de test)
                        </button>
                        
                        <div class="mt-3">
                            <div class="alert alert-warning">
                                <strong><i class="fas fa-info-circle me-2"></i>Debug info:</strong><br>
                                • 15 éléments de test avec images placeholder colorées<br>
                                • Scroll fonctionnel dans la zone principale<br>
                                • Vue grille et liste disponibles<br>
                                • Sélection multiple activée
                            </div>
                        </div>
                        
                        <div class="mt-4" id="result">
                            <p class="text-muted">Aucun média sélectionné</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inclure le composant Universal Media Selector -->
        <livewire:admin.universal-media-selector wire:key="test-selector" />
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts

    <script>
        function openSimpleSelector() {
            // Test simple du sélecteur avec Livewire
            if (window.Livewire) {
                window.Livewire.dispatch('open-universal-media-selector', {
                    title: 'Test - Sélecteur avec données simulées',
                    mode: 'multiple',
                    maxFiles: 5,
                    callback: 'test-selection-simple',
                    allowedTypes: ['image', 'video']
                });
            } else {
                alert('Livewire n\'est pas chargé');
            }
        }

        // Écouter les sélections
        window.addEventListener('test-selection-simple', function(e) {
            document.getElementById('result').innerHTML = `
                <div class="alert alert-success">
                    <strong>Sélection réussie !</strong><br>
                    ${e.detail.count} média(s) sélectionné(s)
                    <ul class="mt-2">
                        ${e.detail.media.map(m => `<li>${m.title || m.original_name}</li>`).join('')}
                    </ul>
                </div>
            `;
        });

        // Écouter les erreurs potentielles
        window.addEventListener('alpine:init', () => {
            console.log('Alpine.js initialisé');
        });

        // Créer l'utilitaire UMSUtils si pas déjà défini
        window.UMSUtils = window.UMSUtils || {
            open: function(config = {}) {
                window.Livewire.dispatch('open-universal-media-selector', config);
            }
        };

        console.log('Test Simple page loaded successfully');
    </script>
</body>
</html>