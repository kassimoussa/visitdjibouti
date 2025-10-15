<?php

namespace App\Livewire\Admin;

use App\Models\Event;
use App\Models\Poi;
use App\Models\Reservation;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

class ReservationManager extends Component
{
    use WithPagination;

    public $reservableType; // 'poi' ou 'event'

    public $reservableId;

    public $reservable;

    // Filtres
    public $statusFilter = '';

    public $searchFilter = '';

    public $dateFilter = '';

    // Modal
    public $showModal = false;

    public $selectedReservation;

    public $actionType = '';

    public $actionReason = '';

    // Stats
    public $stats = [];

    protected $paginationTheme = 'bootstrap';

    public function mount($reservableType, $reservableId)
    {
        $this->reservableType = $reservableType;
        $this->reservableId = $reservableId;

        // Charger l'entité réservable
        if ($reservableType === 'poi') {
            $this->reservable = Poi::findOrFail($reservableId);
        } else {
            $this->reservable = Event::findOrFail($reservableId);
        }

        $this->loadStats();
    }

    public function loadStats()
    {
        $reservations = $this->reservable->reservations();

        $this->stats = [
            'total' => $reservations->count(),
            'pending' => $reservations->where('status', 'pending')->count(),
            'confirmed' => $reservations->where('status', 'confirmed')->count(),
            'cancelled' => $reservations->where('status', 'cancelled')->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
            'total_people' => $reservations->where('status', '!=', 'cancelled')->sum('number_of_people'),
        ];
    }

    public function getReservationsProperty()
    {
        $query = $this->reservable->reservations()
            ->with(['appUser'])
            ->latest('created_at');

        // Filtres
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->searchFilter) {
            $query->where(function ($q) {
                $q->where('guest_name', 'like', '%'.$this->searchFilter.'%')
                    ->orWhere('guest_email', 'like', '%'.$this->searchFilter.'%')
                    ->orWhere('confirmation_number', 'like', '%'.$this->searchFilter.'%')
                    ->orWhereHas('appUser', function ($userQuery) {
                        $userQuery->where('name', 'like', '%'.$this->searchFilter.'%')
                            ->orWhere('email', 'like', '%'.$this->searchFilter.'%');
                    });
            });
        }

        if ($this->dateFilter) {
            $query->whereDate('reservation_date', $this->dateFilter);
        }

        return $query->paginate(15);
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSearchFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->statusFilter = '';
        $this->searchFilter = '';
        $this->dateFilter = '';
        $this->resetPage();
    }

    public function openActionModal($reservationId, $action)
    {
        $this->selectedReservation = Reservation::with(['appUser'])->find($reservationId);
        $this->actionType = $action;
        $this->actionReason = '';
        $this->showModal = true;
    }

    public function confirmAction()
    {
        if (! $this->selectedReservation) {
            return;
        }

        try {
            switch ($this->actionType) {
                case 'confirm':
                    $this->selectedReservation->confirm();
                    session()->flash('success', 'Réservation confirmée avec succès.');
                    break;

                case 'cancel':
                    $this->selectedReservation->cancel($this->actionReason);

                    // Décrémenter les participants pour les événements
                    if ($this->reservable instanceof Event) {
                        $this->reservable->decrement('current_participants', $this->selectedReservation->number_of_people);
                    }

                    session()->flash('success', 'Réservation annulée avec succès.');
                    break;

                case 'complete':
                    $this->selectedReservation->markAsCompleted();
                    session()->flash('success', 'Réservation marquée comme terminée.');
                    break;
            }

            $this->loadStats();
            $this->closeModal();

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur: '.$e->getMessage());
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedReservation = null;
        $this->actionType = '';
        $this->actionReason = '';
    }

    #[On('reservation-updated')]
    public function refreshReservations()
    {
        $this->loadStats();
    }

    public function getStatusBadgeClass($status)
    {
        return match ($status) {
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
        return match ($status) {
            'pending' => 'En attente',
            'confirmed' => 'Confirmée',
            'cancelled' => 'Annulée',
            'completed' => 'Terminée',
            'no_show' => 'Absent',
            default => $status
        };
    }

    public function render()
    {
        return view('livewire.admin.reservation-manager', [
            'reservations' => $this->reservations,
        ]);
    }
}
