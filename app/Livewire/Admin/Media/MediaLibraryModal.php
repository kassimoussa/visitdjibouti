<?php

namespace App\Livewire\Admin\Media;

use App\Models\Media;
use App\Models\MediaTranslation;
use Livewire\Component;
use Livewire\WithFileUploads;

class MediaLibraryModal extends Component
{
    use WithFileUploads;

    public bool $show = false;

    public $mediaList = [];

    public $selectedMediaId = null;

    public $upload;

    public function mount()
    {
        $this->loadMedia();
    }

    public function loadMedia()
    {
        $this->mediaList = Media::with('translations')->latest()->get();
    }

    public function select($mediaId)
    {
        $this->selectedMediaId = $mediaId;
        $this->dispatch('media-selected', id: $mediaId);
        $this->close();
    }

    public function uploadFile()
    {
        $this->validate([
            'upload' => 'required|file|max:20480|mimes:jpg,jpeg,png,gif,webp,svg,mp4',
        ]);

        $file = $this->upload;
        $path = $file->store('media', 'public');

        $media = Media::create([
            'filename' => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'path' => 'storage/'.$path,
            'thumbnail_path' => 'storage/'.$path, // You may generate thumbnail here
            'type' => str_contains($file->getClientMimeType(), 'video') ? 'video' : 'image',
        ]);

        // Création traduction FR par défaut
        MediaTranslation::create([
            'media_id' => $media->id,
            'locale' => config('app.fallback_locale', 'fr'),
            'title' => $media->original_name,
            'alt_text' => '',
            'description' => '',
        ]);

        $this->upload = null;
        $this->loadMedia();
        $this->dispatch('media-uploaded', id: $media->id);
    }

    public function open()
    {
        $this->show = true;
        $this->loadMedia();
    }

    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.admin.media.media-library-modal');
    }
}
