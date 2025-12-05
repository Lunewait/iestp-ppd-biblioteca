<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Prestamo;
use Livewire\Component;
use Livewire\WithPagination;

class LoanRequests extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $showCatalog = true; // Start with catalog view

    protected $paginationTheme = 'tailwind';

    public function toggleView()
    {
        $this->showCatalog = !$this->showCatalog;
        $this->reset(['search']);
    }

    public function requestLoan($materialId)
    {
        $user = auth()->user();
        $maxLoans = config('library.max_active_loans_per_user', 3);

        // Check if user is blocked
        if ($user->blocked_for_loans) {
            session()->flash('error', 'No puedes solicitar préstamos. Motivo: ' . ($user->blocked_reason ?? 'Cuenta bloqueada.'));
            return;
        }

        // Check if user has pending fines
        $pendingFines = $user->multas()->where('status', 'pendiente')->sum('monto');
        if ($pendingFines > 0) {
            session()->flash('error', 'Tienes multas pendientes por S/. ' . number_format($pendingFines, 2) . '. Debes pagarlas antes de solicitar préstamos.');
            return;
        }

        // Check loan limit
        $activeLoansCount = Prestamo::where('user_id', $user->id)
            ->whereIn('approval_status', ['pending', 'approved', 'collected'])
            ->count();

        if ($activeLoansCount >= $maxLoans) {
            session()->flash('error', 'Has alcanzado el máximo de ' . $maxLoans . ' préstamos permitidos.');
            return;
        }

        $material = Material::with('materialFisico')->find($materialId);

        // Check if material is available
        if (!$material || !$material->materialFisico || $material->materialFisico->available <= 0) {
            session()->flash('error', 'Este material no está disponible actualmente.');
            return;
        }

        // Check if user already has a request for this material
        $existingLoan = Prestamo::where('user_id', $user->id)
            ->where('material_id', $materialId)
            ->whereIn('approval_status', ['pending', 'approved', 'collected'])
            ->first();

        if ($existingLoan) {
            session()->flash('error', 'Ya tienes una solicitud o préstamo activo de este material.');
            return;
        }

        // Create the loan request - AUTO APPROVED
        Prestamo::create([
            'user_id' => $user->id,
            'material_id' => $materialId,
            'fecha_prestamo' => now(),
            'fecha_limite_recogida' => now()->addHours(24), // 24 horas para recoger
            'status' => 'pendiente_recogida',
            'approval_status' => 'approved', // Auto-approved
            'approved_by' => null,
            'approval_date' => now(),
            'registrado_por' => $user->id,
        ]);

        // Decrease available stock
        $material->materialFisico->decrement('available');

        session()->flash('success', '¡Solicitud aprobada automáticamente! Tienes 24 horas para acercarte a la biblioteca a recoger tu libro.');
    }

    public function cancelRequest($loanId)
    {
        $loan = Prestamo::find($loanId);

        if ($loan && $loan->user_id === auth()->id() && $loan->approval_status === 'approved' && $loan->status === 'pendiente_recogida') {
            // Return stock
            if ($loan->material->materialFisico) {
                $loan->material->materialFisico->increment('available');
            }

            $loan->update([
                'approval_status' => 'cancelled',
                'status' => 'cancelado'
            ]);

            session()->flash('success', 'Solicitud cancelada exitosamente.');
        }
    }

    public function render()
    {
        // Expire loans that weren't collected in 24 hours
        $expiredLoans = Prestamo::where('approval_status', 'approved')
            ->where('status', 'pendiente_recogida')
            ->where('fecha_limite_recogida', '<', now())
            ->get();

        foreach ($expiredLoans as $loan) {
            // Return stock
            if ($loan->material->materialFisico) {
                $loan->material->materialFisico->increment('available');
            }

            $loan->update([
                'approval_status' => 'expired',
                'status' => 'vencido'
            ]);
        }

        // Get user's loan requests (pending pickup or active)
        $query = Prestamo::where('user_id', auth()->id())
            ->with('material');

        if ($this->statusFilter !== 'all') {
            $query->where('approval_status', $this->statusFilter);
        }

        $myLoans = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get available materials for new requests
        $materials = Material::where('type', '!=', 'digital')
            ->whereHas('materialFisico')
            ->when($this->search, function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('author', 'like', "%{$this->search}%");
            })
            ->with('materialFisico')
            ->get();

        return view('livewire.loan-requests', [
            'reservations' => $myLoans,
            'materials' => $materials,
        ]);
    }
}

