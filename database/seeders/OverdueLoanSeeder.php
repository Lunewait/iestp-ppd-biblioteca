<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Material;
use App\Models\Prestamo;

class OverdueLoanSeeder extends Seeder
{
    public function run()
    {
        // 1. Buscar un estudiante (o crear uno si no existe)
        $student = User::role('Estudiante')->first();

        if (!$student) {
            $this->command->info('No se encontraron estudiantes. Crea uno primero.');
            return;
        }

        // 2. Buscar un material físico disponible
        $material = Material::where('type', '!=', 'digital')->first();

        if (!$material) {
            $this->command->info('No hay materiales físicos disponibles.');
            return;
        }

        // 3. Crear un préstamo con fechas PASADAS (Vencido hace 10 días)
        Prestamo::create([
            'user_id' => $student->id,
            'material_id' => $material->id,
            'fecha_prestamo' => now()->subDays(20), // Se prestó hace 20 días
            'fecha_devolucion_esperada' => now()->subDays(10), // Debió devolverse hace 10 días
            'status' => 'activo', // Aún figura como activo (no devuelto)
            'registrado_por' => User::role('Admin')->first()->id ?? 1,
        ]);

        $this->command->info("¡Préstamo vencido creado para el estudiante: {$student->name}!");
        $this->command->info("Material: {$material->title}");
        $this->command->info("Vencimiento: " . now()->subDays(10)->format('d/m/Y') . " (Hace 10 días)");
    }
}
