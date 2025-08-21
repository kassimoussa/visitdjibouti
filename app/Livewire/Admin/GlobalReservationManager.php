<?php

namespace App\Livewire\Admin;

use App\Models\Reservation;
use App\Models\Poi;
use App\Models\Event;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class GlobalReservationManager extends Component
{
    use WithPagination;

    // Filtres
    public $typeFilter = ''; // '', 'poi', 'event'
    public $statusFilter = '';
    public $searchFilter = '';
    public $dateFromFilter = '';
    public $dateToFilter = '';
    public $resourceFilter = ''; // ID spécifique d'un POI ou Event
    
    // Modal
    public $showModal = false;
    public $selectedReservation;
    public $actionType = '';
    public $actionReason = '';
    
    // Stats globales
    public $globalStats = [];
    
    // Resources avec réservations (POIs et Events)
    public $resourcesWithReservations = [];

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->loadGlobalStats();
        $this->loadResourcesWithReservations();
    }

    public function loadGlobalStats()
    {
        $allReservations = Reservation::query();
        $poiReservations = Reservation::forPois();
        $eventReservations = Reservation::forEvents();
        
        $this->globalStats = [
            // Totaux généraux
            'total' => $allReservations->count(),
            'total_poi' => $poiReservations->count(),
            'total_event' => $eventReservations->count(),
            
            // Par statut
            'pending' => $allReservations->where('status', 'pending')->count(),
            'confirmed' => $allReservations->where('status', 'confirmed')->count(),
            'cancelled' => $allReservations->where('status', 'cancelled')->count(),
            'completed' => $allReservations->where('status', 'completed')->count(),
            
            // Par période
            'today' => $allReservations->whereDate('reservation_date', today())->count(),
            'this_week' => $allReservations->whereBetween('reservation_date', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'this_month' => $allReservations->whereYear('reservation_date', now()->year)
                                          ->whereMonth('reservation_date', now()->month)->count(),
            
            // Personnes
            'total_people' => $allReservations->where('status', '!=', 'cancelled')->sum('number_of_people'),
        ];
    }

    public function loadResourcesWithReservations()
    {
        // POIs avec réservations
        $poisWithReservations = Poi::select('id', 'slug')
            ->with(['translations' => function($query) {
                $query->where('locale', 'fr');
            }])
            ->whereHas('reservations')
            ->withCount('reservations')
            ->orderByDesc('reservations_count')
            ->limit(20)
            ->get()
            ->map(function($poi) {
                return [
                    'type' => 'poi',
                    'id' => $poi->id,
                    'name' => $poi->translation('fr')->name ?? 'Sans nom',
                    'slug' => $poi->slug,
                    'reservations_count' => $poi->reservations_count
                ];
            });

        // Events avec réservations
        $eventsWithReservations = Event::select('id', 'slug')
            ->with(['translations' => function($query) {
                $query->where('locale', 'fr');
            }])
            ->whereHas('reservations')
            ->withCount('reservations')
            ->orderByDesc('reservations_count')
            ->limit(20)
            ->get()
            ->map(function($event) {
                return [
                    'type' => 'event',
                    'id' => $event->id,
                    'name' => $event->translation('fr')->title ?? 'Sans nom',
                    'slug' => $event->slug,
                    'reservations_count' => $event->reservations_count
                ];
            });

        // Fusionner et trier par nombre de réservations
        $this->resourcesWithReservations = $poisWithReservations
            ->concat($eventsWithReservations)
            ->sortByDesc('reservations_count')
            ->take(30)
            ->values()
            ->toArray();
    }

    public function getReservationsProperty()
    {
        $query = Reservation::with(['reservable.translations', 'appUser'])
                           ->latest('created_at');

        // Filtres
        if ($this->typeFilter) {
            if ($this->typeFilter === 'poi') {
                $query->forPois();
            } elseif ($this->typeFilter === 'event') {
                $query->forEvents();
            }
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->resourceFilter) {
            $query->where('reservable_id', $this->resourceFilter);
        }

        if ($this->searchFilter) {
            $query->where(function($q) {
                $q->where('guest_name', 'like', '%' . $this->searchFilter . '%')
                  ->orWhere('guest_email', 'like', '%' . $this->searchFilter . '%')
                  ->orWhere('confirmation_number', 'like', '%' . $this->searchFilter . '%')
                  ->orWhereHas('appUser', function($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->searchFilter . '%')
                               ->orWhere('email', 'like', '%' . $this->searchFilter . '%');
                  });
            });
        }

        if ($this->dateFromFilter) {
            $query->whereDate('reservation_date', '>=', $this->dateFromFilter);
        }

        if ($this->dateToFilter) {
            $query->whereDate('reservation_date', '<=', $this->dateToFilter);
        }

        return $query->paginate(25);
    }

    public function getFilteredResourcesProperty()
    {
        if (!$this->typeFilter) {
            return $this->resourcesWithReservations;
        }

        return collect($this->resourcesWithReservations)
            ->where('type', $this->typeFilter)
            ->values()
            ->toArray();
    }

    // Méthodes de mise à jour des filtres
    public function updatedTypeFilter()
    {
        $this->resourceFilter = ''; // Reset resource filter when type changes
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedResourceFilter()
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
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->resourceFilter = '';
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
        if (!$this->selectedReservation) {
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
            $this->loadResourcesWithReservations();
            $this->closeModal();
            
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

    public function getTopResourcesProperty()
    {
        return collect($this->resourcesWithReservations)
            ->take(10)
            ->toArray();
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

    public function getResourceTypeBadge($reservableType)
    {
        if (str_contains($reservableType, 'Poi')) {
            return ['class' => 'bg-primary', 'label' => 'POI'];
        } elseif (str_contains($reservableType, 'Event')) {
            return ['class' => 'bg-success', 'label' => 'Event'];
        }
        return ['class' => 'bg-secondary', 'label' => 'Unknown'];
    }

    public function render()
    {
        return view('livewire.admin.global-reservation-manager', [
            'reservations' => $this->reservations,
            'filteredResources' => $this->filteredResources,
            'topResources' => $this->topResources
        ]);
    }
}