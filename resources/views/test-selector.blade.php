<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Universal Media Selector</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
</head>

<body>
    <div class="container mt-5">
        <div class="alert alert-info">
            <h4><i class="fas fa-info-circle me-2"></i>Test Universal Media Selector</h4>
            <p>Cette page teste le nouveau composant Universal Media Selector.</p>
        </div>

        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-images me-2"></i>Universal Media Selector - Test</h5>
            </div>
            <div class="card-body">
                <button class="btn btn-primary" onclick="testSelector()">
                    <i class="fas fa-image me-2"></i>Ouvrir le sélecteur
                </button>

                <div class="mt-3" id="result">
                    <p class="text-muted">Aucun média sélectionné</p>
                </div>
            </div>
        </div>

        <!-- Inclure le composant Universal Media Selector -->
        <livewire:admin.universal-media-selector />
    </div>

    <button class="btn btn-outline-primary" wire:click="$dispatch('open-media-modal')">Ouvrir la bibliothèque</button>

    <livewire:media-library-modal />

    <script>
        document.addEventListener('alpine:init', () => {
            Livewire.on('media-selected', ({
                id
            }) => {
                console.log("Média sélectionné avec ID :", id);
                // Traitement : remplir un champ, afficher un aperçu, etc.
            });

            Livewire.on('open-media-modal', () => {
                Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).open();
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts

    <script>
        function testSelector() {
            // Test simple du sélecteur
            if (window.Livewire) {
                window.Livewire.dispatch('open-universal-media-selector', {
                    title: 'Test - Sélectionner des médias',
                    mode: 'multiple',
                    maxFiles: 5,
                    callback: 'test-selection'
                });
            } else {
                alert('Livewire n\'est pas chargé');
            }
        }

        // Écouter les sélections
        window.addEventListener('test-selection', function(e) {
            document.getElementById('result').innerHTML = `
                <div class="alert alert-success">
                    <strong>Sélection réussie !</strong><br>
                    ${e.detail.count} média(s) sélectionné(s)
                </div>
            `;
        });

        // Créer l'utilitaire UMSUtils si pas déjà défini
        window.UMSUtils = window.UMSUtils || {
            open: function(config = {}) {
                window.Livewire.dispatch('open-universal-media-selector', config);
            }
        };

        console.log('Test page loaded successfully');
    </script>
</body>

</html>
