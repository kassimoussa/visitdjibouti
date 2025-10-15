<?php

namespace App\Livewire\Admin\TourOperator;

use App\Models\TourOperatorUser;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\On;
use Livewire\Component;

class UserFormModal extends Component
{
    public $tourOperatorId;

    public $showModal = false;

    // Form fields
    public $name = '';

    public $username = '';

    public $email = '';

    public $password = '';

    public $password_confirmation = '';

    public $phone_number = '';

    public $position = '';

    public $language_preference = 'fr';

    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:tour_operator_users,username',
        'email' => 'required|email|unique:tour_operator_users,email',
        'password' => 'required|string|min:8|confirmed',
        'phone_number' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'language_preference' => 'required|in:fr,en,ar',
        'is_active' => 'boolean',
    ];

    public function mount($tourOperatorId)
    {
        $this->tourOperatorId = $tourOperatorId;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->username = '';
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->phone_number = '';
        $this->position = '';
        $this->language_preference = 'fr';
        $this->is_active = true;
        $this->resetValidation();
    }

    #[On('open-add-user-modal')]
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    #[On('resetForm')]
    public function handleResetForm()
    {
        $this->resetForm();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function save()
    {
        $this->validate();

        $user = TourOperatorUser::create([
            'tour_operator_id' => $this->tourOperatorId,
            'name' => $this->name,
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone_number' => $this->phone_number,
            'position' => $this->position,
            'language_preference' => $this->language_preference,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Utilisateur créé avec succès.');

        $this->closeModal();

        // Émettre les événements pour rafraîchir la liste
        $this->dispatch('user-created');
        $this->dispatch('refreshUserList');
    }

    public function render()
    {
        return view('livewire.admin.tour-operator.user-form-modal');
    }
}
