<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Universal Media Selector - Minimal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <div class="alert alert-success">
                    <h4><i class="fas fa-check-circle me-2"></i>Test Minimal - Sans erreurs</h4>
                    <p>Cette version évite tous les problèmes de base de données et d'Alpine.js.</p>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-images me-2"></i>Test Modal Fonctionnel</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-primary btn-lg w-100" onclick="showMinimalModal()">
                                    <i class="fas fa-image me-2"></i>Ouvrir le Modal
                                </button>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-info mb-0">
                                    <strong>Fonctionnalités testées :</strong><br>
                                    ✅ Modal avec overlay<br>
                                    ✅ Scroll fluide<br>
                                    ✅ Images placeholder<br>
                                    ✅ Sélection multiple
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-4" id="result">
                            <div class="alert alert-light">
                                <i class="fas fa-info-circle me-2"></i>Aucune sélection
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal statique pour test -->
    <div class="universal-media-selector">
        <style>
        /* Styles complets du modal */
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
            min-height: 0;
        }

        .ums-media-grid {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            height: 100%;
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

        .ums-media-item.selected {
            border-color: #0073aa;
            box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.2);
        }

        .ums-media-thumbnail {
            width: 100%; height: 140px;
            object-fit: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-size: 2rem;
        }

        .ums-media-info {
            padding: 12px;
            border-top: 1px solid #f0f0f0;
        }

        .ums-media-title {
            font-size: 0.875rem;
            font-weight: 500;
            margin: 0 0 4px 0;
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

        .btn { padding: 8px 16px; border-radius: 4px; border: none; cursor: pointer; }
        .btn-primary { background: #0073aa; color: white; }
        .btn-secondary { background: #6c757d; color: white; }

        /* Scrollbar */
        .ums-media-grid::-webkit-scrollbar { width: 8px; }
        .ums-media-grid::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        .ums-media-grid::-webkit-scrollbar-thumb { background: #c1c1c1; border-radius: 4px; }
        .ums-media-grid::-webkit-scrollbar-thumb:hover { background: #0073aa; }

        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes slideIn { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        </style>

        <!-- Modal Test -->
        <div class="ums-modal-overlay" id="testModal" onclick="hideMinimalModal()">
            <div class="ums-modal" onclick="event.stopPropagation()">
                <div class="ums-header">
                    <div><h2 class="ums-title">Sélecteur de Médias - Test Fonctionnel</h2></div>
                    <div>
                        <button class="ums-btn-icon" onclick="hideMinimalModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>

                <div class="ums-content">
                    <div class="ums-sidebar">
                        <h6>Filtres</h6>
                        <div class="mb-3">
                            <label>Type:</label>
                            <select class="form-control form-control-sm">
                                <option>Tous (20)</option>
                                <option>Images (15)</option>
                                <option>Vidéos (3)</option>
                                <option>Documents (2)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Recherche:</label>
                            <input type="text" class="form-control form-control-sm" placeholder="Rechercher...">
                        </div>
                    </div>

                    <div class="ums-main">
                        <div class="ums-media-grid">
                            <div class="ums-grid" id="mediaGrid">
                                <!-- Généré par JavaScript -->
                            </div>
                        </div>
                    </div>
                </div>

                <div class="ums-footer">
                    <div><span id="selectionInfo">0 sélectionné</span></div>
                    <div>
                        <button class="btn btn-secondary" onclick="hideMinimalModal()">Annuler</button>
                        <button class="btn btn-primary" onclick="confirmSelection()">Sélectionner</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts

    <script>
        let selectedItems = new Set();

        function showMinimalModal() {
            generateTestImages();
            document.getElementById('testModal').classList.add('show');
        }

        function hideMinimalModal() {
            document.getElementById('testModal').classList.remove('show');
        }

        function generateTestImages() {
            const colors = ['0073aa', '667eea', 'f093fb', '4facfe', '43e97b', 'fa709a', 'ff7675', '6c5ce7', 'a29bfe', '74b9ff', 'fd79a8', '6c5ce7', 'a29bfe', '00b894', 'e17055', '74b9ff', '0984e3', 'e84393', '00cec9', 'fdcb6e'];
            const grid = document.getElementById('mediaGrid');
            
            grid.innerHTML = '';
            
            for (let i = 1; i <= 20; i++) {
                const color = colors[(i-1) % colors.length];
                const item = document.createElement('div');
                item.className = 'ums-media-item';
                item.dataset.id = i;
                item.onclick = () => toggleSelection(i, item);
                
                item.innerHTML = `
                    <div class="ums-media-thumbnail" style="background: linear-gradient(135deg, #${color} 0%, #${color}cc 100%);">
                        <i class="fas fa-image"></i>
                    </div>
                    <div class="ums-media-info">
                        <div class="ums-media-title">Image Test ${i}</div>
                        <div style="font-size: 0.75rem; color: #666;">300×200 • ${Math.floor(Math.random() * 2000 + 500)}KB</div>
                    </div>
                `;
                
                grid.appendChild(item);
            }
        }

        function toggleSelection(id, element) {
            if (selectedItems.has(id)) {
                selectedItems.delete(id);
                element.classList.remove('selected');
            } else {
                selectedItems.add(id);
                element.classList.add('selected');
            }
            updateSelectionInfo();
        }

        function updateSelectionInfo() {
            document.getElementById('selectionInfo').textContent = 
                `${selectedItems.size} sélectionné${selectedItems.size > 1 ? 's' : ''}`;
        }

        function confirmSelection() {
            const result = document.getElementById('result');
            if (selectedItems.size > 0) {
                result.innerHTML = `
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>Sélection confirmée !</strong><br>
                        ${selectedItems.size} image(s) sélectionnée(s) : ${Array.from(selectedItems).join(', ')}
                    </div>
                `;
            } else {
                result.innerHTML = `
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Aucune sélection effectuée
                    </div>
                `;
            }
            selectedItems.clear();
            hideMinimalModal();
        }

        // Fermer avec Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                hideMinimalModal();
            }
        });

        console.log('✅ Test Minimal page loaded - No errors expected!');
    </script>
</body>
</html>