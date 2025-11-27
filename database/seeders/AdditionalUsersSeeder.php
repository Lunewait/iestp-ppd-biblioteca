<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdditionalUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear más estudiantes
        $estudiantesData = [
            ['name' => 'Carlos López', 'email' => 'carlos@iestp.local'],
            ['name' => 'María García', 'email' => 'maria@iestp.local'],
            ['name' => 'Juan Martínez', 'email' => 'juan@iestp.local'],
            ['name' => 'Ana Rodríguez', 'email' => 'ana@iestp.local'],
            ['name' => 'Luis Fernández', 'email' => 'luis@iestp.local'],
            ['name' => 'Rosa Pérez', 'email' => 'rosa@iestp.local'],
            ['name' => 'Pedro Sánchez', 'email' => 'pedro@iestp.local'],
            ['name' => 'Elena Morales', 'email' => 'elena@iestp.local'],
        ];

        foreach ($estudiantesData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'institutional_email' => str_replace('@iestp.local', '@iestp.edu.pe', $data['email']),
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole('Estudiante');
        }

        // Crear más trabajadores
        $trabajadoresData = [
            ['name' => 'Diego Bibliotecario', 'email' => 'diego.lib@iestp.local'],
            ['name' => 'Sofía Asistente', 'email' => 'sofia.asist@iestp.local'],
        ];

        foreach ($trabajadoresData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'institutional_email' => str_replace('@iestp.local', '@iestp.edu.pe', $data['email']),
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole('Trabajador');
        }

        // Crear más admins
        $adminsData = [
            ['name' => 'Sistema Admin', 'email' => 'system@iestp.local'],
        ];

        foreach ($adminsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'institutional_email' => str_replace('@iestp.local', '@iestp.edu.pe', $data['email']),
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole('Admin');
        }
    }
}
