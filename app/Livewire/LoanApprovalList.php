<?php

namespace App\Livewire;

use App\Models\Prestamo;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Livewire\Component;
use Livewire\WithPagination;

class LoanApprovalList extends Component
{
    use AuthorizesRequests, WithPagination;

    public $filterStatus = 'pending';
    public $searchQuery = '';
    public $selectedLoan = null;
    public $approvalReason = '';
    public $showApprovalModal = false;
    public $approvingLoanId = null;
    public $actionType = 'approve'; // approve or reject

    protected $queryString = ['filterStatus', 'searchQuery'];

    public function mount()
    {
        // Check authorization
        $user = auth()->user();
        if (!$user || !($user->hasRole('Admin') || $user->hasRole('Jefe_Area') || $user->hasRole('Trabajador'))) {
            abort(403);
        }
    }

    #[\Livewire\Attributes\Computed]
    public function pendingLoans()
    {
        return Prestamo::where('approval_status', $this->filterStatus)
            ->when($this->searchQuery, function ($query) {
                $query->whereHas('usuario', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchQuery . '%')
                      ->orWhere('email', 'like', '%' . $this->searchQuery . '%');
                })->orWhereHas('material', function ($q) {
                    $q->where('title', 'like', '%' . $this->searchQuery . '%'); // Corregido 'titulo' a 'title'
                });
            })
            ->with(['usuario', 'material', 'registradoPor'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    }

    public function openApprovalModal($loanId, $action = 'approve')
    {
        $this->approvingLoanId = $loanId;
        $this->actionType = $action;
        $this->showApprovalModal = true;
        $this->approvalReason = '';
    }

    public function approveLoan()
    {
        if (!$this->approvingLoanId) {
            return;
        }

        $loan = Prestamo::findOrFail($this->approvingLoanId);

        // Update loan status
        $loan->update([
            'approval_status' => 'approved',
            'status' => 'activo', // Cambiar estado a activo
            'approved_by' => auth()->id(),
            'approval_date' => now(),
            'approval_reason' => $this->approvalReason,
        ]);

        // Log approval
        $loan->approvalLogs()->create([
            'reviewer_id' => auth()->id(),
            'action' => 'approved',
            'notes' => $this->approvalReason ?: 'Aprobado por ' . auth()->user()->name,
        ]);

        $this->dispatch('notify',
            message: 'Préstamo aprobado exitosamente',
            type: 'success'
        );

        $this->closeApprovalModal();
        $this->resetPage();
    }

    public function rejectLoan()
    {
        if (!$this->approvingLoanId) {
            return;
        }

        $loan = Prestamo::findOrFail($this->approvingLoanId);

        if (!$this->approvalReason) {
            $this->dispatch('notify',
                message: 'Debes proporcionar una razón para rechazar',
                type: 'error'
            );
            return;
        }

        // Update loan status
        $loan->update([
            'approval_status' => 'rejected',
            'approved_by' => auth()->id(),
            'approval_date' => now(),
            'approval_reason' => $this->approvalReason,
        ]);

        // Log rejection
        $loan->approvalLogs()->create([
            'reviewer_id' => auth()->id(),
            'action' => 'rejected',
            'notes' => $this->approvalReason,
        ]);

        $this->dispatch('notify',
            message: 'Préstamo rechazado',
            type: 'warning'
        );

        $this->closeApprovalModal();
        $this->resetPage();
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->approvingLoanId = null;
        $this->approvalReason = '';
        $this->actionType = 'approve';
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function updatingSearchQuery()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.loan-approval-list', [
            'loans' => $this->pendingLoans(),
        ]);
    }
}
