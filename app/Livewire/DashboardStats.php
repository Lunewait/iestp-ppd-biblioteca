<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Prestamo;
use App\Models\Multa;
use Livewire\Component;
use Livewire\Attributes\Computed;

class DashboardStats extends Component
{
    #[Computed]
    public function totalMaterials()
    {
        return Material::count();
    }

    #[Computed]
    public function availableMaterials()
    {
        return Material::where(function($q) {
            $q->where('type', 'fisico')
              ->orWhere('type', 'hibrido');
        })->count();
    }

    #[Computed]
    public function activeLoanCount()
    {
        return Prestamo::where('status', 'activo')->count();
    }

    #[Computed]
    public function overdueLoanCount()
    {
        return Prestamo::where('status', 'activo')
            ->where('fecha_devolucion_esperada', '<', now())
            ->count();
    }

    #[Computed]
    public function pendingFines()
    {
        return Multa::where('status', 'pendiente')->count();
    }

    #[Computed]
    public function totalPendingFines()
    {
        return Multa::where('status', 'pendiente')->sum('monto') ?? 0;
    }

    #[Computed]
    public function recentLoans()
    {
        return Prestamo::with(['usuario', 'material'])
            ->latest('created_at')
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
