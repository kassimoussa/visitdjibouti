<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use Livewire\Component;
use Livewire\WithFileUploads;

class SimpleMediaUpload extends Component
{
    use WithFileUploads;

    public $file;

    public $successMessage = '';

    public $errorMessage = '';

    public function uploadFile()
    {
        $this->validate([
            'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,svg,pdf,doc,docx,xls,xlsx,mp4,webm',
        ]);

        try {
            $fileName = $this->file->getClientOriginalName();
            $path = $this->file->storeAs('public/media/uploads', $fileName);

            Media::create([
                'filename' => $fileName,
                'original_name' => $fileName,
                'mime_type' => $this->file->getMimeType(),
                'size' => $this->file->getSize(),
                'path' => str_replace('public/', 'storage/', $path),
                'thumbnail_path' => null,
                'type' => 'images', // Simplifié pour le test
                'title' => $fileName,
                'alt_text' => '',
                'description' => '',
            ]);

            $this->reset('file');
            $this->successMessage = 'Fichier téléversé avec succès!';
        } catch (\Exception $e) {
            $this->errorMessage = 'Erreur lors du téléversement: '.$e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.media.simple-media-upload');
    }
}
