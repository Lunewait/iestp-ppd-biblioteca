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
        return Material::where('type', '!=', 'digital')
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->searchMaterial . '%')
                    ->orWhere('author', 'like', '%' . $this->searchMaterial . '%');
            })
            ->get();
    }

    public function selectMaterial($materialId)
    {
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

        // Create loan with pending approval
        $loan = Prestamo::create([
            'user_id' => auth()->id(),
            'material_id' => $this->selectedMaterial->id,
            'fecha_prestamo' => now(),
            'fecha_devolucion_esperada' => now()->addDays(14),
            'status' => 'activo',
            'approval_status' => 'pending',
            'registrado_por' => auth()->id(),
            'notas' => $this->requestReason,
        ]);

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
