<div>
    <div x-data="{ show: @entangle('show') }">
        <div class="modal fade show d-block" tabindex="-1" x-show="show" style="background: rgba(0,0,0,0.5);" @keydown.escape.window="$wire.close()" x-cloak>
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Bibliothèque de médias</h5>
                        <button type="button" class="btn-close" @click="$wire.close()"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row">
                            <!-- Upload -->
                            <div class="col-md-4">
                                <h6>Téléverser un média</h6>
                                <form wire:submit.prevent="uploadFile">
                                    <input type="file" wire:model="upload" class="form-control mb-2">
                                    @error('upload') <small class="text-danger">{{ $message }}</small> @enderror
                                    <button type="submit" class="btn btn-primary btn-sm">Envoyer</button>
                                </form>
                            </div>

                            <!-- Media Library -->
                            <div class="col-md-8">
                                <h6>Médias existants</h6>
                                <div class="row g-2" style="max-height: 400px; overflow-y: auto;">
                                    @forelse ($mediaList as $media)
                                        <div class="col-3">
                                            <div class="border p-1" style="cursor:pointer;" @click="$wire.select({{ $media->id }})">
                                                @if(Str::startsWith($media->mime_type, 'image/'))
                                                    <img src="{{ asset($media->thumbnail_path) }}" class="img-fluid" alt="{{ $media->alt_text }}">
                                                @elseif(Str::startsWith($media->mime_type, 'video/'))
                                                    <video src="{{ asset($media->path) }}" class="img-fluid" controls></video>
                                                @else
                                                    <div class="text-muted small">{{ $media->original_name }}</div>
                                                @endif
                                                <div class="small text-center mt-1">{{ $media->title }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <p>Aucun média trouvé.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        @if($selectedMediaId)
                            <div class="me-auto text-success">
                                ID sélectionné : {{ $selectedMediaId }}
                            </div>
                        @endif
                        <button class="btn btn-secondary" @click="$wire.close()">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
