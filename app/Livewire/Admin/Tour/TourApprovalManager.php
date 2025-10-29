<?php

namespace App\Livewire\Admin\Tour;

use App\Mail\TourApproved;
use App\Mail\TourRejected;
use App\Models\Tour;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithPagination;

class TourApprovalManager extends Component
{
    use WithPagination;

    public $selectedTour = null;
    public $showApprovalModal = false;
    public $showRejectionModal = false;
    public $rejectionReason = '';

    public $filterStatus = 'pending_approval';
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'filterStatus' => ['except' => 'pending_approval'],
    ];

    protected $rules = [
        'rejectionReason' => 'required|string|min:10|max:500',
    ];

    protected $messages = [
        'rejectionReason.required' => 'Vous devez fournir une raison pour le rejet.',
        'rejectionReason.min' => 'La raison doit contenir au moins 10 caractères.',
        'rejectionReason.max' => 'La raison ne peut pas dépasser 500 caractères.',
    ];

    public function mount()
    {
        //
    }

    public function render()
    {
        $query = Tour::with([
            'translations',
            'tourOperator.translations',
            'createdBy',
            'featuredImage'
        ]);

        // Filter by status
        if ($this->filterStatus && $this->filterStatus !== 'all') {
            $query->where('status', $this->filterStatus);
        }

        // Search
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('translations', function ($subQ) {
                    $subQ->where('title', 'LIKE', '%' . $this->search . '%')
                        ->orWhere('description', 'LIKE', '%' . $this->search . '%');
                })
                ->orWhereHas('tourOperator.translations', function ($subQ) {
                    $subQ->where('name', 'LIKE', '%' . $this->search . '%');
                });
            });
        }

        // Only show operator-created tours for approval workflow
        $query->whereNotNull('created_by_operator_user_id');

        // Order by submission date
        $query->orderBy('submitted_at', 'desc')
            ->orderBy('created_at', 'desc');

        $tours = $query->paginate(15);

        // Statistics
        $statistics = [
            'pending' => Tour::whereNotNull('created_by_operator_user_id')
                ->where('status', 'pending_approval')->count(),
            'approved' => Tour::whereNotNull('created_by_operator_user_id')
                ->where('status', 'approved')->count(),
            'rejected' => Tour::whereNotNull('created_by_operator_user_id')
                ->where('status', 'rejected')->count(),
        ];

        return view('livewire.admin.tour.tour-approval-manager', [
            'tours' => $tours,
            'statistics' => $statistics,
        ]);
    }

    public function openApprovalModal($tourId)
    {
        $this->selectedTour = Tour::with(['translations', 'tourOperator.translations', 'createdBy'])
            ->findOrFail($tourId);

        if ($this->selectedTour->status !== 'pending_approval') {
            session()->flash('error', 'Ce tour n\'est pas en attente d\'approbation.');
            return;
        }

        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->selectedTour = null;
    }

    public function approveTour()
    {
        if (!$this->selectedTour || $this->selectedTour->status !== 'pending_approval') {
            session()->flash('error', 'Erreur lors de l\'approbation du tour.');
            $this->closeApprovalModal();
            return;
        }

        $adminId = Auth::guard('admin')->id();

        if ($this->selectedTour->approve($adminId)) {
            // Send approval email to operator
            if ($this->selectedTour->createdBy && $this->selectedTour->createdBy->email) {
                try {
                    Mail::to($this->selectedTour->createdBy->email)
                        ->send(new TourApproved($this->selectedTour));
                } catch (\Exception $e) {
                    \Log::error('Failed to send tour approval email: ' . $e->getMessage());
                }
            }

            session()->flash('success', 'Tour approuvé avec succès.');
            $this->closeApprovalModal();
        } else {
            session()->flash('error', 'Erreur lors de l\'approbation du tour.');
        }
    }

    public function openRejectionModal($tourId)
    {
        $this->selectedTour = Tour::with(['translations', 'tourOperator.translations', 'createdBy'])
            ->findOrFail($tourId);

        if ($this->selectedTour->status !== 'pending_approval') {
            session()->flash('error', 'Ce tour n\'est pas en attente d\'approbation.');
            return;
        }

        $this->rejectionReason = '';
        $this->showRejectionModal = true;
    }

    public function closeRejectionModal()
    {
        $this->showRejectionModal = false;
        $this->selectedTour = null;
        $this->rejectionReason = '';
        $this->resetValidation();
    }

    public function rejectTour()
    {
        $this->validate();

        if (!$this->selectedTour || $this->selectedTour->status !== 'pending_approval') {
            session()->flash('error', 'Erreur lors du rejet du tour.');
            $this->closeRejectionModal();
            return;
        }

        $adminId = Auth::guard('admin')->id();

        if ($this->selectedTour->reject($adminId, $this->rejectionReason)) {
            // Send rejection email to operator
            if ($this->selectedTour->createdBy && $this->selectedTour->createdBy->email) {
                try {
                    Mail::to($this->selectedTour->createdBy->email)
                        ->send(new TourRejected($this->selectedTour));
                } catch (\Exception $e) {
                    \Log::error('Failed to send tour rejection email: ' . $e->getMessage());
                }
            }

            session()->flash('success', 'Tour rejeté avec succès.');
            $this->closeRejectionModal();
        } else {
            session()->flash('error', 'Erreur lors du rejet du tour.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }
}
