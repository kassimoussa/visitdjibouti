<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Universal Media Selector - Debug</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info">
                    <h4><i class="fas fa-bug me-2"></i>Debug Universal Media Selector</h4>
                    <p>Cette page permet de tester le modal et ses styles même sans base de données.</p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-images me-2"></i>Test du Modal</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary" onclick="openTestModal()">
                            <i class="fas fa-image me-2"></i>Ouvrir le modal de test
                        </button>
                        
                        <div class="mt-4">
                            <h6>États possibles :</h6>
                            <ul>
                                <li>✅ Modal s'affiche avec overlay</li>
                                <li>✅ Styles CSS appliqués</li>
                                <li>✅ Header avec boutons</li>
                                <li>✅ Sidebar avec onglets</li>
                                <li>✅ Zone principale</li>
                                <li>⚠️ Images depuis base de données (nécessite données)</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de test statique pour vérifier les styles -->
    <div class="universal-media-selector">
        <style>
        /* Tous les styles du modal */
        .ums-modal-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.75);
            z-index: 10000;
            display: none;
            align-items: center;
            justify-content: center;
            backdrop-filter: blur(3px);
            animation: fadeIn 0.3s ease-out;
        }
        .ums-modal-overlay.show { display: flex; }
        
        .ums-modal {
            background: white;
            border-radius: 12px;
            max-width: 90vw; max-height: 90vh;
            width: 1200px; height: 800px;
            display: flex; flex-direction: column;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            animation: slideIn 0.3s ease-out;
            border: 1px solid #e1e5e9;
        }

        .ums-header {
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .ums-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #2c3e50;
            margin: 0;
        }

        .ums-header-right {
            display: flex;
            gap: 8px;
        }

        .ums-btn-icon {
            background: none;
            border: 1px solid #ddd;
            border-radius: 4px;
            width: 36px; height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .ums-btn-icon:hover {
            background: #f0f0f0;
            border-color: #0073aa;
        }

        .ums-content {
            display: flex;
            flex: 1;
            overflow: hidden;
        }

        .ums-sidebar {
            width: 280px;
            background: #f8f9fa;
            border-right: 1px solid #e9ecef;
            padding: 16px;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .ums-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .ums-tabs {
            display: flex;
            border-bottom: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .ums-tab {
            padding: 12px 20px;
            background: none; border: none;
            cursor: pointer;
            font-size: 0.875rem;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease;
        }

        .ums-tab:hover { background: #e9ecef; }
        .ums-tab.active {
            background: white;
            border-bottom-color: #0073aa;
            color: #0073aa;
        }

        .ums-media-grid {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: white;
        }

        .ums-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 16px;
        }

        .ums-media-item {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s ease;
            background: white;
            position: relative;
        }

        .ums-media-item:hover {
            border-color: #0073aa;
            transform: scale(1.02);
        }

        .ums-media-thumbnail {
            width: 100%; height: 140px;
            object-fit: cover;
            background: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .ums-media-info {
            padding: 12px;
            border-top: 1px solid #f0f0f0;
        }

        .ums-media-title {
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0 0 4px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .ums-footer {
            background: #f8f9fa;
            border-top: 1px solid #e9ecef;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-shrink: 0;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .btn-primary {
            background: #0073aa;
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { 
                opacity: 0;
                transform: scale(0.95) translateY(-20px);
            }
            to { 
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        </style>

        <!-- Modal Test -->
        <div class="ums-modal-overlay" id="testModal">
            <div class="ums-modal" onclick="event.stopPropagation()">
                <!-- Header -->
                <div class="ums-header">
                    <div class="ums-header-left">
                        <h2 class="ums-title">Universal Media Selector - Test</h2>
                    </div>
                    <div class="ums-header-right">
                        <button class="ums-btn-icon" onclick="closeTestModal()" title="Fermer">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="ums-content">
                    <!-- Sidebar -->
                    <div class="ums-sidebar">
                        <h6>Onglets</h6>
                        <div class="ums-tabs" style="flex-direction: column; border: none;">
                            <button class="ums-tab active" style="text-align: left;">
                                <i class="fas fa-folder me-2"></i>Bibliothèque
                            </button>
                            <button class="ums-tab" style="text-align: left;">
                                <i class="fas fa-upload me-2"></i>Upload
                            </button>
                            <button class="ums-tab" style="text-align: left;">
                                <i class="fas fa-link me-2"></i>URL
                            </button>
                        </div>

                        <hr>

                        <h6>Filtres</h6>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control">
                                <option>Tous</option>
                                <option>Images</option>
                                <option>Vidéos</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Recherche</label>
                            <input type="text" class="form-control" placeholder="Rechercher...">
                        </div>
                    </div>

                    <!-- Main -->
                    <div class="ums-main">
                        <div class="ums-media-grid">
                            <div class="ums-grid">
                                <!-- Éléments de test -->
                                <div class="ums-media-item">
                                    <div class="ums-media-thumbnail" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                        <i class="fas fa-image fa-3x"></i>
                                    </div>
                                    <div class="ums-media-info">
                                        <div class="ums-media-title">Exemple 1</div>
                                        <div class="text-muted">Image • 1024x768</div>
                                    </div>
                                </div>

                                <div class="ums-media-item">
                                    <div class="ums-media-thumbnail" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                                        <i class="fas fa-play-circle fa-3x"></i>
                                    </div>
                                    <div class="ums-media-info">
                                        <div class="ums-media-title">Vidéo exemple</div>
                                        <div class="text-muted">Vidéo • 1920x1080</div>
                                    </div>
                                </div>

                                <div class="ums-media-item">
                                    <div class="ums-media-thumbnail" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                                        <i class="fas fa-file-pdf fa-3x"></i>
                                    </div>
                                    <div class="ums-media-info">
                                        <div class="ums-media-title">Document.pdf</div>
                                        <div class="text-muted">PDF • 2.4 MB</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="ums-footer">
                    <div>
                        <span class="text-muted">0 sélectionné</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-secondary" onclick="closeTestModal()">Annuler</button>
                        <button class="btn btn-primary">
                            <i class="fas fa-check me-2"></i>Sélectionner
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts

    <script>
        function openTestModal() {
            document.getElementById('testModal').classList.add('show');
        }

        function closeTestModal() {
            document.getElementById('testModal').classList.remove('show');
        }

        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeTestModal();
            }
        });

        // Fermer en cliquant sur l'overlay
        document.getElementById('testModal').addEventListener('click', closeTestModal);
    </script>
</body>
</html>