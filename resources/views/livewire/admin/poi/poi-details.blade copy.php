<div>
    <div class="container-fluid">
        <!-- En-tête avec boutons d'action -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3">{{ $poi->name }}</h1>
            <div>
                <a href="{{ route('pois.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                <a href="{{ route('pois.edit', $poi) }}" class="btn btn-primary">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
            </div>
        </div>
        
        <div class="row">
            <!-- Informations principales -->
            <div class="col-lg-8">
                <!-- Image principale -->
                <div class="card mb-4">
                    <div class="card-body p-0">
                        @if($poi->featuredImage)
                            <img src="{{ asset($poi->featuredImage->path) }}" 
                                 alt="{{ $poi->name }}" class="img-fluid w-100 h-50 rounded-top">
                        @else
                            <div class="bg-light text-center py-5">
                                <i class="fas fa-image fa-4x text-muted"></i>
                                <p class="mt-3 text-muted">Aucune image principale</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                <!-- Description -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Description</h5>
                    </div>
                    <div class="card-body">
                        @if($poi->short_description)
                            <div class="alert alert-light">
                                <strong>Description courte :</strong> {{ $poi->short_description }}
                            </div>
                        @endif
                        
                        <div>
                            {!! nl2br(e($poi->description)) !!}
                        </div>
                    </div>
                </div>
                
                <!-- Galerie d'images -->
                @if($poi->media->isNotEmpty())
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Galerie d'images</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                @foreach($poi->media as $mediaItem)
                                    <div class="col-md-3">
                                        <a href="{{ asset($mediaItem->path) }}" target="_blank">
                                            <img src="{{ asset($mediaItem->path) }}" 
                                                 alt="{{ $mediaItem->title ?? $mediaItem->original_name }}"
                                                 class="img-fluid rounded border">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Conseils aux visiteurs -->
                @if($poi->tips)
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Conseils aux visiteurs</h5>
                        </div>
                        <div class="card-body">
                            {!! nl2br(e($poi->tips)) !!}
                        </div>
                    </div>
                @endif
            </div>
            
            <!-- Informations complémentaires -->
            <div class="col-lg-4">
                <!-- Statut et options -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Statut</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if($poi->status === 'published')
                                <span class="badge bg-success">Publié</span>
                            @elseif($poi->status === 'draft')
                                <span class="badge bg-warning">Brouillon</span>
                            @else
                                <span class="badge bg-secondary">Archivé</span>
                            @endif
                            
                            @if($poi->is_featured)
                                <span class="badge bg-info ms-2">À la une</span>
                            @endif
                            
                            @if($poi->allow_reservations)
                                <span class="badge bg-primary ms-2">Réservations activées</span>
                            @endif
                        </div>
                        
                        <div class="text-muted small">
                            <div>Créé le {{ $poi->created_at->format('d/m/Y à H:i') }}</div>
                            <div>Dernière mise à jour le {{ $poi->updated_at->format('d/m/Y à H:i') }}</div>
                            @if($poi->creator)
                                <div>Créé par {{ $poi->creator->name }}</div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Catégories -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Catégories</h5>
                    </div>
                    <div class="card-body">
                        @if($poi->categories->isNotEmpty())
                            <div>
                                @foreach($poi->categories as $category)
                                    <span class="badge mb-2 me-1" style="background-color: {{ $category->color ?? '#6c757d' }}">
                                        {{ $category->icon ?? '' }} {{ $category->name }}
                                    </span>
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted">Aucune catégorie associée</p>
                        @endif
                    </div>
                </div>
                
                <!-- Localisation -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Localisation</h5>
                    </div>
                    <div class="card-body">
                        @if($poi->region)
                            <div class="mb-2">
                                <strong>Région :</strong> {{ $poi->region }}
                            </div>
                        @endif
                        
                        @if($poi->address)
                            <div class="mb-2">
                                <strong>Adresse :</strong> {{ $poi->address }}
                            </div>
                        @endif
                        
                        @if($poi->latitude && $poi->longitude)
                            <div class="mb-2">
                                <strong>Coordonnées GPS :</strong><br>
                                Latitude: {{ $poi->latitude }}<br>
                                Longitude: {{ $poi->longitude }}
                            </div>
                            
                            <!-- Ici, on pourrait ajouter une petite carte -->
                        @endif
                    </div>
                </div>
                
                <!-- Informations pratiques -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Informations pratiques</h5>
                    </div>
                    <div class="card-body">
                        @if($poi->opening_hours)
                            <div class="mb-3">
                                <strong>Horaires d'ouverture :</strong><br>
                                {!! nl2br(e($poi->opening_hours)) !!}
                            </div>
                        @endif
                        
                        @if($poi->entry_fee)
                            <div class="mb-3">
                                <strong>Prix d'entrée :</strong><br>
                                {{ $poi->entry_fee }}
                            </div>
                        @endif
                        
                        @if($poi->contact)
                            <div class="mb-3">
                                <strong>Contact :</strong><br>
                                {{ $poi->contact }}
                            </div>
                        @endif
                        
                        @if($poi->website)
                            <div class="mb-3">
                                <strong>Site web :</strong><br>
                                <a href="{{ $poi->website }}" target="_blank">{{ $poi->website }}</a>
                            </div>
                        @endif
                        
                        @if(!$poi->opening_hours && !$poi->entry_fee && !$poi->contact && !$poi->website)
                            <p class="text-muted">Aucune information pratique disponible</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>