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
     * 
     * New loan flow:
     * 1. Student requests -> AUTO APPROVED (status: pendiente_recogida, approval_status: approved)
     * 2. Admin delivers -> ACTIVE (status: activo, approval_status: collected)
     * 3. Admin receives return -> RETURNED (status: devuelto, approval_status: returned)
     */
    public function run(): void
    {
        $students = User::role('Estudiante')->get();
        $worker = User::role('Trabajador')->first() ?? User::role('Admin')->first();
        $materials = Material::where('type', '!=', 'digital')->get();

        if ($students->isEmpty() || !$worker || $materials->isEmpty()) {
            return;
        }

        // 1. PRÉSTAMOS ACTIVOS (Ya entregados al estudiante)
        foreach ($students->take(2) as $student) {
            $material = $materials->random();

            // Decrementar stock
            if ($material->materialFisico) {
                $material->materialFisico->decrement('available');
            }

            Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now()->subDays(3),
                'fecha_limite_recogida' => now()->subDays(3)->addHours(24),
                'fecha_recogida' => now()->subDays(2),
                'fecha_devolucion_esperada' => now()->addDays(5), // 7 días desde recogida
                'status' => 'activo',
                'approval_status' => 'collected',
                'approved_by' => $worker->id,
                'registrado_por' => $student->id,
                'approval_date' => now()->subDays(3),
                'notas' => 'Material entregado al estudiante',
            ]);
        }

        // 2. PRÉSTAMOS APROBADOS (Esperando recogida - 24h)
        foreach ($students->skip(2)->take(2) as $student) {
            $material = $materials->random();

            // Decrementar stock (reservado)
            if ($material->materialFisico) {
                $material->materialFisico->decrement('available');
            }

            Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now(),
                'fecha_limite_recogida' => now()->addHours(18), // Le quedan 18h
                'fecha_devolucion_esperada' => null, // Se define al entregar
                'status' => 'pendiente_recogida',
                'approval_status' => 'approved',
                'approved_by' => null, // Auto-aprobado
                'registrado_por' => $student->id,
                'approval_date' => now(),
                'notas' => 'Aprobado automáticamente, esperando recogida',
            ]);
        }

        // 3. PRÉSTAMOS DEVUELTOS (Historial limpio)
        foreach ($students->skip(4)->take(2) as $student) {
            $material = $materials->random();

            Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now()->subDays(20),
                'fecha_limite_recogida' => now()->subDays(20)->addHours(24),
                'fecha_recogida' => now()->subDays(19),
                'fecha_devolucion_esperada' => now()->subDays(12),
                'fecha_devolucion_actual' => now()->subDays(13), // Devuelto a tiempo
                'status' => 'devuelto',
                'approval_status' => 'returned',
                'approved_by' => $worker->id,
                'registrado_por' => $student->id,
                'approval_date' => now()->subDays(20),
                'notas' => 'Devuelto correctamente',
            ]);
        }

        // 4. PRÉSTAMOS VENCIDOS (No devueltos a tiempo - tiene multa)
        foreach ($students->skip(6)->take(1) as $student) {
            $material = $materials->random();

            if ($material->materialFisico) {
                $material->materialFisico->decrement('available');
            }

            $prestamo = Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now()->subDays(15),
                'fecha_limite_recogida' => now()->subDays(15)->addHours(24),
                'fecha_recogida' => now()->subDays(14),
                'fecha_devolucion_esperada' => now()->subDays(7), // Vencido hace 7 días
                'status' => 'activo', // Sigue activo hasta que devuelva
                'approval_status' => 'collected',
                'approved_by' => $worker->id,
                'registrado_por' => $student->id,
                'approval_date' => now()->subDays(15),
            ]);

            // Crear multa automática
            $diasVencido = 7;
            $monto = $diasVencido * 1.50;

            Multa::create([
                'user_id' => $student->id,
                'prestamo_id' => $prestamo->id,
                'monto' => $monto,
                'razon' => "Retraso en devolución de material ({$diasVencido} días)",
                'status' => 'pendiente',
                'fecha_vencimiento' => now()->addDays(7),
                'registrado_por' => $worker->id,
            ]);

            // Bloquear usuario
            $student->update([
                'blocked_for_loans' => true,
                'blocked_reason' => 'Multa pendiente por devolución tardía'
            ]);
        }

        // 5. PRÉSTAMOS EXPIRADOS (No recogió en 24h)
        foreach ($students->skip(7)->take(1) as $student) {
            $material = $materials->random();

            Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now()->subDays(3),
                'fecha_limite_recogida' => now()->subDays(2), // Expiró hace 2 días
                'status' => 'vencido',
                'approval_status' => 'expired',
                'approved_by' => null,
                'registrado_por' => $student->id,
                'approval_date' => now()->subDays(3),
                'notas' => 'No recogido a tiempo - stock restaurado',
            ]);
        }
    }
}
