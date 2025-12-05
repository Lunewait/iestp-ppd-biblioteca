<?php

namespace App\Livewire;

use App\Models\Material;
use Livewire\Component;
use Livewire\WithPagination;

class MaterialsList extends Component
{
    use WithPagination;

    public $search = '';
    public $filterType = 'all';
    public $searchMode = 'simple'; // simple or advanced
    public $advancedAuthor = '';
    public $advancedYear = '';
    public $sortBy = 'created_at';

    protected $queryString = ['search', 'filterType', 'sortBy'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingFilterType()
    {
        $this->resetPage();
    }

    public function delete(Material $material)
    {
        $this->authorize('delete_material');
        $material->delete();
        $this->dispatch('notify', message: 'Material eliminado exitosamente', type: 'success');
    }

    public function showDetails($materialId)
    {
        $this->dispatch('open-detail-modal', materialId: $materialId);
    }

    public function render()
    {
        $query = Material::query();

        if ($this->search) {
            $query->where('title', 'like', "%{$this->search}%")
                  ->orWhere('author', 'like', "%{$this->search}%")
                  ->orWhere('code', 'like', "%{$this->search}%");
        }

        if ($this->advancedAuthor) {
            $query->where('author', 'like', "%{$this->advancedAuthor}%");
        }

        if ($this->advancedYear) {
            $query->whereYear('created_at', $this->advancedYear);
        }

        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        // Para estudiantes, solo mostrar materiales disponibles
        // Los administradores ven todos los materiales
        if (auth()->check() && auth()->user()->hasRole('Estudiante')) {
            $query->where(function ($q) {
                // Materiales digitales siempre disponibles
                $q->where('type', 'digital')
                  // O materiales físicos con stock y sin préstamos activos
                  ->orWhere(function ($subQ) {
                      $subQ->where('type', '!=', 'digital')
                           ->whereHas('materialFisico', function ($fisicoQ) {
                               $fisicoQ->where('available', '>', 0);
                           })
                           ->whereDoesntHave('prestamos', function ($prestamoQ) {
                               $prestamoQ->where('status', 'activo')
                                        ->whereIn('approval_status', ['pending', 'approved']);
                           });
                  });
            });
        }

        $query->orderBy($this->sortBy, 'desc');

        $materials = $query->with(['materialFisico', 'materialDigital'])
                          ->paginate(10);

        return view('livewire.materials-list', [
            'materials' => $materials,
        ]);
    }
}
