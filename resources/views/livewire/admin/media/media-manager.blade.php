<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($uploadView)
        <!-- Vue téléversement -->
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Téléverser des médias</h1>
                <button wire:click="backToList" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </button>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <form wire:submit="save">
                        <div class="mb-4">
                            <label for="files" class="form-label">Choisir des fichiers</label>
                            <input wire:model="files" type="file" id="files" multiple
                                class="form-control @error('files.*') is-invalid @enderror">
                            @error('files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($files && count($files) > 0)
                                <div class="mt-3">
                                    <h6>Fichiers sélectionnés ({{ count($files) }}):</h6>
                                    <div class="d-flex flex-wrap gap-3 mt-2">
                                        @foreach($files as $index => $file)
                                            <div class="d-flex align-items-center p-2 border rounded">
                                                @if (in_array($file->getClientOriginalExtension(), ['jpg', 'jpeg', 'png', 'gif', 'svg']))
                                                    <img src="{{ $file->temporaryUrl() }}" class="img-thumbnail me-3"
                                                        style="max-width: 80px; max-height: 80px;">
                                                @else
                                                    <div class="bg-light rounded p-3 me-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                        @if (in_array($file->getClientOriginalExtension(), ['pdf', 'doc', 'docx']))
                                                            <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                        @elseif(in_array($file->getClientOriginalExtension(), ['mp4', 'webm']))
                                                            <i class="fas fa-file-video fa-2x text-primary"></i>
                                                        @else
                                                            <i class="fas fa-file fa-2x text-secondary"></i>
                                                        @endif
                                                    </div>
                                                @endif
                                                <div class="flex-grow-1">
                                                    <p class="mb-0 fw-medium text-truncate" style="max-width: 200px;">{{ $file->getClientOriginalName() }}</p>
                                                    <p class="mb-0 text-muted small">{{ round($file->getSize() / 1024) }} KB</p>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                                                    wire:click="$set('files.{{ $index }}', null)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Section Multi-Langue pour l'upload -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Informations multilingues</h5>
                            </div>
                            <div class="card-body">
                                <!-- Onglets pour les langues -->
                                <ul class="nav nav-tabs mb-3" id="langTabs" role="tablist">
                                    @foreach($availableLocales as $locale)
                                        <li class="nav-item" role="presentation">
                                            <button class="nav-link {{ $locale === $currentLocale ? 'active' : '' }}" 
                                                   id="lang-{{ $locale }}-tab" 
                                                   wire:click.prevent="changeLocale('{{ $locale }}')"
                                                   type="button" role="tab">
                                                {{ strtoupper($locale) }}
                                                @if($locale === 'fr')
                                                    <span class="text-danger">*</span>
                                                @endif
                                            </button>
                                        </li>
                                    @endforeach
                                </ul>

                                <!-- Contenu des onglets -->
                                <div class="tab-content" id="langTabsContent">
                                    <div class="tab-pane fade show active">
                                        <div class="mb-3">
                                            <label class="form-label" for="translations-{{ $currentLocale }}-title">
                                                Titre 
                                                @if($currentLocale === 'fr') <span class="text-danger">*</span> @endif
                                            </label>
                                            <input wire:model="translations.{{ $currentLocale }}.title" 
                                                  type="text" 
                                                  id="translations-{{ $currentLocale }}-title" 
                                                  class="form-control @error('translations.'.$currentLocale.'.title') is-invalid @enderror">
                                            @error('translations.'.$currentLocale.'.title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="translations-{{ $currentLocale }}-alt_text">
                                                Texte alternatif
                                            </label>
                                            <input wire:model="translations.{{ $currentLocale }}.alt_text" 
                                                  type="text" 
                                                  id="translations-{{ $currentLocale }}-alt_text" 
                                                  class="form-control @error('translations.'.$currentLocale.'.alt_text') is-invalid @enderror">
                                            <div class="form-text">Utilisé pour l'accessibilité et le SEO (pour les images)</div>
                                            @error('translations.'.$currentLocale.'.alt_text')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="translations-{{ $currentLocale }}-description">
                                                Description
                                            </label>
                                            <textarea wire:model="translations.{{ $currentLocale }}.description" 
                                                     id="translations-{{ $currentLocale }}-description" 
                                                     class="form-control @error('translations.'.$currentLocale.'.description') is-invalid @enderror"
                                                     rows="4"></textarea>
                                            @error('translations.'.$currentLocale.'.description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="save">
                                <span wire:loading wire:target="save" class="spinner-border spinner-border-sm me-1"></span>
                                Téléverser {{ count($files ?? []) > 0 ? '(' . count($files) . ' fichiers)' : '' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($editingMedia)
        <!-- Vue édition -->
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Modifier le média</h1>
                <button wire:click="backToList" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </button>
            </div>

            <div class="row">
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Informations du média</h5>
                                <div>
                                    <!-- Sélecteur de langue -->
                                    <div class="btn-group" role="group">
                                        @foreach($availableLocales as $locale)
                                            <button type="button" 
                                                   wire:click="changeLocale('{{ $locale }}')"
                                                   class="btn {{ $locale === $currentLocale ? 'btn-primary' : 'btn-outline-primary' }}">
                                                {{ strtoupper($locale) }}
                                                @if($locale === 'fr')
                                                    <span class="text-white">*</span>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <form wire:submit="updateMediaDetails">
                                <div class="mb-3">
                                    <label for="translations-{{ $currentLocale }}-title" class="form-label">
                                        Titre
                                        @if($currentLocale === 'fr') <span class="text-danger">*</span> @endif
                                    </label>
                                    <input wire:model="translations.{{ $currentLocale }}.title" 
                                           type="text" 
                                           id="translations-{{ $currentLocale }}-title" 
                                           class="form-control @error('translations.'.$currentLocale.'.title') is-invalid @enderror">
                                    @error('translations.'.$currentLocale.'.title')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="translations-{{ $currentLocale }}-alt_text" class="form-label">Texte alternatif</label>
                                    <input wire:model="translations.{{ $currentLocale }}.alt_text" 
                                           type="text" 
                                           id="translations-{{ $currentLocale }}-alt_text" 
                                           class="form-control @error('translations.'.$currentLocale.'.alt_text') is-invalid @enderror">
                                    <div class="form-text">Utilisé pour l'accessibilité et le SEO (pour les images)</div>
                                    @error('translations.'.$currentLocale.'.alt_text')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="translations-{{ $currentLocale }}-description" class="form-label">Description</label>
                                    <textarea wire:model="translations.{{ $currentLocale }}.description" 
                                              id="translations-{{ $currentLocale }}-description" 
                                              class="form-control @error('translations.'.$currentLocale.'.description') is-invalid @enderror" 
                                              rows="4"></textarea>
                                    @error('translations.'.$currentLocale.'.description')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-1"></i> Enregistrer les modifications
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Aperçu</h5>
                        </div>
                        <div class="card-body text-center">
                            @if ($editingMedia->type == 'images')
                                <img src="{{ asset($editingMedia->path) }}" class="img-fluid mb-3"
                                    alt="{{ $translations[$currentLocale]['alt_text'] ?? '' }}" style="max-height: 300px;">
                            @elseif($editingMedia->type == 'documents')
                                <i class="fas fa-file-pdf fa-5x text-danger mb-3"></i>
                            @elseif($editingMedia->type == 'videos')
                                <i class="fas fa-file-video fa-5x text-primary mb-3"></i>
                            @else
                                <i class="fas fa-file fa-5x text-secondary mb-3"></i>
                            @endif

                            <div class="d-grid gap-2">
                                <a href="{{ asset($editingMedia->path) }}" target="_blank"
                                    class="btn btn-outline-primary">
                                    <i class="fas fa-external-link-alt me-1"></i> Ouvrir dans un nouvel onglet
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Détails techniques</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Nom du fichier</span>
                                    <span class="text-muted">{{ $editingMedia->filename }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Type</span>
                                    <span class="text-muted">{{ ucfirst($editingMedia->type) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Taille</span>
                                    <span class="text-muted">{{ humanFileSize($editingMedia->size) }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>MIME type</span>
                                    <span class="text-muted">{{ $editingMedia->mime_type }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>Date d'ajout</span>
                                    <span
                                        class="text-muted">{{ $editingMedia->created_at->format('d/m/Y H:i') }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Vue liste -->
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3">Gestion des médias</h1> 
                <div class="d-flex">
                    <div class="input-group me-2"  style="height: fit-content;">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input wire:model.live.debounce.300ms="search" type="text" class="form-control"
                            placeholder="Rechercher...">
                    </div>
                    <button wire:click="showUploadForm" class="btn btn-primary"
                        style="height: 38px; display: flex; align-items: center;">
                        <i class="fas fa-upload me-1"></i> Téléverser 
                    </button>
                </div>
            </div>

            <!-- Filtres et contrôles de vue -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div class="d-flex">
                            <div class="col-12 me-2">
                                <select wire:model.live="typeFilter" class="form-select me-2">
                                    <option value="">Tous les types</option>
                                    <option value="images">Images</option>
                                    <option value="documents">Documents</option>
                                    <option value="videos">Vidéos</option>
                                    <option value="others">Autres</option>
                                </select>
                            </div>

                           {{--  <select wire:model.live="dateFilter" class="form-select me-2">
                                <option value="">Toutes les dates</option>
                                <option value="today">Aujourd'hui</option>
                                <option value="week">Cette semaine</option>
                                <option value="month">Ce mois</option>
                                <option value="year">Cette année</option>
                            </select> --}}
                            
                            <!-- Sélecteur de langue pour l'affichage -->
                            <div class="col-4">
                                <select wire:model.live="currentLocale" class="form-select me-2">
                                @foreach($availableLocales as $locale)
                                    <option value="{{ $locale }}">{{ strtoupper($locale) }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <!-- Boutons pour changer le mode d'affichage -->
                            <div class="btn-group">
                                <button wire:click="changeViewMode('grid')" type="button"
                                    class="btn btn-sm {{ $viewMode == 'grid' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas fa-th"></i>
                                </button>
                                <button wire:click="changeViewMode('list')" type="button"
                                    class="btn btn-sm {{ $viewMode == 'list' ? 'btn-primary' : 'btn-outline-primary' }}">
                                    <i class="fas fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions en masse -->
            @if (count($selectedItems) > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="fw-bold">{{ count($selectedItems) }} élément(s) sélectionné(s)</span>
                            </div>
                            <div>
                                <button wire:click="$dispatch('openDeleteSelectedModal')"
                                    class="btn btn-danger btn-sm">
                                    <i class="fas fa-trash me-1"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Vue en grille -->
            @if ($viewMode == 'grid')
                <div class="row g-3">
                    @forelse($media as $item)
                        <div class="col-lg-2 col-md-3 col-sm-4 col-6">
                            <div class="card h-100 {{ in_array($item->id, $selectedItems) ? 'border-primary' : '' }}">
                                <div class="position-relative">
                                    <!-- Checkbox de sélection -->
                                    <div class="position-absolute top-0 start-0 m-2">
                                        <div class="form-check">
                                            <input wire:model.live="selectedItems" class="form-check-input"
                                                type="checkbox" value="{{ $item->id }}"
                                                id="check-{{ $item->id }}">
                                        </div>
                                    </div> 

                                    <!-- Prévisualisation -->
                                    <div class="media-thumbnail"
                                        style="height: 150px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa;">
                                        @if ($item->type == 'images')
                                            <img src="{{ asset($item->thumbnail_path ?? $item->path) }}"
                                                class="card-img-top img-fluid" 
                                                alt="{{ $item->getTranslation($currentLocale)->alt_text ?? '' }}"
                                                style="max-height: 150px; width: auto;">
                                        @elseif($item->type == 'documents')
                                            <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        @elseif($item->type == 'videos')
                                            <i class="fas fa-file-video fa-3x text-primary"></i>
                                        @else
                                            <i class="fas fa-file fa-3x text-secondary"></i>
                                        @endif
                                    </div>
                                </div>

                                <div class="card-body p-2">
                                    <h6 class="card-title mb-0 text-truncate" 
                                        title="{{ $item->getTranslation($currentLocale)->title ?? $item->filename }}">
                                        {{ $item->getTranslation($currentLocale)->title ?? $item->filename }}
                                    </h6>
                                    <p class="card-text text-muted small mb-0">{{ humanFileSize($item->size) }}</p>
                                    <p class="card-text text-muted small">{{ $item->created_at->format('d/m/Y') }}</p>
                                </div>

                                <div class="card-footer p-2 d-flex justify-content-around">
                                    <button wire:click="showEditForm({{ $item->id }})"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="{{ asset($item->path) }}" target="_blank"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button wire:click="confirmDelete({{ $item->id }})"
                                        class="btn btn-sm btn-outline-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                Aucun média disponible. Commencez par téléverser des fichiers.
                            </div>
                        </div>
                    @endforelse
                </div>
            @else
                <!-- Vue en liste -->
                <div class="card shadow-sm">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th style="width: 40px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll"
                                                onchange="toggleAllCheckboxes(this)">
                                        </div>
                                    </th>
                                    <th>Fichier</th>
                                    <th>Type</th>
                                    <th>Taille</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($media as $item)
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input wire:model.live="selectedItems"
                                                    class="form-check-input checkboxItem" type="checkbox"
                                                    value="{{ $item->id }}" id="list-check-{{ $item->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3"
                                                    style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                                    @if ($item->type == 'images')
                                                        <img src="{{ asset($item->thumbnail_path ?? $item->path) }}"
                                                            class="img-fluid"
                                                            style="max-width: 40px; max-height: 40px;">
                                                    @elseif($item->type == 'documents')
                                                        <i class="fas fa-file-pdf fa-2x text-danger"></i>
                                                    @elseif($item->type == 'videos')
                                                        <i class="fas fa-file-video fa-2x text-primary"></i>
                                                    @else
                                                        <i class="fas fa-file fa-2x text-secondary"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ $item->getTranslation($currentLocale)->title ?? $item->filename }}
                                                    </div>
                                                    <div class="text-muted small">{{ $item->filename }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if ($item->type == 'images')
                                                <span class="badge bg-info">Image</span>
                                            @elseif($item->type == 'documents')
                                                <span class="badge bg-danger">Document</span>
                                            @elseif($item->type == 'videos')
                                                <span class="badge bg-primary">Vidéo</span>
                                            @else
                                                <span class="badge bg-secondary">Autre</span>
                                            @endif
                                        </td>
                                        <td>{{ humanFileSize($item->size) }}</td>
                                        <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button wire:click="showEditForm({{ $item->id }})"
                                                    class="btn btn-outline-primary">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="{{ asset($item->path) }}" target="_blank"
                                                    class="btn btn-outline-secondary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <button wire:click="confirmDelete({{ $item->id }})"
                                                    class="btn btn-outline-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">Aucun média disponible</div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($media->isEmpty())
                        <div class="text-center p-4">
                            <div class="text-muted">Aucun média disponible. Commencez par téléverser des fichiers.
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $media->links() }}
            </div>
        </div>
    @endif

    <!-- Modals pour les actions de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirmer la suppression</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <p>Êtes-vous sûr de vouloir supprimer ce média? Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteMedia"
                        data-bs-dismiss="modal">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteSelectedModal" tabindex="-1" aria-labelledby="deleteSelectedModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteSelectedModalLabel">Confirmer la suppression en masse</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class="fas fa-exclamation-triangle text-warning fa-3x mb-3"></i>
                    <p>Êtes-vous sûr de vouloir supprimer les {{ count($selectedItems) }} médias sélectionnés? Cette
                        action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" wire:click="deleteSelected"
                        data-bs-dismiss="modal">Supprimer</button>
                </div>
            </div>
        </div>
    </div>

    @script
        <script>
            // On utilise une fonction anonyme immédiatement invoquée pour éviter les problèmes de portée
            (function() {
                // Ces variables sont déclarées dans le scope de la fonction
                var deleteModalElement = document.getElementById('deleteModal');
                var deleteSelectedModalElement = document.getElementById('deleteSelectedModal');
                var deleteModalInstance = null;
                var deleteSelectedModalInstance = null;

                // Initialisation après chargement de Livewire
                document.addEventListener('livewire:initialized', function() {
                    // Initialisation des modals Bootstrap
                    if (deleteModalElement) {
                        deleteModalInstance = new bootstrap.Modal(deleteModalElement);
                    }

                    if (deleteSelectedModalElement) {
                        deleteSelectedModalInstance = new bootstrap.Modal(deleteSelectedModalElement);
                    }

                    // Écoute des événements Livewire
                    Livewire.on('openDeleteModal', function() {
                        if (deleteModalInstance) {
                            deleteModalInstance.show();
                        }
                    });

                    Livewire.on('openDeleteSelectedModal', function() {
                        if (deleteSelectedModalInstance) {
                            deleteSelectedModalInstance.show();
                        }
                    });
                });
            })();

            // Fonction globale pour sélectionner/désélectionner tous les éléments
            function toggleAllCheckboxes(checkbox) {
                const checkboxes = document.getElementsByClassName('checkboxItem');

                if (checkbox.checked) {
                    @this.selectedItems = Array.from(checkboxes).map(item => parseInt(item.value));
                } else {
                    @this.selectedItems = [];
                }
            }
        </script>
    @endscript
</div>