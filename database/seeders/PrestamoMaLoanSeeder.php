<?php

namespace Database\Seeders;

use App\Models\Prestamo;
use App\Models\Multa;
use App\Models\User;
use App\Models\Material;
use Illuminate\Database\Seeder;

class PrestamoMaLoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = User::role('Estudiante')->get();
        $worker = User::role('Trabajador')->first();
        $materials = Material::all();

        if ($students->isEmpty() || !$worker || $materials->isEmpty()) {
            return;
        }

        // Crear préstamos activos
        foreach ($students->take(3) as $student) {
            // Préstamo activo (dentro del plazo)
            $prestamo = Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $materials->random()->id,
                'fecha_prestamo' => now()->subDays(5),
                'fecha_devolucion_esperada' => now()->addDays(9),
                'status' => 'activo',
                'approval_status' => 'approved',
                'approved_by' => $worker->id,
                'registrado_por' => $worker->id,
                'notas' => 'Préstamo aprobado por trabajador',
                'approval_date' => now()->subDays(5),
            ]);

            // Log de aprobación
            $prestamo->approvalLogs()->create([
                'reviewer_id' => $worker->id,
                'action' => 'approved',
                'notes' => 'Aprobado por ' . $worker->name,
            ]);
        }

        // Crear préstamos vencidos con multas
        foreach ($students->skip(3)->take(2) as $student) {
            $prestamo = Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $materials->random()->id,
                'fecha_prestamo' => now()->subDays(25),
                'fecha_devolucion_esperada' => now()->subDays(11), // Ya vencido
                'status' => 'activo',
                'approval_status' => 'approved',
                'approved_by' => $worker->id,
                'registrado_por' => $worker->id,
                'approval_date' => now()->subDays(25),
            ]);

        // Crear multa automática
            $diasVencido = now()->diffInDays($prestamo->fecha_devolucion_esperada);
            $monto = $diasVencido * 1.50;

            Multa::create([
                'user_id' => $student->id,
                'prestamo_id' => $prestamo->id,
                'monto' => $monto,
                'razon' => 'Retraso en devolución de material',
                'status' => 'pendiente',
                'fecha_vencimiento' => $prestamo->fecha_devolucion_esperada->addDays(7),
                'registrado_por' => $worker->id,
            ]);
        }

        // Crear préstamos rechazados
        foreach ($students->skip(5)->take(2) as $student) {
            Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $materials->random()->id,
                'fecha_prestamo' => now()->subDays(15),
                'fecha_devolucion_esperada' => now()->subDays(1),
                'status' => 'devuelto',
                'approval_status' => 'rejected',
                'approved_by' => $worker->id,
                'registrado_por' => $student->id,
                'approval_reason' => 'Material no disponible en ese momento',
                'approval_date' => now()->subDays(14),
                'notas' => 'Solicitud rechazada',
            ]);
        }

        // Crear préstamos pendientes de aprobación
        foreach ($students->skip(7)->take(2) as $student) {
            Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $materials->random()->id,
                'fecha_prestamo' => now(),
                'fecha_devolucion_esperada' => now()->addDays(14),
                'status' => 'activo',
                'approval_status' => 'pending',
                'registrado_por' => $student->id,
                'notas' => 'Pendiente de aprobación del administrador',
            ]);
        }
    }
}
