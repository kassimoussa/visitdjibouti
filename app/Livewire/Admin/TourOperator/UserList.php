<?php

namespace App\Livewire\Admin\TourOperator;

use App\Models\TourOperatorUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\Attributes\On;

class UserList extends Component
{
    public $tourOperatorId;

    public function mount($tourOperatorId)
    {
        $this->tourOperatorId = $tourOperatorId;
    }

    #[On('user-created')]
    #[On('refreshUserList')]
    public function refreshList()
    {
        // Force le re-render du composant
        $this->render();
    }

    public function toggleStatus($userId)
    {
        $user = TourOperatorUser::findOrFail($userId);
        $user->update(['is_active' => !$user->is_active]);

        session()->flash('success', 'Statut utilisateur modifié avec succès.');

        // Pas besoin de rafraîchir car les données sont déjà dans le state
    }

    public function resetPassword($userId)
    {
        $user = TourOperatorUser::findOrFail($userId);
        $newPassword = Str::random(12);

        $user->update(['password' => Hash::make($newPassword)]);

        session()->flash('success', "Mot de passe réinitialisé pour {$user->name}. Nouveau mot de passe: {$newPassword}");
    }

    public function delete($userId)
    {
        $user = TourOperatorUser::findOrFail($userId);
        $userName = $user->name;
        $user->delete();

        session()->flash('success', "Utilisateur {$userName} supprimé avec succès.");
    }

    public function render()
    {
        $users = TourOperatorUser::where('tour_operator_id', $this->tourOperatorId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.admin.tour-operator.user-list', [
            'users' => $users,
        ]);
    }
}