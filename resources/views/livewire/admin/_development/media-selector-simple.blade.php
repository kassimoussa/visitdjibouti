<div>
    {{-- Modal Bootstrap Simple --}}
    <div class="modal fade {{ $isOpen ? 'show' : '' }}" 
         id="mediaModal" 
         tabindex="-1" 
         style="{{ $isOpen ? 'display: block; background: rgba(0,0,0,0.5);' : 'display: none;' }}">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-images me-2"></i>
                        {{ $title }}
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                
                <div class="modal-body">
                    @if($media && $media->count() > 0)
                        <div class="row">
                            @foreach($media as $item)
                            <div class="col-md-2 mb-3">
                                <div class="card {{ in_array($item->id, $selectedMedia) ? 'border-primary' : '' }}" 
                                     style="cursor: pointer;" 
                                     wire:click="toggleMedia({{ $item->id }})">
                                    
                                    @if($item->type === 'image')
                                        <img src="{{ $item->url ?? '/storage/app/public/media/images/' . $item->filename }}" 
                                             alt="{{ $item->name ?? $item->original_name }}" 
                                             class="card-img-top" 
                                             style="height: 120px; object-fit: cover;"
                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMTIwIiBoZWlnaHQ9IjEyMCIgdmlld0JveD0iMCAwIDEyMCAxMjAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIxMjAiIGhlaWdodD0iMTIwIiBmaWxsPSIjZjhmOWZhIi8+CjxwYXRoIGQ9Ik02MCA0MEw4MCA4MEg0MEw2MCA0MFoiIGZpbGw9IiNkZWUyZTYiLz4KPC9zdmc+Cg=='">
                                    @else
                                        <div class="card-img-top d-flex align-items-center justify-content-center bg-light" 
                                             style="height: 120px;">
                                            <i class="fas fa-file fa-2x text-muted"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="card-body p-2">
                                        <small class="text-truncate d-block">{{ $item->name ?? $item->original_name ?? 'Sans nom' }}</small>
                                        @if(in_array($item->id, $selectedMedia))
                                            <i class="fas fa-check-circle text-primary"></i>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-images fa-4x text-muted mb-3"></i>
                            <h5>Aucun média trouvé</h5>
                            <p class="text-muted">Commencez par ajouter quelques images depuis la gestion des médias</p>
                            <a href="{{ route('media.index') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Aller à la gestion des médias
                            </a>
                        </div>
                    @endif
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">
                        Annuler
                    </button>
                    <button type="button" class="btn btn-primary" 
                            wire:click="confirmSelection"
                            {{ empty($selectedMedia) ? 'disabled' : '' }}>
                        <i class="fas fa-check me-2"></i>
                        Sélectionner ({{ count($selectedMedia) }})
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>