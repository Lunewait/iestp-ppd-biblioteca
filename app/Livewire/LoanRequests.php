<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Prestamo;
use App\Models\Reserva;
use Livewire\Component;
use Livewire\WithPagination;

class LoanRequests extends Component
{
    use WithPagination;

    public $showRequestModal = false;
    public $selectedMaterialId = null;
    public $search = '';
    public $statusFilter = 'all';
    public $showCatalog = false; // Toggle between requests and catalog

    protected $paginationTheme = 'tailwind';

    public function toggleView()
    {
        $this->showCatalog = !$this->showCatalog;
        $this->reset(['search']);
    }

    public function requestLoan($materialId)
    {
        // Check if user has an active loan for this material
        $activeLoan = Prestamo::where('user_id', auth()->id())
            ->where('material_id', $materialId)
            ->where('status', 'activo')
            ->first();

        if ($activeLoan) {
            session()->flash('error', 'No puedes solicitar este libro porque ya tienes un préstamo activo del mismo.');
            return;
        }

        // Check if user already has a pending/approved reservation
        $existingReservation = Reserva::where('user_id', auth()->id())
            ->where('material_id', $materialId)
            ->whereIn('status', ['pendiente', 'aprobada'])
            ->first();

        if ($existingReservation) {
            session()->flash('error', 'Ya tienes una solicitud activa para este material.');
            return;
        }

        // Create reservation
        Reserva::create([
            'user_id' => auth()->id(),
            'material_id' => $materialId,
            'fecha_reserva' => now(),
            'status' => 'pendiente',
            'posicion_cola' => Reserva::where('material_id', $materialId)
                ->whereIn('status', ['pendiente', 'aprobada'])
                ->count() + 1,
        ]);

        session()->flash('success', 'Solicitud enviada exitosamente. Recibirás una notificación cuando sea aprobada.');
        $this->reset(['showRequestModal', 'selectedMaterialId']);
    }

    public function cancelRequest($reservationId)
    {
        $reservation = Reserva::find($reservationId);

        if ($reservation && $reservation->user_id === auth()->id()) {
            $reservation->update(['status' => 'cancelada']);
            session()->flash('success', 'Solicitud cancelada exitosamente.');
        }
    }

    public function render()
    {
        // Expire old approved reservations
        $expiredReservations = Reserva::where('status', 'aprobada')
            ->where('fecha_expiracion', '<', now())
            ->get();

        foreach ($expiredReservations as $reservation) {
            $reservation->markAsExpired();
        }

        // Get user's reservations
        $query = Reserva::where('user_id', auth()->id())
            ->with('material');

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        $reservations = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get available materials for new requests
        $materials = Material::where('type', '!=', 'digital')
            ->whereHas('materialFisico', function ($q) {
                $q->where('available', '>', 0);
            })
            ->when($this->search, function ($q) {
                $q->where('title', 'like', "%{$this->search}%")
                    ->orWhere('author', 'like', "%{$this->search}%");
            })
            ->with('materialFisico')
            ->get();

        return view('livewire.loan-requests', [
            'reservations' => $reservations,
            'materials' => $materials,
        ]);
    }
}
