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

    public function render()
    {
        $query = Prestamo::query();

        // Different behavior for students vs admin
        if (auth()->user()?->hasRole('Estudiante')) {
            // Students see their OWN loan history (collected -> returned)
            $query->where('user_id', auth()->id())
                ->whereIn('approval_status', ['collected', 'returned'])
                ->whereIn('status', ['activo', 'devuelto']);
        } else {
            // Admin/Workers see ALL returned loans (history)
            // For active loan management, they use loan-approvals page
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('usuario', function ($subQ) {
                    $subQ->where('name', 'like', "%{$this->search}%");
                })->orWhereHas('material', function ($subQ) {
                    $subQ->where('title', 'like', "%{$this->search}%");
                });
            });
        }

        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'activo') {
                $query->where('approval_status', 'collected')
                    ->where('status', 'activo');
            } elseif ($this->filterStatus === 'devuelto') {
                $query->where('status', 'devuelto');
            } elseif ($this->filterStatus === 'vencido') {
                $query->where('approval_status', 'collected')
                    ->where('status', 'activo')
                    ->where('fecha_devolucion_esperada', '<', now());
            }
        }

        $query->orderBy($this->sortBy, 'desc');

        $loans = $query->with(['usuario', 'material', 'registradoPor', 'multas'])
            ->paginate(10);

        return view('livewire.loans-list', [
            'loans' => $loans,
        ]);
    }
}

