<?php

namespace App\Livewire;

use App\Exports\MaterialsExport;
use App\Exports\LoansExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ExportData extends Component
{
    public function exportMaterials()
    {
        $this->authorize('export_materials');

        return Excel::download(new MaterialsExport, 'materiales_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function exportLoans()
    {
        $this->authorize('export_loans');

        return Excel::download(new LoansExport, 'prestamos_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    public function render()
    {
        return view('livewire.export-data');
    }
}
