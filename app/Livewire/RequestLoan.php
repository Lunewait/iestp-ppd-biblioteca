<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Prestamo;
use App\Models\User;
use Livewire\Component;

class RequestLoan extends Component
{
    public $materials = [];
    public $selectedMaterial = null;
    public $showForm = false;
    public $requestReason = '';
    public $searchMaterial = '';

    public function mount()
    {
        // Only students can request loans
        if (!auth()->user()?->hasRole('Estudiante')) {
            abort(403, 'Solo estudiantes pueden solicitar préstamos');
        }
    }

    #[\Livewire\Attributes\Computed]
    public function availableMaterials()
    {
        // Solo mostrar materiales físicos que estén disponibles
        // (que tengan stock disponible y no estén completamente prestados)
        return Material::where('type', '!=', 'digital')
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->searchMaterial . '%')
                    ->orWhere('author', 'like', '%' . $this->searchMaterial . '%');
            })
            // Filtrar solo materiales que tengan disponibilidad
            ->whereHas('materialFisico', function ($query) {
                $query->where('available', '>', 0);
            })
            // Excluir materiales que ya tienen préstamos activos o pendientes de aprobación
            ->whereDoesntHave('prestamos', function ($query) {
                $query->where('status', 'activo')
                      ->whereIn('approval_status', ['pending', 'approved']);
            })
            ->with('materialFisico')
            ->get();
    }

    public function selectMaterial($materialId)
    {
        // Verificar límite de 3 SOLICITUDES (pending, approved, collected)
        $activeLoanCount = Prestamo::getActiveRequestsCount(auth()->id());

        $maxLoans = config('library.max_active_loans_per_user', 3);
        
        if ($activeLoanCount >= $maxLoans) {
            $this->dispatch('notify', 
                message: "Alcanzó el límite permitido de solicitudes (máximo {$maxLoans}). Debes devolver o esperar a que expire una solicitud para poder solicitar más libros.",
                type: 'error'
            );
            return;
        }

        $this->selectedMaterial = Material::findOrFail($materialId);
        $this->showForm = true;
    }

    public function submitRequest()
    {
        if (!$this->selectedMaterial) {
            $this->dispatch('notify', 
                message: 'Por favor selecciona un material',
                type: 'error'
            );
            return;
        }

        // Verificar nuevamente el límite de 3 SOLICITUDES
        $activeLoanCount = Prestamo::getActiveRequestsCount(auth()->id());

        $maxLoans = config('library.max_active_loans_per_user', 3);
        
        if ($activeLoanCount >= $maxLoans) {
            $this->dispatch('notify', 
                message: "Alcanzó el límite permitido de solicitudes (máximo {$maxLoans})",
                type: 'error'
            );
            return;
        }

        // Verificar que el material aún esté disponible
        if (!$this->selectedMaterial->isAvailable()) {
            $this->dispatch('notify', 
                message: 'Este material ya no está disponible',
                type: 'error'
            );
            return;
        }

        // Verificar que no haya otro préstamo activo/pendiente de este mismo material
        $existingLoan = Prestamo::where('material_id', $this->selectedMaterial->id)
            ->whereIn('approval_status', ['pending', 'approved', 'collected'])
            ->exists();

        if ($existingLoan) {
            $this->dispatch('notify', 
                message: 'Este libro ya está reservado o prestado',
                type: 'error'
            );
            return;
        }

        // Crear solicitud con estado PENDING
        $loan = Prestamo::create([
            'user_id' => auth()->id(),
            'material_id' => $this->selectedMaterial->id,
            'fecha_prestamo' => now(),
            'fecha_devolucion_esperada' => null, // Se establecerá cuando recoja
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => auth()->id(),
            'notas' => $this->requestReason,
        ]);

        // Disminuir stock disponible INMEDIATAMENTE (reserva)
        if ($this->selectedMaterial->materialFisico) {
            $this->selectedMaterial->materialFisico->decrement('available');
        }

        // Log the request
        $loan->approvalLogs()->create([
            'reviewer_id' => auth()->id(),
            'action' => 'requested',
            'notes' => 'Solicitud de préstamo creada por ' . auth()->user()->name,
        ]);

        // Notify admins
        $this->notifyAdmins($loan);

        // Reset form
        $this->reset(['selectedMaterial', 'showForm', 'requestReason', 'searchMaterial']);
        
        $this->dispatch('notify',
            message: 'Solicitud de préstamo enviada. Espera la aprobación del administrador.',
            type: 'success'
        );
    }

    private function notifyAdmins($loan)
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin', 'Jefe_Area']);
        })
        ->where('id', '!=', auth()->id())
        ->get();

        foreach ($admins as $admin) {
            // En producción, enviar email o notificación
            // Para ahora, los admins verán en el dashboard de aprobaciones
        }
    }

    public function cancelRequest()
    {
        $this->reset(['selectedMaterial', 'showForm', 'requestReason']);
    }

    public function render()
    {
        return view('livewire.request-loan', [
            'availableMaterials' => $this->availableMaterials(),
        ]);
    }
}
