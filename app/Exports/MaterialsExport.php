<?php

namespace App\Exports;

use App\Models\Material;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MaterialsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Material::with(['materialFisico', 'materialDigital'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Título',
            'Autor',
            'Editorial',
            'Año',
            'ISBN',
            'Tipo',
            'Categoría',
            'Copias Totales',
            'Copias Disponibles',
            'URL (Digital)',
            'Creado',
        ];
    }

    public function map($material): array
    {
        return [
            $material->id,
            $material->title,
            $material->author,
            $material->publisher,
            $material->publication_year,
            $material->isbn,
            $material->type,
            $material->category,
            $material->materialFisico->total_copies ?? 'N/A',
            $material->materialFisico->available ?? 'N/A',
            $material->materialDigital->url ?? 'N/A',
            $material->created_at->format('d/m/Y'),
        ];
    }
}
