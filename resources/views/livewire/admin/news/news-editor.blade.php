<div class="news-editor-container">
    {{-- Auto-save indicator --}}
    @if($lastSaved)
    <div class="autosave-indicator">
        <i class="fas fa-check-circle text-success"></i>
        <span class="text-muted small">Dernière sauvegarde: {{ $lastSaved }}</span>
    </div>
    @endif

    <form wire:submit.prevent="save">
        <div class="row">
            {{-- Contenu principal --}}
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="fas fa-edit me-2"></i>
                                {{ $isEditMode ? 'Modifier l\'actualité' : 'Nouvelle actualité' }}
                            </h5>
                            
                            {{-- Language tabs --}}
                            <div class="btn-group btn-group-sm" role="group">
                                <input type="radio" class="btn-check" name="locale" id="locale-fr" 
                                       wire:click="switchLocale('fr')" {{ $currentLocale === 'fr' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="locale-fr">🇫🇷 FR</label>

                                <input type="radio" class="btn-check" name="locale" id="locale-en"
                                       wire:click="switchLocale('en')" {{ $currentLocale === 'en' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="locale-en">🇬🇧 EN</label>

                                <input type="radio" class="btn-check" name="locale" id="locale-ar"
                                       wire:click="switchLocale('ar')" {{ $currentLocale === 'ar' ? 'checked' : '' }}>
                                <label class="btn btn-outline-primary" for="locale-ar">🇩🇯 AR</label>
                            </div>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Titre --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">
                                Titre de l'article *
                            </label>
                            <input type="text" class="form-control form-control-lg" 
                                   wire:model.live="translations.{{ $currentLocale }}.title"
                                   placeholder="Saisir le titre de l'article">
                            @error('translations.' . $currentLocale . '.title')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">URL (slug)</label>
                            <div class="input-group">
                                <span class="input-group-text">{{ url('/') }}/actualites/</span>
                                <input type="text" class="form-control" wire:model.live="slug">
                            </div>
                        </div>

                        {{-- Extrait --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Extrait/Résumé</label>
                            <textarea class="form-control" rows="3" 
                                      wire:model.live="translations.{{ $currentLocale }}.excerpt"
                                      placeholder="Court résumé de l'article (affiché dans les listes)"></textarea>
                            <div class="form-text">Recommandé : 150-300 caractères</div>
                        </div>

                        {{-- Éditeur de contenu --}}
                        <div class="mb-4" wire:ignore>
                            <label class="form-label fw-semibold">Contenu de l'article</label>
                            <div id="tinymce-wrapper">
                                <textarea id="tinymce-editor" 
                                          wire:model.defer="contentBlocks"
                                          placeholder="Saisissez le contenu de votre article ici...">{{ $contentBlocks }}</textarea>
                            </div>
                            <div class="form-text mt-2">
                                <span class="text-info">💡 Utilisez l'éditeur pour formater votre contenu avec des titres, listes, images, etc.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SEO Section - Masquée pour le moment --}}
                <div class="card mt-4" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-search me-2"></i>
                            Référencement (SEO)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Titre SEO</label>
                            <input type="text" class="form-control" 
                                   wire:model.live="translations.{{ $currentLocale }}.meta_title"
                                   maxlength="60">
                            <div class="form-text">Recommandé : 50-60 caractères</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description SEO</label>
                            <textarea class="form-control" rows="3" 
                                      wire:model.live="translations.{{ $currentLocale }}.meta_description"
                                      maxlength="160"></textarea>
                            <div class="form-text">Recommandé : 150-160 caractères</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mots-clés SEO</label>
                            <input type="text" class="form-control" 
                                   x-data="tagInput('{{ $currentLocale }}')" 
                                   x-model="tagValue"
                                   @keydown.enter.prevent="addTag()"
                                   placeholder="Appuyer sur Entrée pour ajouter un mot-clé">
                            <div class="mt-2">
                                @if(isset($translations[$currentLocale]['seo_keywords']) && is_array($translations[$currentLocale]['seo_keywords']))
                                    @foreach($translations[$currentLocale]['seo_keywords'] as $index => $keyword)
                                        <span class="badge bg-secondary me-1 mb-1">
                                            {{ $keyword }}
                                            <button type="button" class="btn-close btn-close-white ms-1" 
                                                    wire:click="removeKeyword('{{ $currentLocale }}', {{ $index }})"></button>
                                        </span>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                {{-- Actions --}}
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Actions
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-success" wire:click="publish"
                                    wire:loading.attr="disabled">
                                <i class="fas fa-globe me-2"></i>
                                Publier
                            </button>
                            
                            <button type="button" class="btn btn-outline-secondary" wire:click="saveDraft"
                                    wire:loading.attr="disabled">
                                <i class="fas fa-save me-2"></i>
                                Enregistrer comme brouillon
                            </button>
                            
                            <button type="button" class="btn btn-outline-primary" wire:click="save"
                                    wire:loading.attr="disabled">
                                <i class="fas fa-check me-2"></i>
                                Sauvegarder
                            </button>
                        </div>
                        
                        {{-- Status --}}
                        <div class="mt-3">
                            <small class="text-muted">
                                <strong>Statut:</strong> 
                                <span class="badge bg-{{ $status === 'published' ? 'success' : ($status === 'draft' ? 'secondary' : 'warning') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </small>
                        </div>
                        
                        {{-- Reading time --}}
                        @if($readingTime)
                            <div class="mt-2">
                                <small class="text-muted">
                                    <i class="fas fa-clock me-1"></i>
                                    {{ $readingTime }} min de lecture
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Publication --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-calendar me-2"></i>
                            Publication
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Date de publication</label>
                            <input type="datetime-local" class="form-control" wire:model.live="publishedAt">
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" wire:model.live="isFeatured" id="isFeatured">
                            <label class="form-check-label" for="isFeatured">
                                Article à la une
                            </label>
                        </div>

                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model.live="allowComments" id="allowComments">
                            <label class="form-check-label" for="allowComments">
                                Autoriser les commentaires
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Image à la une --}}
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-image me-2"></i>
                            Image à la une
                        </h6>
                        <button type="button" class="btn btn-sm btn-primary" wire:click="openFeaturedImageSelector">
                            <i class="fas fa-images me-1"></i>Choisir une image
                        </button>
                    </div>
                    <div class="card-body">
                        @if ($featuredImageId)
                            <div class="text-center">
                                @php $featuredImage = $media->firstWhere('id', $featuredImageId); @endphp
                                @if ($featuredImage)
                                    <div class="position-relative d-inline-block">
                                        <img src="{{ asset($featuredImage->thumbnail_path ?? $featuredImage->path) }}"
                                            alt="{{ $featuredImage->getTranslation('fr')->title ?? $featuredImage->original_name }}"
                                            class="img-fluid rounded border shadow-sm"
                                            style="max-height: 200px; max-width: 100%;">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2"
                                                wire:click="selectFeaturedImage(null)"
                                                title="Supprimer l'image">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            {{ $featuredImage->getTranslation('fr')->title ?? $featuredImage->original_name }}
                                        </small>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="text-center py-4 border-dashed rounded" style="border: 2px dashed #dee2e6;">
                                <i class="fas fa-image fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Aucune image sélectionnée</p>
                                <button type="button" class="btn btn-outline-primary" wire:click="openFeaturedImageSelector">
                                    <i class="fas fa-plus me-1"></i>Sélectionner une image
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Galerie d'images --}}
                <div class="card mt-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">
                            <i class="fas fa-images me-2"></i>
                            Galerie d'images ({{ count($selectedMedia) }})
                        </h6>
                        <button type="button" class="btn btn-sm btn-success" wire:click="openGallerySelector">
                            <i class="fas fa-plus me-1"></i>Ajouter des images
                        </button>
                    </div>
                    <div class="card-body">
                        @if (count($selectedMedia) > 0)
                            <div class="mb-3">
                                <div class="alert alert-info alert-sm">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Glissez-déposez les images pour les réorganiser
                                </div>
                                <div id="gallery-images" class="row g-3">
                                    @foreach ($selectedMedia as $index => $mediaId)
                                        @php $mediaItem = $media->firstWhere('id', $mediaId); @endphp
                                        @if ($mediaItem)
                                            <div class="col-6 col-lg-4" data-media-id="{{ $mediaId }}">
                                                <div class="gallery-item position-relative">
                                                    <div class="image-container" style="cursor: grab;">
                                                        <img src="{{ asset($mediaItem->thumbnail_path ?? $mediaItem->path) }}"
                                                            alt="{{ $mediaItem->getTranslation('fr')->alt_text ?? $mediaItem->original_name }}"
                                                            class="img-fluid rounded border shadow-sm"
                                                            style="height: 120px; width: 100%; object-fit: cover;">
                                                        <div class="image-overlay">
                                                            <div class="overlay-actions">
                                                                <button type="button" class="btn btn-sm btn-light btn-icon" 
                                                                        title="Définir comme image principale"
                                                                        wire:click="selectFeaturedImage({{ $mediaId }})">
                                                                    <i class="fas fa-star {{ $featuredImageId == $mediaId ? 'text-warning' : 'text-muted' }}"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-danger btn-icon"
                                                                        title="Supprimer de la galerie"
                                                                        wire:click="removeFromGallery({{ $mediaId }})">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            </div>
                                                            <div class="drag-handle">
                                                                <i class="fas fa-grip-vertical"></i>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2 text-center">
                                                        <small class="text-muted d-block text-truncate">
                                                            {{ $mediaItem->getTranslation('fr')->title ?? $mediaItem->original_name }}
                                                        </small>
                                                        <span class="badge badge-sm bg-primary">{{ $index + 1 }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="text-center py-4 border-dashed rounded" style="border: 2px dashed #dee2e6;">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p class="text-muted mb-2">Aucune image dans la galerie</p>
                                <button type="button" class="btn btn-outline-success" wire:click="openGallerySelector">
                                    <i class="fas fa-plus me-1"></i>Ajouter des images
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Catégorie --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-folder me-2"></i>
                            Catégorie
                        </h6>
                    </div>
                    <div class="card-body">
                        <select class="form-select" wire:model.live="categoryId">
                            <option value="">Aucune catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category['id'] }}">
                                    {{ str_repeat('— ', $category['depth']) }}{{ $category['name'] }}
                                </option>
                            @endforeach
                        </select>
                        
                        @if($this->selectedCategory)
                            <div class="mt-2">
                                <small class="text-muted">
                                    Sélectionnée: <strong>{{ $this->selectedCategory['full_name'] }}</strong>
                                </small>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tags - Masquée pour le moment --}}
                <div class="card mt-3" style="display: none;">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="fas fa-tags me-2"></i>
                            Étiquettes
                        </h6>
                    </div>
                    <div class="card-body">
                        {{-- Selected tags --}}
                        <div class="mb-3">
                            @forelse($this->selectedTagsData as $tag)
                                <span class="badge bg-primary me-1 mb-1">
                                    {{ $tag['name'] }}
                                    <button type="button" class="btn-close btn-close-white ms-1" 
                                            wire:click="toggleTag({{ $tag['id'] }})"></button>
                                </span>
                            @empty
                                <em class="text-muted">Aucune étiquette sélectionnée</em>
                            @endforelse
                        </div>

                        {{-- Add new tag --}}
                        <div class="mb-3" x-data="{ newTag: '' }">
                            <div class="input-group">
                                <input type="text" class="form-control form-control-sm" 
                                       x-model="newTag" 
                                       placeholder="Nouvelle étiquette"
                                       @keydown.enter.prevent="$wire.addTag(newTag); newTag = ''">
                                <button class="btn btn-outline-secondary btn-sm" type="button"
                                        @click="$wire.addTag(newTag); newTag = ''">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>

                        {{-- Available tags --}}
                        <div class="available-tags">
                            <small class="text-muted mb-2 d-block">Tags disponibles:</small>
                            <div style="max-height: 200px; overflow-y: auto;">
                                @foreach($availableTags as $tag)
                                    <div class="form-check form-check-sm mb-1">
                                        <input class="form-check-input" type="checkbox" 
                                               wire:click="toggleTag({{ $tag['id'] }})"
                                               {{ in_array($tag['id'], $selectedTags) ? 'checked' : '' }}
                                               id="tag-{{ $tag['id'] }}">
                                        <label class="form-check-label small" for="tag-{{ $tag['id'] }}">
                                            {{ $tag['name'] }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal de sélection de médias -->
    @livewire('admin.media-selector-modal')

    @if($showPoiSelector)
        <livewire:admin.poi-selector-modal wire:key="poi-selector-{{ now() }}" />
    @endif

    @if($showEventSelector)
        <livewire:admin.event-selector-modal wire:key="event-selector-{{ now() }}" />
    @endif

    {{-- JavaScript avec TinyMCE --}}
    @push('scripts')
    <!-- TinyMCE CDN avec clé API -->
    <script src="https://cdn.tiny.cloud/1/89bngka7a9dwqwi7ifztya3hrkhnpd2dy4l7bfp23qqgt7rw/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    
    <script>
        let tinymceEditor = null;
        
        // Initialiser TinyMCE
        function initTinyMCE() {
            if (tinymceEditor) {
                tinymceEditor.remove();
            }
            
            tinymce.init({
                selector: '#tinymce-editor',
                height: 500,
                menubar: false,
                language: 'fr_FR',
                branding: false,
                promotion: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount', 'emoticons'
                ],
                toolbar: 'undo redo | formatselect | bold italic underline strikethrough | \
                         alignleft aligncenter alignright alignjustify | \
                         bullist numlist outdent indent | removeformat | \
                         link image media | code preview fullscreen | help',
                content_style: `
                    body { 
                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif; 
                        font-size: 16px; 
                        line-height: 1.6;
                        color: #333;
                    }
                    img { max-width: 100%; height: auto; }
                    blockquote { 
                        border-left: 4px solid #ddd; 
                        margin: 1em 0; 
                        padding: 0.5em 1em; 
                        background: #f9f9f9; 
                    }
                `,
                setup: function (editor) {
                    tinymceEditor = editor;
                    
                    // Synchronisation avec Livewire
                    editor.on('change', function () {
                        editor.save();
                        @this.set('contentBlocks', editor.getContent());
                    });
                    
                    editor.on('blur', function () {
                        editor.save();
                        @this.set('contentBlocks', editor.getContent());
                    });
                    
                    // Charger le contenu initial
                    editor.on('init', function () {
                        editor.setContent(@this.get('contentBlocks') || '');
                    });
                },
                // Configuration pour les images
                images_upload_handler: function (blobInfo, progress) {
                    return new Promise(function(resolve, reject) {
                        const formData = new FormData();
                        formData.append('file', blobInfo.blob(), blobInfo.filename());
                        
                        // TODO: Implémenter l'upload d'images
                        // Pour le moment, on utilise une image placeholder
                        setTimeout(() => {
                            resolve('https://via.placeholder.com/400x300?text=Image+Upload');
                        }, 1000);
                    });
                },
                file_picker_types: 'image',
                file_picker_callback: function (callback, value, meta) {
                    if (meta.filetype === 'image') {
                        // Ouvrir le modal de sélection de médias
                        @this.dispatch('open-media-selector', {
                            title: 'Sélectionner une image',
                            mode: 'single',
                            callback: 'tinymce-image-selected'
                        });
                        
                        // Stocker le callback pour l'utiliser plus tard
                        window.tinymceCallback = callback;
                    }
                }
            });
        }
        
        // Initialiser au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            // Attendre que TinyMCE soit disponible
            if (typeof tinymce !== 'undefined') {
                initTinyMCE();
            } else {
                // Attendre un peu que le script se charge
                setTimeout(() => {
                    if (typeof tinymce !== 'undefined') {
                        initTinyMCE();
                    }
                }, 1000);
            }
        });
        
        // Réinitialiser après les mises à jour Livewire
        document.addEventListener('livewire:updated', function() {
            setTimeout(() => {
                if (!document.getElementById('tinymce-editor')) return;
                initTinyMCE();
            }, 100);
        });
        
        // Nettoyer avant la fermeture de la page
        window.addEventListener('beforeunload', function() {
            if (tinymceEditor) {
                tinymceEditor.save();
                @this.set('contentBlocks', tinymceEditor.getContent());
            }
        });

        function selectRealImage(mediaId, mediaName) {
            console.log('Image sélectionnée:', mediaId, mediaName);
            @this.call('onMediaSelected', {single: {id: mediaId, name: mediaName}});
        }

        // Auto-save every 60 seconds (plus long avec TinyMCE)
        setInterval(() => {
            if (tinymceEditor) {
                tinymceEditor.save();
                @this.set('contentBlocks', tinymceEditor.getContent());
            }
            @this.dispatch('autosave-trigger');
        }, 60000);
        
        function tagInput(locale) {
            return {
                tagValue: '',
                
                addTag() {
                    if (this.tagValue.trim()) {
                        @this.call('addKeyword', locale, this.tagValue.trim());
                        this.tagValue = '';
                    }
                }
            }
        }

        // Initialiser SortableJS pour le drag & drop de la galerie
        document.addEventListener('livewire:initialized', () => {
            initializeGallerySorting();
        });
        
        // Réinitialiser après mise à jour Livewire
        document.addEventListener('livewire:updated', () => {
            initializeGallerySorting();
        });
        
        function initializeGallerySorting() {
            const galleryContainer = document.getElementById('gallery-images');
            if (galleryContainer && typeof Sortable !== 'undefined') {
                new Sortable(galleryContainer, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    handle: '.drag-handle',
                    onEnd: function(evt) {
                        if (evt.oldIndex !== evt.newIndex) {
                            @this.call('reorderGallery', evt.oldIndex, evt.newIndex);
                        }
                    }
                });
            }
        }
    </script>
    @endpush

    {{-- Styles --}}
    @push('styles')
    <style>
        .news-editor-container {
            position: relative;
        }
        
        .autosave-indicator {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1050;
        }
        
        .available-tags {
            border-top: 1px solid #e9ecef;
            padding-top: 1rem;
        }
        
        .tiptap-editor-container {
            border: 1px solid #e9ecef;
            border-radius: 0.375rem;
            background: white;
        }
        
        /* Styles pour TinyMCE */
        #tinymce-wrapper {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            overflow: hidden;
        }
        
        .tox-tinymce {
            border: none !important;
        }
        
        .tox .tox-toolbar-overlord {
            background: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
        }
        
        .tox .tox-editor-header {
            border: none !important;
        }
        
        /* Améliorer l'apparence des boutons TinyMCE */
        .tox .tox-tbtn {
            color: #495057 !important;
        }
        
        .tox .tox-tbtn:hover {
            background: #e9ecef !important;
            color: #212529 !important;
        }
        
        .tox .tox-tbtn--enabled {
            background: #007bff !important;
            color: white !important;
        }
        
        /* Zone d'édition */
        .tox .tox-edit-area {
            border: none !important;
        }
        
        .tox .tox-edit-area__iframe {
            background: white !important;
        }
        
        .border-dashed {
            border-style: dashed !important;
        }
        
        .gallery-item {
            transition: transform 0.2s ease;
        }
        
        .gallery-item:hover {
            transform: translateY(-2px);
        }
        
        .image-container {
            position: relative;
            overflow: hidden;
            border-radius: 8px;
        }
        
        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.2s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 8px;
        }
        
        .image-container:hover .image-overlay {
            opacity: 1;
        }
        
        .overlay-actions {
            display: flex;
            justify-content: flex-end;
            gap: 4px;
        }
        
        .btn-icon {
            width: 32px;
            height: 32px;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .drag-handle {
            align-self: center;
            color: white;
            font-size: 18px;
            cursor: grab;
        }
        
        .drag-handle:active {
            cursor: grabbing;
        }
        
        .sortable-ghost {
            opacity: 0.5;
        }
        
        .sortable-chosen {
            transform: scale(1.05);
        }
        
        .badge-sm {
            font-size: 0.7rem;
        }
        
        .alert-sm {
            padding: 0.5rem 0.75rem;
            font-size: 0.875rem;
        }
    </style>
    @endpush
</div>