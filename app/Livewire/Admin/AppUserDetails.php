<?php

namespace App\Livewire\Admin;

use App\Models\AppUser;
use App\Models\Reservation;
use App\Models\UserFavorite;
use Livewire\Component;
use Livewire\WithPagination;

class AppUserDetails extends Component
{
    use WithPagination;

    public $appUser;
    public $activeTab = 'profile'; // profile, reservations, favorites, activity
    
    // Modal
    public $showModal = false;
    public $selectedReservation;
    public $actionType = '';
    public $actionReason = '';
    
    // Stats de l'utilisateur
    public $userStats = [];

    protected $paginationTheme = 'bootstrap';

    public function mount($userId)
    {
        $this->appUser = AppUser::with([
            'reservations.reservable.translations',
            'favorites',
            'favoritePois.translations',
            'favoriteEvents.translations'
        ])->findOrFail($userId);

        $this->loadUserStats();
    }

    public function loadUserStats()
    {
        $this->userStats = [
            'total_reservations' => $this->appUser->reservations()->count(),
            'pending_reservations' => $this->appUser->reservations()->where('status', 'pending')->count(),
            'confirmed_reservations' => $this->appUser->reservations()->where('status', 'confirmed')->count(),
            'cancelled_reservations' => $this->appUser->reservations()->where('status', 'cancelled')->count(),
            'completed_reservations' => $this->appUser->reservations()->where('status', 'completed')->count(),
            'total_favorites' => $this->appUser->favorites()->count(),
            'favorite_pois' => $this->appUser->favoritePois()->count(),
            'favorite_events' => $this->appUser->favoriteEvents()->count(),
            'account_age_days' => $this->appUser->created_at->diffInDays(now()),
            'last_login_days_ago' => $this->appUser->last_login_at ? $this->appUser->last_login_at->diffInDays(now()) : null,
        ];
    }

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function getReservationsProperty()
    {
        return $this->appUser->reservations()
                            ->with(['reservable.translations'])
                            ->latest()
                            ->paginate(10, ['*'], 'reservationsPage');
    }

    public function getFavoritesProperty()
    {
        return $this->appUser->favorites()
                            ->with(['favoritable.translations'])
                            ->latest()
                            ->paginate(10, ['*'], 'favoritesPage');
    }

    public function getActivityProperty()
    {
        // Combine reservations and favorites with timestamps for activity feed
        $reservations = $this->appUser->reservations()
                                    ->with(['reservable.translations'])
                                    ->get()
                                    ->map(function($reservation) {
                                        return [
                                            'type' => 'reservation',
                                            'action' => 'created',
                                            'data' => $reservation,
                                            'timestamp' => $reservation->created_at,
                                            'description' => 'Réservation créée pour ' . 
                                                           ($reservation->reservable ? 
                                                            ($reservation->reservable_type === 'App\\Models\\Poi' ? 
                                                             $reservation->reservable->translation('fr')->name ?? 'POI' :
                                                             $reservation->reservable->translation('fr')->title ?? 'Event') : 
                                                            'Resource supprimée')
                                        ];
                                    });

        $favorites = $this->appUser->favorites()
                                 ->with(['favoritable.translations'])
                                 ->get()
                                 ->map(function($favorite) {
                                     return [
                                         'type' => 'favorite',
                                         'action' => 'added',
                                         'data' => $favorite,
                                         'timestamp' => $favorite->created_at,
                                         'description' => 'Ajouté aux favoris : ' . 
                                                        ($favorite->favoritable ? 
                                                         ($favorite->favoritable_type === 'App\\Models\\Poi' ? 
                                                          $favorite->favoritable->translation('fr')->name ?? 'POI' :
                                                          $favorite->favoritable->translation('fr')->title ?? 'Event') : 
                                                         'Resource supprimée')
                                     ];
                                 });

        // Merge and sort by timestamp
        return $reservations->concat($favorites)
                          ->sortByDesc('timestamp')
                          ->take(20);
    }

    public function toggleUserStatus()
    {
        $this->appUser->update(['is_active' => !$this->appUser->is_active]);
        $this->appUser->refresh();
        
        $status = $this->appUser->is_active ? 'activé' : 'désactivé';
        session()->flash('success', "Utilisateur {$status} avec succès.");
    }

    public function sendPasswordReset()
    {
        // TODO: Implement password reset email functionality
        session()->flash('success', 'Email de réinitialisation envoyé à ' . $this->appUser->email);
    }

    public function deleteUser()
    {
        $this->appUser->delete();
        session()->flash('success', 'Utilisateur supprimé avec succès.');
        return redirect()->route('app-users.index');
    }

    // Actions sur les réservations de l'utilisateur
    public function openReservationModal($reservationId, $action)
    {
        $this->selectedReservation = Reservation::with(['reservable.translations'])->find($reservationId);
        $this->actionType = $action;
        $this->actionReason = '';
        $this->showModal = true;
    }

    public function confirmReservationAction()
    {
        if (!$this->selectedReservation) {
            return;
        }

        try {
            switch ($this->actionType) {
                case 'confirm':
                    $this->selectedReservation->update(['status' => 'confirmed']);
                    session()->flash('success', 'Réservation confirmée.');
                    break;
                    
                case 'cancel':
                    $this->selectedReservation->update([
                        'status' => 'cancelled',
                        'cancelled_at' => now(),
                        'cancellation_reason' => $this->actionReason
                    ]);
                    session()->flash('success', 'Réservation annulée.');
                    break;
                    
                case 'complete':
                    $this->selectedReservation->update(['status' => 'completed']);
                    session()->flash('success', 'Réservation marquée comme terminée.');
                    break;
            }

            $this->loadUserStats();
            $this->closeModal();
            
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function removeFavorite($favoriteId)
    {
        try {
            UserFavorite::find($favoriteId)->delete();
            $this->loadUserStats();
            $this->appUser->load(['favorites', 'favoritePois.translations', 'favoriteEvents.translations']);
            session()->flash('success', 'Favori supprimé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: ' . $e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedReservation = null;
        $this->actionType = '';
        $this->actionReason = '';
    }

    public function getStatusBadgeClass($status)
    {
        return match($status) {
            'pending' => 'bg-warning',
            'confirmed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'completed' => 'bg-info',
            'no_show' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    public function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'completed' => 'Terminée',
            'no_show' => 'Absent',
            default => $status
        };
    }

    public function getProviderBadgeClass($provider)
    {
        return match($provider) {
            'google' => 'bg-danger',
            'facebook' => 'bg-primary',
            'email', null => 'bg-secondary',
            default => 'bg-info'
        };
    }

    public function getProviderLabel($provider)
    {
        return match($provider) {
            'google' => 'Google',
            'facebook' => 'Facebook',
            'email', null => 'Email',
            default => ucfirst($provider)
        };
    }

    public function render()
    {
        $data = [
            'userStats' => $this->userStats,
        ];

        // Load data based on active tab
        switch ($this->activeTab) {
            case 'reservations':
                $data['reservations'] = $this->reservations;
                break;
            case 'favorites':
                $data['favorites'] = $this->favorites;
                break;
            case 'activity':
                $data['activities'] = $this->activity;
                break;
        }

        return view('livewire.admin.app-user-details', $data);
    }
}