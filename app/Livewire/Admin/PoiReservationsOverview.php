<?php

namespace App\Livewire\Admin;

use App\Models\Poi;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PoiReservationsOverview extends Component
{
    use WithPagination;

    // Filtres
    public $statusFilter = '';

    public $poiFilter = '';

    public $searchFilter = '';

    public $dateFromFilter = '';

    public $dateToFilter = '';

    // Modal
    public $showModal = false;

    public $selectedReservation;

    public $actionType = '';

    public $actionReason = '';

    // Stats globales
    public $globalStats = [];

    // POIs avec réservations
    public $poisWithReservations = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->loadGlobalStats();
        $this->loadPoisWithReservations();
    }

    public function loadGlobalStats()
    {
        $reservations = Reservation::forPois();

        $this->globalStats = [
            'total' => $reservations->count(),
            'pending' => $reservations->where('status', 'pending')->count(),
            'confirmed' => $reservations->where('status', 'confirmed')->count(),
            'cancelled' => $reservations->where('status', 'cancelled')->count(),
            'completed' => $reservations->where('status', 'completed')->count(),
            'total_people' => $reservations->where('status', '!=', 'cancelled')->sum('number_of_people'),
            'today' => $reservations->whereDate('reservation_date', today())->count(),
            'this_week' => $reservations->whereBetween('reservation_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $reservations->whereYear('reservation_date', now()->year)->whereMonth('reservation_date', now()->month)->count(),
        ];
    }

    public function loadPoisWithReservations()
    {
        $this->poisWithReservations = Poi::select('id', 'slug')
            ->with(['translations' => function ($query) {
                $query->where('locale', 'fr');
            }])
            ->whereHas('reservations')
            ->withCount('reservations')
            ->orderByDesc('reservations_count')
            ->limit(20)
            ->get();
    }

    public function getReservationsProperty()
    {
        $query = Reservation::forPois()
            ->with(['reservable.translations', 'appUser'])
            ->latest('created_at');

        // Filtres
        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->poiFilter) {
            $query->where('reservable_id', $this->poiFilter);
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

        if ($this->dateFromFilter) {
            $query->whereDate('reservation_date', '>=', $this->dateFromFilter);
        }

        if ($this->dateToFilter) {
            $query->whereDate('reservation_date', '<=', $this->dateToFilter);
        }

        return $query->paginate(20);
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPoiFilter()
    {
        $this->resetPage();
    }

    public function updatedSearchFilter()
    {
        $this->resetPage();
    }

    public function updatedDateFromFilter()
    {
        $this->resetPage();
    }

    public function updatedDateToFilter()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->statusFilter = '';
        $this->poiFilter = '';
        $this->searchFilter = '';
        $this->dateFromFilter = '';
        $this->dateToFilter = '';
        $this->resetPage();
    }

    public function openActionModal($reservationId, $action)
    {
        $this->selectedReservation = Reservation::with(['appUser', 'reservable.translations'])->find($reservationId);
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
                    session()->flash('success', 'Réservation annulée avec succès.');
                    break;

                case 'complete':
                    $this->selectedReservation->markAsCompleted();
                    session()->flash('success', 'Réservation marquée comme terminée.');
                    break;
            }

            $this->loadGlobalStats();
            $this->loadPoisWithReservations();
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

    public function getTopPoisProperty()
    {
        return DB::table('reservations')
            ->select('reservable_id', DB::raw('COUNT(*) as reservations_count'))
            ->join('pois', 'reservations.reservable_id', '=', 'pois.id')
            ->join('poi_translations', function ($join) {
                $join->on('pois.id', '=', 'poi_translations.poi_id')
                    ->where('poi_translations.locale', '=', 'fr');
            })
            ->where('reservations.reservable_type', Poi::class)
            ->where('reservations.status', '!=', 'cancelled')
            ->groupBy('reservable_id')
            ->orderByDesc('reservations_count')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                $poi = Poi::with('translations')->find($item->reservable_id);

                return [
                    'poi' => $poi,
                    'name' => $poi->translation('fr')->name ?? 'Sans nom',
                    'reservations_count' => $item->reservations_count,
                ];
            });
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
        return view('livewire.admin.poi-reservations-overview', [
            'reservations' => $this->reservations,
            'topPois' => $this->topPois,
        ]);
    }
}
