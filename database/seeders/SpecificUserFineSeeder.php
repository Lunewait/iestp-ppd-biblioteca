<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Material;
use App\Models\Prestamo;

class SpecificUserFineSeeder extends Seeder
{
    public function run()
    {
        $email = 'estudiante@iestp.local';

        // 1. Buscar el usuario específico
        $student = User::where('email', $email)->first();

        if (!$student) {
            // Intentar buscar por institutional_email si no encuentra por email
            $student = User::where('institutional_email', $email)->first();
        }

        if (!$student) {
            $this->command->error("No se encontró el usuario con email: {$email}");
            return;
        }

        // 2. Buscar un material físico
        $material = Material::where('type', '!=', 'digital')->inRandomOrder()->first();

        if (!$material) {
            $this->command->error('No hay materiales físicos disponibles.');
            return;
        }

        // 3. Crear el préstamo vencido hace 20 días
        // Si venció hace 20 días, y el préstamo dura 7 días, se prestó hace 27 días.
        Prestamo::create([
            'user_id' => $student->id,
            'material_id' => $material->id,
            'fecha_prestamo' => now()->subDays(27),
            'fecha_devolucion_esperada' => now()->subDays(20), // Venció hace 20 días
            'status' => 'activo',
            'registrado_por' => User::role('Admin')->first()->id ?? 1,
        ]);

        $this->command->info("✅ Préstamo vencido (20 días) creado para: {$student->name} ({$email})");
        $this->command->info("📖 Material: {$material->title}");
        $this->command->info("📅 Venció el: " . now()->subDays(20)->format('d/m/Y'));
        $this->command->info("💰 Multa esperada al devolver: S/. 30.00");
    }
}
