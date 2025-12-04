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

    /**
     * Obtener préstamos aprobados del usuario actual que están pendientes de recogida
     */
    #[Computed]
    public function approvedLoansToCollect()
    {
        if (!auth()->user()?->hasRole('Estudiante')) {
            return collect();
        }

        return Prestamo::where('user_id', auth()->id())
            ->where('approval_status', 'approved')
            ->whereNull('fecha_recogida')
            ->with('material')
            ->get();
    }

    /**
     * Verificar si el usuario tiene préstamos aprobados pendientes de recoger
     */
    #[Computed]
    public function hasApprovedLoansToCollect()
    {
        return $this->approvedLoansToCollect->count() > 0;
    }

    /**
     * Obtener préstamos con tiempo de recogida expirado (para el admin)
     */
    #[Computed]
    public function expiredCollectionLoans()
    {
        if (!auth()->user()?->can('approve_loan')) {
            return collect();
        }

        return Prestamo::where('approval_status', 'approved')
            ->whereNull('fecha_recogida')
            ->where('fecha_limite_recogida', '<', now())
            ->with(['usuario', 'material'])
            ->get();
    }

    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
