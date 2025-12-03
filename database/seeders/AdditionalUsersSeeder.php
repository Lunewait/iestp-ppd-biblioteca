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
            ['name' => 'Carlos López', 'email' => 'carlos@iestp.edu.pe'],
            ['name' => 'María García', 'email' => 'maria@iestp.edu.pe'],
            ['name' => 'Juan Martínez', 'email' => 'juan@iestp.edu.pe'],
            ['name' => 'Ana Rodríguez', 'email' => 'ana@iestp.edu.pe'],
            ['name' => 'Luis Fernández', 'email' => 'luis@iestp.edu.pe'],
            ['name' => 'Rosa Pérez', 'email' => 'rosa@iestp.edu.pe'],
            ['name' => 'Pedro Sánchez', 'email' => 'pedro@iestp.edu.pe'],
            ['name' => 'Elena Morales', 'email' => 'elena@iestp.edu.pe'],
        ];

        foreach ($estudiantesData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'institutional_email' => $data['email'],
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole('Estudiante');
        }

        // Crear más trabajadores
        $trabajadoresData = [
            ['name' => 'Diego Bibliotecario', 'email' => 'diego.lib@iestp.edu.pe'],
            ['name' => 'Sofía Asistente', 'email' => 'sofia.asist@iestp.edu.pe'],
        ];

        foreach ($trabajadoresData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'institutional_email' => $data['email'],
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole('Trabajador');
        }

        // Crear más admins
        $adminsData = [
            ['name' => 'Sistema Admin', 'email' => 'system@iestp.edu.pe'],
        ];

        foreach ($adminsData as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'institutional_email' => $data['email'],
                    'password' => bcrypt('password'),
                ]
            );
            $user->assignRole('Admin');
        }
    }
}
