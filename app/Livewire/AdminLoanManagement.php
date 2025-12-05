<?php

namespace App\Livewire;

use App\Models\Prestamo;
use App\Models\Multa;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AdminLoanManagement extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'pending_pickup'; // Default: show pending pickups

    protected $paginationTheme = 'tailwind';

    public function deliver($loanId)
    {
        $loan = Prestamo::find($loanId);

        if (!$loan || $loan->approval_status !== 'approved' || $loan->status !== 'pendiente_recogida') {
            session()->flash('error', 'Este préstamo no está pendiente de entrega.');
            return;
        }

        $loanDays = config('library.default_loan_days', 7);

        $loan->update([
            'approval_status' => 'collected',
            'status' => 'activo',
            'fecha_recogida' => now(),
            'fecha_devolucion_esperada' => now()->addDays($loanDays),
        ]);

        session()->flash('success', '¡Libro entregado! El préstamo de ' . $loanDays . ' días ha comenzado.');
    }

    public function receive($loanId)
    {
        $loan = Prestamo::with('material.materialFisico')->find($loanId);

        if (!$loan || $loan->status !== 'activo' || $loan->approval_status !== 'collected') {
            session()->flash('error', 'Este préstamo no está activo para recibir devolución.');
            return;
        }

        $loan->update([
            'fecha_devolucion_actual' => now(),
            'status' => 'devuelto',
            'approval_status' => 'returned',
        ]);

        // Return stock
        if ($loan->material->materialFisico) {
            $loan->material->materialFisico->increment('available');
        }

        // Check if overdue and create fine
        if ($loan->fecha_devolucion_esperada < now()) {
            $daysLate = now()->diffInDays($loan->fecha_devolucion_esperada);
            $fineRate = config('library.daily_fine_rate', 1.50);
            $fineAmount = $daysLate * $fineRate;

            Multa::create([
                'prestamo_id' => $loan->id,
                'user_id' => $loan->user_id,
                'monto' => $fineAmount,
                'razon' => "Devolución tardía ({$daysLate} días de retraso)",
                'status' => 'pendiente',
                'registrado_por' => auth()->id(),
            ]);

            // Block user
            $user = User::find($loan->user_id);
            $user->blockLoans("Multa generada por devolución tardía de S/. " . number_format($fineAmount, 2));

            session()->flash('warning', 'Libro recibido. Se generó multa de S/. ' . number_format($fineAmount, 2) . ' por ' . $daysLate . ' días de retraso. Usuario bloqueado.');
        } else {
            session()->flash('success', '¡Libro recibido! Devolución a tiempo.');
        }
    }

    public function cancelLoan($loanId)
    {
        $loan = Prestamo::with('material.materialFisico')->find($loanId);

        if (!$loan || $loan->approval_status !== 'approved' || $loan->status !== 'pendiente_recogida') {
            session()->flash('error', 'No se puede cancelar este préstamo.');
            return;
        }

        // Return stock
        if ($loan->material->materialFisico) {
            $loan->material->materialFisico->increment('available');
        }

        $loan->update([
            'approval_status' => 'cancelled',
            'status' => 'cancelado'
        ]);

        session()->flash('success', 'Préstamo cancelado. Stock restaurado.');
    }

    public function render()
    {
        // Auto-expire loans not collected in 24h
        $expiredLoans = Prestamo::where('approval_status', 'approved')
            ->where('status', 'pendiente_recogida')
            ->where('fecha_limite_recogida', '<', now())
            ->with('material.materialFisico')
            ->get();

        foreach ($expiredLoans as $loan) {
            if ($loan->material->materialFisico) {
                $loan->material->materialFisico->increment('available');
            }
            $loan->update([
                'approval_status' => 'expired',
                'status' => 'vencido'
            ]);
        }

        // Build query based on filter
        $query = Prestamo::with(['usuario', 'material']);

        if ($this->search) {
            $query->whereHas('usuario', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })->orWhereHas('material', function ($q) {
                $q->where('title', 'like', "%{$this->search}%");
            });
        }

        switch ($this->statusFilter) {
            case 'pending_pickup':
                $query->where('approval_status', 'approved')
                    ->where('status', 'pendiente_recogida');
                break;
            case 'active':
                $query->where('approval_status', 'collected')
                    ->where('status', 'activo');
                break;
            case 'overdue':
                $query->where('approval_status', 'collected')
                    ->where('status', 'activo')
                    ->where('fecha_devolucion_esperada', '<', now());
                break;
            case 'all':
            default:
                // Show all
                break;
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.admin-loan-management', [
            'loans' => $loans,
        ]);
    }
}
