<?php

namespace App\Exports;

use App\Models\Prestamo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoansExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Prestamo::with(['usuario', 'material'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Usuario',
            'Email',
            'Material',
            'Fecha Préstamo',
            'Fecha Devolución Esperada',
            'Fecha Devolución Real',
            'Estado',
            'Días de Retraso',
            'Multa',
        ];
    }

    public function map($loan): array
    {
        $daysLate = 0;
        $fine = 0;

        if ($loan->status === 'activo' && $loan->fecha_devolucion_esperada < now()) {
            $daysLate = now()->diffInDays($loan->fecha_devolucion_esperada);
            $fine = $daysLate * 1.50; // S/. 1.50 por día
        }

        return [
            $loan->id,
            $loan->usuario->name ?? 'N/A',
            $loan->usuario->email ?? 'N/A',
            $loan->material->title ?? 'N/A',
            $loan->fecha_prestamo->format('d/m/Y'),
            $loan->fecha_devolucion_esperada->format('d/m/Y'),
            $loan->fecha_devolucion_real ? $loan->fecha_devolucion_real->format('d/m/Y') : 'Pendiente',
            ucfirst($loan->status),
            $daysLate > 0 ? $daysLate : 0,
            $fine > 0 ? 'S/. ' . number_format($fine, 2) : 'S/. 0.00',
        ];
    }
}
