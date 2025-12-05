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

        // 1. Préstamos RECOGIDOS (Activos - 'collected')
        // El estudiante ya tiene el libro
        foreach ($students->take(3) as $student) {
            $material = $materials->random();
            
            // Decrementar stock (ya lo tiene)
            if ($material->materialFisico) {
                $material->materialFisico->decrement('available');
            }

            $prestamo = Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now()->subDays(2),
                'fecha_recogida' => now()->subDays(2),
                'fecha_devolucion_esperada' => now()->addDays(5), // 7 días total
                'status' => 'activo',
                'approval_status' => 'collected',
                'approved_by' => $worker->id,
                'registrado_por' => $worker->id,
                'notas' => 'Material entregado al estudiante',
                'approval_date' => now()->subDays(3),
            ]);

            // Log de recolección
            $prestamo->approvalLogs()->create([
                'reviewer_id' => $worker->id,
                'action' => 'collected',
                'notes' => 'Material recogido por el estudiante',
            ]);
        }

        // 2. Préstamos APROBADOS (Esperando recogida - 'approved')
        // El estudiante tiene 24h para recoger
        foreach ($students->skip(3)->take(2) as $student) {
            $material = $materials->random();
            
            // Decrementar stock (está reservado)
            if ($material->materialFisico) {
                $material->materialFisico->decrement('available');
            }

            $prestamo = Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now(),
                'fecha_limite_recogida' => now()->addHours(20), // Le quedan 20h
                'fecha_devolucion_esperada' => null, // Se define al recoger
                'status' => 'activo',
                'approval_status' => 'approved',
                'approved_by' => $worker->id,
                'registrado_por' => $student->id,
                'approval_date' => now(),
                'notas' => 'Aprobado, esperando recogida',
            ]);

            $prestamo->approvalLogs()->create([
                'reviewer_id' => $worker->id,
                'action' => 'approved',
                'notes' => 'Aprobado, estudiante tiene 24h para recoger',
            ]);
        }

        // 3. Préstamos PENDIENTES (Solicitudes - 'pending')
        // Esperando aprobación del admin
        foreach ($students->skip(5)->take(2) as $student) {
            $material = $materials->random();
            
            // Decrementar stock (reserva inmediata al solicitar)
            if ($material->materialFisico) {
                $material->materialFisico->decrement('available');
            }

            Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now(),
                'fecha_devolucion_esperada' => null,
                'status' => 'activo',
                'approval_status' => 'pending',
                'registrado_por' => $student->id,
                'notas' => 'Solicitud de préstamo enviada',
            ]);
        }

        // 4. Préstamos VENCIDOS (No devueltos a tiempo - 'collected' + fecha pasada)
        foreach ($students->skip(7)->take(2) as $student) {
            $material = $materials->random();
            
            if ($material->materialFisico) {
                $material->materialFisico->decrement('available');
            }

            $prestamo = Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now()->subDays(15),
                'fecha_recogida' => now()->subDays(15),
                'fecha_devolucion_esperada' => now()->subDays(8), // Vencido hace 8 días
                'status' => 'activo', // Sigue activo hasta que devuelva
                'approval_status' => 'collected',
                'approved_by' => $worker->id,
                'registrado_por' => $worker->id,
                'approval_date' => now()->subDays(15),
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
                'fecha_vencimiento' => now()->addDays(7),
                'registrado_por' => $worker->id,
            ]);
        }
        
        // 5. Préstamos EXPIRADOS (No recogió en 24h - 'expired')
        // El stock YA volvió al catálogo
        foreach ($students->skip(9)->take(2) as $student) {
            $material = $materials->random();
            
            // NO decrementamos stock porque ya expiró y volvió

            Prestamo::create([
                'user_id' => $student->id,
                'material_id' => $material->id,
                'fecha_prestamo' => now()->subDays(2),
                'fecha_limite_recogida' => now()->subDays(1), // Expiró ayer
                'status' => 'vencido',
                'approval_status' => 'expired',
                'approved_by' => $worker->id,
                'registrado_por' => $student->id,
                'approval_date' => now()->subDays(2),
                'notas' => 'No recogido a tiempo',
            ]);
        }
    }
}
