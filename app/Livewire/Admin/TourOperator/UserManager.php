<?php

namespace App\Livewire\Admin\TourOperator;

use App\Models\TourOperator;
use App\Models\TourOperatorUser;
use App\Mail\TourOperatorInvitation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class UserManager extends Component
{
    use WithPagination;

    public $showCreateForm = false;
    public $editingUserId = null;
    public $search = '';
    public $selectedTourOperator = '';

    // Form fields
    public $tour_operator_id = '';
    public $name = '';
    public $email = '';
    public $phone_number = '';
    public $position = '';
    public $language_preference = 'fr';
    public $permissions = [];
    public $is_active = true;
    public $send_invitation = true;

    protected $rules = [
        'tour_operator_id' => 'required|exists:tour_operators,id',
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:tour_operator_users,email',
        'phone_number' => 'nullable|string|max:255',
        'position' => 'nullable|string|max:255',
        'language_preference' => 'required|in:fr,en,ar',
        'permissions' => 'array',
        'is_active' => 'boolean',
        'send_invitation' => 'boolean',
    ];

    public function mount()
    {
        $this->permissions = [
            'manage_events' => true,
            'view_reservations' => true,
            'manage_reservations' => true,
            'manage_tours' => true,
            'view_reports' => true,
            'manage_profile' => true,
        ];
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showCreateForm = true;
    }

    public function hideCreateForm()
    {
        $this->showCreateForm = false;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->tour_operator_id = '';
        $this->name = '';
        $this->email = '';
        $this->phone_number = '';
        $this->position = '';
        $this->language_preference = 'fr';
        $this->is_active = true;
        $this->send_invitation = true;
        $this->editingUserId = null;
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

    public function save()
    {
        $this->validate();

        $password = Str::random(12);

        $user = TourOperatorUser::create([
            'tour_operator_id' => $this->tour_operator_id,
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

        $this->hideCreateForm();
    }

    public function edit($userId)
    {
        $user = TourOperatorUser::findOrFail($userId);

        $this->editingUserId = $userId;
        $this->tour_operator_id = $user->tour_operator_id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->position = $user->position;
        $this->language_preference = $user->language_preference;
        $this->permissions = $user->permissions ?? [];
        $this->is_active = $user->is_active;
        $this->send_invitation = false;

        $this->showCreateForm = true;
    }

    public function update()
    {
        $this->rules['email'] = 'required|email|unique:tour_operator_users,email,' . $this->editingUserId;
        $this->validate();

        $user = TourOperatorUser::findOrFail($this->editingUserId);

        $user->update([
            'tour_operator_id' => $this->tour_operator_id,
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'position' => $this->position,
            'language_preference' => $this->language_preference,
            'permissions' => $this->permissions,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Utilisateur mis à jour avec succès.');
        $this->hideCreateForm();
    }

    public function toggleStatus($userId)
    {
        $user = TourOperatorUser::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);

        session()->flash('success', 'Statut utilisateur modifié avec succès.');
    }

    public function resetPassword($userId)
    {
        $user = TourOperatorUser::findOrFail($userId);
        $newPassword = Str::random(12);

        $user->update(['password' => Hash::make($newPassword)]);

        $this->sendPasswordResetEmail($user, $newPassword);

        session()->flash('success', 'Mot de passe réinitialisé. Un email a été envoyé à l\'utilisateur.');
    }

    public function delete($userId)
    {
        $user = TourOperatorUser::findOrFail($userId);
        $user->delete();

        session()->flash('success', 'Utilisateur supprimé avec succès.');
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
            // Log l'erreur mais continue le processus
            \Log::error('Erreur envoi email invitation: ' . $e->getMessage());
            return false;
        }
    }

    protected function sendPasswordResetEmail($user, $password)
    {
        // TODO: Implémenter l'envoi d'email de reset
    }

    public function render()
    {
        $tourOperators = TourOperator::where('is_active', true)
            ->with(['translations' => function($query) {
                $query->where('locale', 'fr');
            }])
            ->get()
            ->sortBy(function($operator) {
                return $operator->getTranslatedName('fr');
            });

        $query = TourOperatorUser::with(['tourOperator.translations']);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('position', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->selectedTourOperator) {
            $query->where('tour_operator_id', $this->selectedTourOperator);
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('livewire.admin.tour-operator.user-manager', [
            'users' => $users,
            'tourOperators' => $tourOperators,
        ]);
    }
}