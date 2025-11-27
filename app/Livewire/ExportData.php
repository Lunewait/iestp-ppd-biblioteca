<?php

namespace App\Livewire;

use App\Models\Material;
use App\Models\Prestamo;
use Livewire\Component;

class ExportData extends Component
{
    public function exportMaterialsCSV()
    {
        $this->authorize('view_material');

        $materials = Material::all();
        
        $csv = "Título,Autor,Tipo,Categoría,Código\n";
        foreach ($materials as $material) {
            $csv .= "\"{$material->title}\",\"{$material->author}\",\"{$material->type}\",\"{$material->category}\",\"{$material->code}\"\n";
        }

        return response()->streamDownload(
            function () use ($csv) {
                echo $csv;
            },
            'materiales-' . now()->format('Y-m-d-H-i-s') . '.csv'
        );
    }

    public function exportLoansCSV()
    {
        $this->authorize('view_loan');

        $loans = Prestamo::with(['user', 'material'])->get();
        
        $csv = "Material,Usuario,Fecha Préstamo,Vencimiento,Estado\n";
        foreach ($loans as $loan) {
            $status = $loan->is_returned ? 'Devuelto' : ($loan->due_date < now() ? 'Vencido' : 'Activo');
            $csv .= "\"{$loan->material->title}\",\"{$loan->user->name}\",\"{$loan->date_borrowed->format('d/m/Y')}\",\"{$loan->due_date->format('d/m/Y')}\",\"{$status}\"\n";
        }

        return response()->streamDownload(
            function () use ($csv) {
                echo $csv;
            },
            'prestamos-' . now()->format('Y-m-d-H-i-s') . '.csv'
        );
    }

    public function render()
    {
        return view('livewire.export-data');
    }
}
