<?php

namespace App\Livewire\Admin;

use App\Models\Media;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class MediaUpload extends Component
{
    use WithFileUploads;

    public $file;

    public $successMessage = '';

    public $errorMessage = '';

    public function save()
    {
        try {
            $this->validate([
                'file' => 'required|file|max:10240|mimes:jpg,jpeg,png,gif,svg,pdf,doc,docx,xls,xlsx,mp4,webm',
            ]);

            if (! $this->file) {
                $this->errorMessage = 'Aucun fichier sélectionné.';

                return;
            }

            $originalName = $this->file->getClientOriginalName();
            $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)).'_'.time().'.'.$this->file->getClientOriginalExtension();

            // Déterminer le type de média
            $type = $this->getMediaType($this->file->getClientOriginalExtension());

            // Stocker le fichier
            $path = $this->file->storeAs("media/{$type}", $fileName, 'public');

            // Créer l'enregistrement dans la base de données
            Media::create([
                'filename' => $fileName,
                'original_name' => $originalName,
                'mime_type' => $this->file->getMimeType(),
                'size' => $this->file->getSize(),
                'path' => 'storage/'.$path,
                'thumbnail_path' => null,
                'type' => $type,
                'title' => pathinfo($originalName, PATHINFO_FILENAME),
                'alt_text' => '',
                'description' => '',
            ]);

            $this->reset('file');
            $this->successMessage = 'Fichier téléversé avec succès!';

            // Rediriger après un court délai
            $this->dispatch('mediaUploaded');

        } catch (\Exception $e) {
            $this->errorMessage = 'Erreur lors du téléversement: '.$e->getMessage();
        }
    }

    private function getMediaType($extension)
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
        $documentExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
        $videoExtensions = ['mp4', 'webm'];

        if (in_array(strtolower($extension), $imageExtensions)) {
            return 'images';
        } elseif (in_array(strtolower($extension), $documentExtensions)) {
            return 'documents';
        } elseif (in_array(strtolower($extension), $videoExtensions)) {
            return 'videos';
        }

        return 'others';
    }

    public function render()
    {
        return view('livewire.admin.media.media-upload');
    }
}
