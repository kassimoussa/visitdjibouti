@extends('layouts.admin')

@section('title', 'Universal Media Selector - Exemples')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-images me-2"></i>
                        Universal Media Selector - Exemples d'utilisation
                    </h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Nouveau composant universel !</strong> 
                        Ce sélecteur de médias est inspiré de WordPress et peut être utilisé partout dans votre application.
                    </div>

                    <!-- Exemples Basic -->
                    <div class="row mb-5">
                        <div class="col-md-6">
                            <h5><i class="fas fa-mouse-pointer me-2"></i>Sélection simple</h5>
                            <p class="text-muted">Sélectionner une seule image</p>
                            <div class="example-result mb-3" id="single-result">
                                <div class="placeholder-image">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">Aucune image sélectionnée</p>
                                </div>
                            </div>
                            <button class="btn btn-outline-primary" onclick="openSingleSelector()">
                                <i class="fas fa-image me-2"></i>
                                Sélectionner une image
                            </button>
                        </div>

                        <div class="col-md-6">
                            <h5><i class="fas fa-images me-2"></i>Sélection multiple</h5>
                            <p class="text-muted">Sélectionner plusieurs images (max 5)</p>
                            <div class="example-result mb-3" id="multiple-result">
                                <div class="selected-images-grid" id="selected-grid">
                                    <div class="placeholder-grid">
                                        <i class="fas fa-images fa-2x text-muted"></i>
                                        <p class="mt-2 text-muted">Aucune image sélectionnée</p>
                                    </div>
                                </div>
                                <div class="selection-count mt-2">
                                    <span class="badge bg-secondary" id="selection-counter">0 sélectionnée(s)</span>
                                </div>
                            </div>
                            <button class="btn btn-outline-success" onclick="openMultipleSelector()">
                                <i class="fas fa-images me-2"></i>
                                Sélectionner des images
                            </button>
                            <button class="btn btn-outline-danger btn-sm ms-2" onclick="clearMultipleSelection()">
                                <i class="fas fa-trash me-1"></i>
                                Vider
                            </button>
                        </div>
                    </div>

                    <!-- Exemples Avancés -->
                    <div class="row mb-5">
                        <div class="col-md-4">
                            <h5><i class="fas fa-filter me-2"></i>Images seulement</h5>
                            <p class="text-muted">Filtrer par type de média</p>
                            <div class="example-result mb-3" id="images-only-result">
                                <div class="placeholder-image">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">Images uniquement</p>
                                </div>
                            </div>
                            <button class="btn btn-outline-info" onclick="openImagesOnlySelector()">
                                <i class="fas fa-image me-2"></i>
                                Sélectionner (Images)
                            </button>
                        </div>

                        <div class="col-md-4">
                            <h5><i class="fas fa-file me-2"></i>Documents seulement</h5>
                            <p class="text-muted">PDF, DOC, DOCX uniquement</p>
                            <div class="example-result mb-3" id="docs-only-result">
                                <div class="placeholder-image">
                                    <i class="fas fa-file-alt fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">Documents uniquement</p>
                                </div>
                            </div>
                            <button class="btn btn-outline-warning" onclick="openDocsOnlySelector()">
                                <i class="fas fa-file-alt me-2"></i>
                                Sélectionner (Docs)
                            </button>
                        </div>

                        <div class="col-md-4">
                            <h5><i class="fas fa-cog me-2"></i>Configuration avancée</h5>
                            <p class="text-muted">Avec callback personnalisé</p>
                            <div class="example-result mb-3" id="advanced-result">
                                <div class="placeholder-image">
                                    <i class="fas fa-cogs fa-2x text-muted"></i>
                                    <p class="mt-2 text-muted">Configuration avancée</p>
                                </div>
                            </div>
                            <button class="btn btn-outline-dark" onclick="openAdvancedSelector()">
                                <i class="fas fa-cogs me-2"></i>
                                Configuration avancée
                            </button>
                        </div>
                    </div>

                    <!-- Exemples d'intégration -->
                    <div class="row mb-5">
                        <div class="col-12">
                            <h5><i class="fas fa-code me-2"></i>Intégration avec Livewire</h5>
                            <p class="text-muted">Exemple d'utilisation dans un composant Livewire</p>
                            <livewire:admin.example.media-integration />
                        </div>
                    </div>

                    <!-- Code Examples -->
                    <div class="row">
                        <div class="col-12">
                            <h5><i class="fas fa-code-branch me-2"></i>Exemples de code</h5>
                            
                            <!-- PHP/Livewire -->
                            <div class="mb-4">
                                <h6>PHP - Dans un composant Livewire</h6>
                                <pre><code class="language-php">// Ouvrir le sélecteur
public function openMediaSelector()
{
    $this->dispatch('open-universal-media-selector', [
        'title' => 'Sélectionner des médias',
        'mode' => 'multiple', // 'single' ou 'multiple'
        'maxFiles' => 10,
        'allowedTypes' => ['image', 'video'],
        'callback' => 'my-media-selected',
        'selected' => $this->currentSelection
    ]);
}

// Écouter la sélection
#[On('my-media-selected')]
public function handleMediaSelection($data)
{
    $this->selectedMedia = $data['media'];
    $this->selectionCount = $data['count'];
}</code></pre>
                            </div>

                            <!-- JavaScript -->
                            <div class="mb-4">
                                <h6>JavaScript - Utilisation directe</h6>
                                <pre><code class="language-javascript">// Ouvrir le sélecteur
function openMediaSelector() {
    UMSUtils.open({
        title: 'Choisir des médias',
        mode: 'multiple',
        maxFiles: 5,
        allowedTypes: ['image'],
        callback: 'media-chosen'
    });
}

// Écouter les sélections
UMSUtils.onSelection(function(event) {
    const { media, count, mode } = event.detail;
    console.log('Médias sélectionnés:', media);
});

// Méthodes utilitaires
UMSUtils.getSelected(); // Obtenir la sélection actuelle
UMSUtils.clearSelection(); // Vider la sélection</code></pre>
                            </div>

                            <!-- Blade Template -->
                            <div class="mb-4">
                                <h6>Blade - Inclusion du composant</h6>
                                <pre><code class="language-html">{{-- Inclure le sélecteur dans votre template --}}
&lt;livewire:admin.universal-media-selector /&gt;

{{-- Ou avec un nom personnalisé --}}
&lt;livewire:admin.universal-media-selector wire:key="my-selector-{{ now() }}" /&gt;</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- Features List -->
                    <div class="row">
                        <div class="col-12">
                            <h5><i class="fas fa-star me-2"></i>Fonctionnalités disponibles</h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Sélection simple ou multiple</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Upload par drag & drop</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Upload depuis URL</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Filtres avancés (type, date, taille)</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Recherche en temps réel</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Vue grille et liste</li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-check text-success me-2"></i>Édition inline des métadonnées</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Panneau de détails</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Raccourcis clavier</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Sélection avec Shift</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Responsive design</li>
                                        <li><i class="fas fa-check text-success me-2"></i>Lazy loading des images</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure le sélecteur -->
<livewire:admin.universal-media-selector />

@endsection

@push('styles')
<style>
.example-result {
    min-height: 150px;
    border: 2px dashed #e9ecef;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
    position: relative;
}

.placeholder-image {
    text-align: center;
    color: #6c757d;
}

.placeholder-grid {
    text-align: center;
    color: #6c757d;
}

.selected-images-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(60px, 1fr));
    gap: 8px;
    padding: 10px;
    min-height: 120px;
}

.selected-image-item {
    position: relative;
    border-radius: 4px;
    overflow: hidden;
    background: white;
    border: 2px solid #e9ecef;
    transition: transform 0.2s ease;
}

.selected-image-item:hover {
    transform: scale(1.05);
}

.selected-image-item img {
    width: 100%;
    height: 60px;
    object-fit: cover;
}

.selected-image-remove {
    position: absolute;
    top: -5px;
    right: -5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    border: none;
    font-size: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.selection-count {
    text-align: center;
}

pre {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 4px;
    padding: 1rem;
    font-size: 0.875rem;
    max-height: 300px;
    overflow-y: auto;
}

code {
    color: #e83e8c;
}
</style>
@endpush

@push('scripts')
<script>
// Variables globales pour les exemples
let singleSelection = null;
let multipleSelection = [];

// Sélection simple
function openSingleSelector() {
    UMSUtils.open({
        title: 'Sélectionner une image',
        mode: 'single',
        allowedTypes: ['image'],
        callback: 'single-selected',
        selected: singleSelection ? [singleSelection.id] : []
    });
}

// Sélection multiple
function openMultipleSelector() {
    UMSUtils.open({
        title: 'Sélectionner des images (max 5)',
        mode: 'multiple',
        maxFiles: 5,
        allowedTypes: ['image'],
        callback: 'multiple-selected',
        selected: multipleSelection.map(item => item.id)
    });
}

// Images seulement
function openImagesOnlySelector() {
    UMSUtils.open({
        title: 'Images seulement',
        mode: 'single',
        allowedTypes: ['image'],
        callback: 'images-only-selected'
    });
}

// Documents seulement
function openDocsOnlySelector() {
    UMSUtils.open({
        title: 'Documents seulement',
        mode: 'single',
        allowedTypes: ['document'],
        allowedMimeTypes: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
        callback: 'docs-only-selected'
    });
}

// Configuration avancée
function openAdvancedSelector() {
    UMSUtils.open({
        title: 'Sélecteur avancé avec callback personnalisé',
        mode: 'multiple',
        maxFiles: 3,
        allowedTypes: ['image', 'video'],
        callback: 'advanced-selected',
        resetFilters: false
    });
}

// Vider la sélection multiple
function clearMultipleSelection() {
    multipleSelection = [];
    updateMultipleResult();
}

// Event listeners pour les callbacks
window.addEventListener('single-selected', function(e) {
    singleSelection = e.detail.media[0];
    updateSingleResult();
});

window.addEventListener('multiple-selected', function(e) {
    multipleSelection = e.detail.media;
    updateMultipleResult();
});

window.addEventListener('images-only-selected', function(e) {
    const media = e.detail.media[0];
    document.getElementById('images-only-result').innerHTML = `
        <img src="${media.thumbnail_url || media.url}" alt="${media.title}" 
             style="max-width: 100%; max-height: 140px; border-radius: 4px;">
    `;
});

window.addEventListener('docs-only-selected', function(e) {
    const media = e.detail.media[0];
    document.getElementById('docs-only-result').innerHTML = `
        <div class="text-center">
            <i class="fas fa-file-alt fa-3x text-primary"></i>
            <p class="mt-2 mb-0"><strong>${media.title}</strong></p>
            <small class="text-muted">${media.mime_type}</small>
        </div>
    `;
});

window.addEventListener('advanced-selected', function(e) {
    const { media, count, mode } = e.detail;
    document.getElementById('advanced-result').innerHTML = `
        <div class="text-center">
            <i class="fas fa-check-circle fa-3x text-success"></i>
            <p class="mt-2 mb-0"><strong>${count} média(s) sélectionné(s)</strong></p>
            <small class="text-muted">Mode: ${mode}</small>
            <div class="mt-2">
                ${media.slice(0, 3).map(m => `<span class="badge bg-info me-1">${m.title}</span>`).join('')}
            </div>
        </div>
    `;
    
    // Afficher une notification personnalisée
    showCustomNotification('success', `Configuration avancée: ${count} média(s) sélectionné(s)`);
});

// Mise à jour des résultats
function updateSingleResult() {
    const result = document.getElementById('single-result');
    if (singleSelection) {
        result.innerHTML = `
            <div class="position-relative">
                <img src="${singleSelection.thumbnail_url || singleSelection.url}" 
                     alt="${singleSelection.title}" 
                     style="max-width: 100%; max-height: 140px; border-radius: 4px;">
                <button class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" 
                        onclick="clearSingleSelection()" title="Supprimer">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    } else {
        result.innerHTML = `
            <div class="placeholder-image">
                <i class="fas fa-image fa-2x text-muted"></i>
                <p class="mt-2 text-muted">Aucune image sélectionnée</p>
            </div>
        `;
    }
}

function updateMultipleResult() {
    const grid = document.getElementById('selected-grid');
    const counter = document.getElementById('selection-counter');
    
    counter.textContent = `${multipleSelection.length} sélectionnée(s)`;
    counter.className = `badge ${multipleSelection.length > 0 ? 'bg-primary' : 'bg-secondary'}`;
    
    if (multipleSelection.length > 0) {
        grid.innerHTML = multipleSelection.map(media => `
            <div class="selected-image-item">
                <img src="${media.thumbnail_url || media.url}" alt="${media.title}">
                <button class="selected-image-remove" onclick="removeFromMultiple(${media.id})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `).join('');
    } else {
        grid.innerHTML = `
            <div class="placeholder-grid">
                <i class="fas fa-images fa-2x text-muted"></i>
                <p class="mt-2 text-muted">Aucune image sélectionnée</p>
            </div>
        `;
    }
}

// Fonctions utilitaires
function clearSingleSelection() {
    singleSelection = null;
    updateSingleResult();
}

function removeFromMultiple(mediaId) {
    multipleSelection = multipleSelection.filter(media => media.id !== mediaId);
    updateMultipleResult();
}

function showCustomNotification(type, message) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    updateSingleResult();
    updateMultipleResult();
    
    console.log('🎯 Universal Media Selector Examples loaded!');
});
</script>
@endpush