<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Prestamo;
use Livewire\Component;
use Livewire\WithPagination;

class LoansList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = 'all';
    public $sortBy = 'created_at';

    protected $queryString = ['search', 'filterStatus', 'sortBy'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function returnLoan($loanId)
    {
        $loan = Prestamo::findOrFail($loanId);
        $this->authorize('return_loan');

        if ($loan->status !== 'activo') {
            session()->flash('error', 'Este préstamo no está activo');
            return;
        }

        $loan->update([
            'fecha_devolucion_actual' => now(),
            'status' => 'devuelto',
        ]);

        if ($loan->material->materialFisico) {
            $loan->material->materialFisico->increment('available');
        }

        if ($loan->isOverdue()) {
            $daysLate = now()->diffInDays($loan->fecha_devolucion_esperada);
            $fineAmount = $daysLate * 1.50;

            \App\Models\Multa::create([
                'prestamo_id' => $loan->id,
                'user_id' => $loan->user_id,
                'monto' => $fineAmount,
                'razon' => "Devolución tardía ({$daysLate} días)",
                'status' => 'pendiente',
                'registrado_por' => auth()->id(),
            ]);
        }

        session()->flash('success', 'Préstamo devuelto exitosamente');
    }



    public function render()
    {
        $query = Prestamo::query();

        // Students only see their own loans
        if (auth()->user()?->hasRole('Estudiante')) {
            $query->where('user_id', auth()->id());
        }

        if ($this->search) {
            $query->whereHas('usuario', function ($q) {
                $q->where('name', 'like', "%{$this->search}%");
            })->orWhereHas('material', function ($q) {
                $q->where('title', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'vencido') {
                $query->where('status', 'activo')
                    ->where('fecha_devolucion_esperada', '<', now());
            } else {
                $query->where('status', $this->filterStatus);
            }
        } else {
            // Por defecto, para administradores/trabajadores, no mostrar el historial (devueltos)
            // a menos que se filtre explícitamente.
            if (!auth()->user()?->hasRole('Estudiante')) {
                $query->where('status', '!=', 'devuelto');
            }
        }

        $query->orderBy($this->sortBy, 'desc');

        $loans = $query->with(['usuario', 'material', 'registradoPor'])
            ->paginate(10);

        return view('livewire.loans-list', [
            'loans' => $loans,
        ]);
    }
}
