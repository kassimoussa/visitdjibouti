<?php

namespace App\Livewire\Admin\TourOperator;

use App\Models\TourOperatorUser;
use App\Mail\TourOperatorInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On;

class UserFormModal extends Component
{
    public $tourOperatorId;
    public $showModal = false;

    // Form fields
    public $name = '';
    public $email = '';
    public $phone_number = '';
    public $position = '';
    public $language_preference = 'fr';
    public $permissions = [];
    public $is_active = true;
    public $send_invitation = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:tour_operator_users,email',
        'phone_number' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'language_preference' => 'required|in:fr,en,ar',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'send_invitation' => 'boolean',
    ];

    public function mount($tourOperatorId)
    {
        $this->tourOperatorId = $tourOperatorId;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->phone_number = '';
        $this->position = '';
        $this->language_preference = 'fr';
        $this->is_active = true;
        $this->send_invitation = true;
        $this->permissions = [
            'manage_events' => true,
            'view_reservations' => true,
            'manage_reservations' => true,
            'manage_tours' => true,
            'view_reports' => true,
            'manage_profile' => true,
        ];
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

        $password = Str::random(12);

        $user = TourOperatorUser::create([
            'tour_operator_id' => $this->tourOperatorId,
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($password),
            'phone_number' => $this->phone_number,
            'position' => $this->position,
            'language_preference' => $this->language_preference,
            'permissions' => $this->permissions,
            'is_active' => $this->is_active,
        ]);

        // Envoyer l'invitation par email si demandé
        if ($this->send_invitation) {
            $this->sendInvitationEmail($user, $password);
        }

        session()->flash('success', 'Utilisateur créé avec succès. ' .
            ($this->send_invitation ? 'Un email d\'invitation a été envoyé.' : 'Mot de passe temporaire: ' . $password));

        $this->closeModal();

        // Émettre les événements pour rafraîchir la liste
        $this->dispatch('user-created');
        $this->dispatch('refreshUserList');
    }

    protected function sendInvitationEmail($user, $password)
    {
        try {
            $loginUrl = route('operator.login');

            Mail::to($user->email)->send(
                new TourOperatorInvitation($user, $password, $loginUrl)
            );

            return true;
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email invitation: ' . $e->getMessage());
            return false;
        }
    }

    public function render()
    {
        return view('livewire.admin.tour-operator.user-form-modal');
    }
}