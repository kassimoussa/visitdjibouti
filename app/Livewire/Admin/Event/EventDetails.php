<?php

namespace App\Livewire\Admin\Event;

use Livewire\Component;
use App\Models\Event;

class EventDetails extends Component
{
    public $event;
    public $currentLocale;
    public $activeTab = 'details'; // details, registrations, statistics
    
    /**
     * Montage du composant
     */
    public function mount($eventId)
    {
        // Charger l'événement avec ses relations
        $this->event = Event::with([
            'categories', 
            'media', 
            'creator', 
            'featuredImage', 
            'translations',
            'reservations.appUser',
            'reviews' => function($query) {
                $query->approved()->latest();
            }
        ])->findOrFail($eventId);
            
        // Initialiser la langue courante avec la langue de l'application
        $this->currentLocale = app()->getLocale();
    }
    
    /**
     * Changer la langue d'affichage
     */
    public function changeLocale($locale)
    {
        if (in_array($locale, ['fr', 'en', 'ar'])) {
            $this->currentLocale = $locale;
        }
        // Émettre un événement pour signaler le changement de langue
        $this->dispatch('event-locale-updated');
    }
    
    /**
     * Changer l'onglet actif
     */
    public function changeTab($tab)
    {
        if (in_array($tab, ['details', 'registrations', 'statistics'])) {
            $this->activeTab = $tab;
        }
    }
    
    /**
     * Obtenir le statut de l'événement avec style
     */
    public function getEventStatus()
    {
        $now = now();
        $today = $now->toDateString();
        
        if ($this->event->end_date < $today) {
            return [
                'label' => 'Terminé',
                'class' => 'bg-secondary',
                'icon' => 'fas fa-check-circle'
            ];
        } elseif ($this->event->start_date <= $today && $this->event->end_date >= $today) {
            return [
                'label' => 'En cours',
                'class' => 'bg-success',
                'icon' => 'fas fa-play-circle'
            ];
        } else {
            return [
                'label' => 'À venir',
                'class' => 'bg-primary',
                'icon' => 'fas fa-calendar-alt'
            ];
        }
    }
    
    /**
     * Obtenir les inscriptions groupées par statut
     */
    public function getGroupedRegistrations()
    {
        return [
            'confirmed' => $this->event->reservations()->confirmed()->latest()->get(),
            'pending' => $this->event->reservations()->pending()->latest()->get(),
            'cancelled' => $this->event->reservations()->where('status', 'cancelled')->latest()->get(),
        ];
    }
    
    /**
     * Obtenir les statistiques détaillées
     */
    public function getDetailedStats()
    {
        $reservations = $this->event->reservations;
        
        return [
            'total_registrations' => $reservations->count(),
            'confirmed_registrations' => $reservations->where('status', 'confirmed')->count(),
            'pending_registrations' => $reservations->where('status', 'pending')->count(),
            'cancelled_registrations' => $reservations->where('status', 'cancelled')->count(),
            'total_participants' => $reservations->where('status', 'confirmed')->sum('number_of_people'),
            'total_revenue' => $reservations->where('payment_status', 'paid')->sum('payment_amount'),
            'pending_payments' => $reservations->where('payment_status', 'pending')->sum('payment_amount'),
        ];
    }
    public function getEventStats()
    {
        $confirmedRegistrations = $this->event->reservations()->confirmed()->count();
        $pendingRegistrations = $this->event->reservations()->pending()->count();
        $totalParticipants = $this->event->reservations()
            ->confirmed()
            ->sum('number_of_people');
        
        $approvedReviews = $this->event->reviews()->approved()->count();
        $averageRating = $this->event->reviews()->approved()->avg('rating');
        
        return [
            'confirmed_registrations' => $confirmedRegistrations,
            'pending_registrations' => $pendingRegistrations,
            'total_participants' => $totalParticipants,
            'approved_reviews' => $approvedReviews,
            'average_rating' => $averageRating ? round($averageRating, 1) : null,
            'available_spots' => $this->event->available_spots,
            'is_sold_out' => $this->event->is_sold_out,
        ];
    }
    
    /**
     * Obtenir les heures formatées
     */
    public function getFormattedTime($time)
    {
        if (!$time) return null;
        
        return \Carbon\Carbon::parse($time)->format('H:i');
    }
    
    /**
     * Obtenir les dates formatées
     */
    public function getFormattedDateRange()
    {
        if ($this->event->start_date->isSameDay($this->event->end_date)) {
            return $this->event->start_date->format('d/m/Y');
        }
        
        return $this->event->start_date->format('d/m/Y') . ' - ' . $this->event->end_date->format('d/m/Y');
    }
    
    /**
     * Obtenir le prix formaté
     */
    public function getFormattedPrice()
    {
        if (!$this->event->price) {
            return 'Gratuit';
        }
        
        return number_format($this->event->price, 0, ',', ' ') . ' DJF';
    }
    
    /**
     * Rendu du composant
     */
    public function render()
    {
        $eventStatus = $this->getEventStatus();
        $eventStats = $this->getEventStats();
        $groupedRegistrations = $this->getGroupedRegistrations();
        $detailedStats = $this->getDetailedStats();
        
        return view('livewire.admin.event.event-details', [
            'availableLocales' => ['fr', 'en', 'ar'],
            'eventStatus' => $eventStatus,
            'eventStats' => $eventStats,
            'groupedRegistrations' => $groupedRegistrations,
            'detailedStats' => $detailedStats,
            'formattedDateRange' => $this->getFormattedDateRange(),
            'formattedPrice' => $this->getFormattedPrice(),
            'startTime' => $this->getFormattedTime($this->event->start_time),
            'endTime' => $this->getFormattedTime($this->event->end_time),
        ]);
    } 
}
