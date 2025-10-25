<?php

namespace App\Livewire\Operator;

use App\Models\Media;
use App\Models\TourOperator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TourOperatorForm extends Component
{
    public $tourOperator;

    public $phones = [];

    public $emails = [];

    public $website;

    public $address;

    public $latitude;

    public $longitude;

    public $logo_id;

    public $translations = [];

    public $allMedia = [];

    public $mediaSelectorMode = 'single';

    protected $listeners = ['media-selected' => 'handleMediaSelection'];

    protected function rules()
    {
        return [
            'phones' => 'nullable|array',
            'phones.*' => 'nullable|string|max:20',
            'emails' => 'nullable|array',
            'emails.*' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'address' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'logo_id' => 'nullable|exists:media,id',
            'translations.fr.name' => 'required|string|max:255',
            'translations.fr.description' => 'nullable|string',
            'translations.en.name' => 'nullable|string|max:255',
            'translations.en.description' => 'nullable|string',
        ];
    }

    protected function messages()
    {
        return [
            'translations.fr.name.required' => 'Le nom en français est requis.',
            'website.url' => 'Le site web doit être une URL valide.',
        ];
    }

    public function mount()
    {
        $user = Auth::guard('operator')->user();
        $this->tourOperator = $user->tourOperator;
        $this->allMedia = Media::all();

        // Initialiser les traductions
        $this->translations = [
            'fr' => ['name' => '', 'description' => ''],
            'en' => ['name' => '', 'description' => ''],
        ];

        if ($this->tourOperator) {
            $this->fill($this->tourOperator->only([
                'phones', 'emails', 'website', 'address', 'latitude', 'longitude', 'logo_id',
            ]));

            // Charger les traductions
            foreach ($this->tourOperator->translations as $translation) {
                if (isset($this->translations[$translation->locale])) {
                    $this->translations[$translation->locale] = [
                        'name' => $translation->name ?? '',
                        'description' => $translation->description ?? '',
                    ];
                }
            }

            // Convertir null en tableaux vides pour phones et emails
            $this->phones = $this->phones ?? [];
            $this->emails = $this->emails ?? [];
        }
    }

    public function addPhone()
    {
        $this->phones[] = '';
    }

    public function removePhone($index)
    {
        unset($this->phones[$index]);
        $this->phones = array_values($this->phones);
    }

    public function addEmail()
    {
        $this->emails[] = '';
    }

    public function removeEmail($index)
    {
        unset($this->emails[$index]);
        $this->emails = array_values($this->emails);
    }

    public function handleMediaSelection($selectedIds)
    {
        if ($this->mediaSelectorMode === 'single') {
            $this->logo_id = ! empty($selectedIds) ? $selectedIds[0] : null;
        }
    }

    public function openLogoSelector()
    {
        $this->mediaSelectorMode = 'single';
        $preselected = $this->logo_id ? [$this->logo_id] : [];
        $this->dispatch('open-media-selector', 'single', $preselected);
    }

    public function save()
    {
        $validatedData = $this->validate();

        // Filtrer les phones et emails vides
        $validatedData['phones'] = array_values(array_filter($this->phones, fn ($phone) => ! empty(trim($phone))));
        $validatedData['emails'] = array_values(array_filter($this->emails, fn ($email) => ! empty(trim($email))));

        // Mettre à jour les informations du tour operator
        $this->tourOperator->update([
            'phones' => $validatedData['phones'],
            'emails' => $validatedData['emails'],
            'website' => $this->website,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'logo_id' => $this->logo_id,
        ]);

        // Mettre à jour les traductions
        foreach ($this->translations as $locale => $data) {
            if (! empty($data['name'])) {
                $this->tourOperator->translations()->updateOrCreate(
                    ['locale' => $locale],
                    [
                        'name' => $data['name'],
                        'description' => $data['description'] ?? '',
                    ]
                );
            }
        }

        session()->flash('success', 'Informations mises à jour avec succès.');

        return redirect()->route('operator.tour-operator.show');
    }

    public function render()
    {
        return view('livewire.operator.tour-operator-form');
    }
}
