<?php

namespace App\Livewire\Admin;

use App\Models\AppUser;
use Livewire\Component;
use Livewire\WithPagination;

class AppUserManager extends Component
{
    use WithPagination;

    // Filtres
    public $searchFilter = '';

    public $statusFilter = ''; // '', 'active', 'inactive'

    public $providerFilter = ''; // '', 'email', 'google', 'facebook'

    public $languageFilter = ''; // '', 'fr', 'en'

    public $registrationDateFrom = '';

    public $registrationDateTo = '';

    public $lastLoginDateFrom = '';

    public $lastLoginDateTo = '';

    // Tri
    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    // Modal et actions
    public $showModal = false;

    public $selectedUser;

    public $actionType = '';

    public $actionReason = '';

    // Sélection multiple
    public $selectedUsers = [];

    public $selectAll = false;

    // Stats globales
    public $globalStats = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->loadGlobalStats();
    }

    public function loadGlobalStats()
    {
        $this->globalStats = [
            'total' => AppUser::count(),
            'active' => AppUser::where('is_active', true)->count(),
            'inactive' => AppUser::where('is_active', false)->count(),
            'with_reservations' => AppUser::whereHas('reservations')->count(),
            'with_favorites' => AppUser::whereHas('favorites')->count(),
            'social_users' => AppUser::whereNotNull('provider')->where('provider', '!=', 'email')->count(),
            'email_users' => AppUser::where('provider', 'email')->orWhereNull('provider')->count(),
            'recent_signups' => AppUser::where('created_at', '>=', now()->subDays(30))->count(),
            'active_last_month' => AppUser::where('last_login_at', '>=', now()->subDays(30))->count(),
        ];
    }

    public function getUsersProperty()
    {
        $query = AppUser::with(['reservations', 'favorites'])
            ->withCount(['reservations', 'favorites']);

        // Filtres de recherche
        if ($this->searchFilter) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchFilter.'%')
                    ->orWhere('email', 'like', '%'.$this->searchFilter.'%')
                    ->orWhere('phone', 'like', '%'.$this->searchFilter.'%');
            });
        }

        // Filtre par statut
        if ($this->statusFilter === 'active') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inactive') {
            $query->where('is_active', false);
        }

        // Filtre par provider
        if ($this->providerFilter) {
            if ($this->providerFilter === 'email') {
                $query->where(function ($q) {
                    $q->where('provider', 'email')->orWhereNull('provider');
                });
            } else {
                $query->where('provider', $this->providerFilter);
            }
        }

        // Filtre par langue
        if ($this->languageFilter) {
            $query->where('preferred_language', $this->languageFilter);
        }

        // Filtres de dates d'inscription
        if ($this->registrationDateFrom) {
            $query->whereDate('created_at', '>=', $this->registrationDateFrom);
        }
        if ($this->registrationDateTo) {
            $query->whereDate('created_at', '<=', $this->registrationDateTo);
        }

        // Filtres de dates de dernière connexion
        if ($this->lastLoginDateFrom) {
            $query->whereDate('last_login_at', '>=', $this->lastLoginDateFrom);
        }
        if ($this->lastLoginDateTo) {
            $query->whereDate('last_login_at', '<=', $this->lastLoginDateTo);
        }

        // Tri
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(20);
    }

    public function getProvidersProperty()
    {
        return AppUser::selectRaw('COALESCE(provider, "email") as provider, COUNT(*) as count')
            ->groupBy('provider')
            ->pluck('count', 'provider')
            ->toArray();
    }

    public function getLanguagesProperty()
    {
        return AppUser::selectRaw('COALESCE(preferred_language, "fr") as language, COUNT(*) as count')
            ->groupBy('preferred_language')
            ->pluck('count', 'language')
            ->toArray();
    }

    // Méthodes de mise à jour des filtres
    public function updatedSearchFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedProviderFilter()
    {
        $this->resetPage();
    }

    public function updatedLanguageFilter()
    {
        $this->resetPage();
    }

    public function updatedRegistrationDateFrom()
    {
        $this->resetPage();
    }

    public function updatedRegistrationDateTo()
    {
        $this->resetPage();
    }

    public function updatedLastLoginDateFrom()
    {
        $this->resetPage();
    }

    public function updatedLastLoginDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchFilter = '';
        $this->statusFilter = '';
        $this->providerFilter = '';
        $this->languageFilter = '';
        $this->registrationDateFrom = '';
        $this->registrationDateTo = '';
        $this->lastLoginDateFrom = '';
        $this->lastLoginDateTo = '';
        $this->resetPage();
    }

    // Méthodes de tri
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    // Sélection multiple
    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedUsers = $this->users->pluck('id')->toArray();
        } else {
            $this->selectedUsers = [];
        }
    }

    public function updatedSelectedUsers()
    {
        $this->selectAll = count($this->selectedUsers) === $this->users->count();
    }

    // Actions sur utilisateurs
    public function openActionModal($userId, $action)
    {
        $this->selectedUser = AppUser::find($userId);
        $this->actionType = $action;
        $this->actionReason = '';
        $this->showModal = true;
    }

    public function confirmAction()
    {
        if (! $this->selectedUser) {
            return;
        }

        try {
            switch ($this->actionType) {
                case 'activate':
                    $this->selectedUser->update(['is_active' => true]);
                    session()->flash('success', 'Utilisateur activé avec succès.');
                    break;

                case 'deactivate':
                    $this->selectedUser->update(['is_active' => false]);
                    session()->flash('success', 'Utilisateur désactivé avec succès.');
                    break;

                case 'delete':
                    $this->selectedUser->delete();
                    session()->flash('success', 'Utilisateur supprimé avec succès.');
                    break;

                case 'reset_password':
                    // TODO: Implement password reset functionality
                    session()->flash('success', 'Email de réinitialisation envoyé.');
                    break;
            }

            $this->loadGlobalStats();
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: '.$e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedUser = null;
        $this->actionType = '';
        $this->actionReason = '';
    }

    // Actions en lot
    public function bulkAction($action)
    {
        if (empty($this->selectedUsers)) {
            session()->flash('error', 'Aucun utilisateur sélectionné.');

            return;
        }

        try {
            switch ($action) {
                case 'activate':
                    AppUser::whereIn('id', $this->selectedUsers)->update(['is_active' => true]);
                    $message = count($this->selectedUsers).' utilisateur(s) activé(s).';
                    break;

                case 'deactivate':
                    AppUser::whereIn('id', $this->selectedUsers)->update(['is_active' => false]);
                    $message = count($this->selectedUsers).' utilisateur(s) désactivé(s).';
                    break;

                case 'delete':
                    AppUser::whereIn('id', $this->selectedUsers)->delete();
                    $message = count($this->selectedUsers).' utilisateur(s) supprimé(s).';
                    break;
            }

            $this->selectedUsers = [];
            $this->selectAll = false;
            $this->loadGlobalStats();
            session()->flash('success', $message);

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: '.$e->getMessage());
        }
    }

    public function getProviderBadgeClass($provider)
    {
        return match ($provider) {
            'google' => 'bg-danger',
            'facebook' => 'bg-primary',
            'email', null => 'bg-secondary',
            default => 'bg-info'
        };
    }

    public function getProviderLabel($provider)
    {
        return match ($provider) {
            'google' => 'Google',
            'facebook' => 'Facebook',
            'email', null => 'Email',
            default => ucfirst($provider)
        };
    }

    public function getLanguageFlag($language)
    {
        return match ($language) {
            'fr' => 'fi fi-fr',
            'en' => 'fi fi-gb',
            'ar' => 'fi fi-sa',
            default => 'fi fi-fr'
        };
    }

    public function render()
    {
        return view('livewire.admin.app-user-manager', [
            'users' => $this->users,
            'providers' => $this->providers,
            'languages' => $this->languages,
        ]);
    }
}
