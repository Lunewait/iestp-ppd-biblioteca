<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define permissions
        $permissions = [
            // Material permissions
            'view_materials',
            'create_material',
            'edit_material',
            'delete_material',
            'manage_inventory',

            // Loan permissions
            'view_loans',
            'create_loan',
            'return_loan',
            'manage_loans',
            'approve_loan',

            // Fine permissions
            'view_fines',
            'create_fine',
            'manage_fines',
            'forgive_fine',

            // Reservation permissions
            'view_reservations',
            'create_reservation',
            'manage_reservations',

            // Repository permissions
            'view_repository',
            'submit_document',
            'approve_document',
            'manage_repository',

            // User management
            'view_users',
            'create_user',
            'edit_user',
            'delete_user',
            'manage_roles',

            // Admin permissions
            'access_admin',
            'manage_settings',

            // Export/Download permissions
            'export_materials',
            'export_loans',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define and create roles
        $studentRole = Role::firstOrCreate(['name' => 'Estudiante']);
        $studentRole->syncPermissions([
            'view_materials',
            'view_loans',
            'create_reservation',
            'view_reservations',
            'view_repository',
            'view_fines',
        ]);

        $workerRole = Role::firstOrCreate(['name' => 'Trabajador']);
        $workerRole->syncPermissions([
            'view_materials',
            'create_loan',
            'return_loan',
            'view_loans',
            'manage_inventory',
            'view_fines',
            'create_fine',
            'manage_fines',
            'forgive_fine',
            'view_reservations',
            'manage_reservations',
            'view_repository',
            'approve_loan',
            'submit_document',
        ]);

        $jefaAreaRole = Role::firstOrCreate(['name' => 'Jefe_Area']);
        $jefaAreaRole->syncPermissions([
            'view_materials',
            'view_repository',
            'approve_document',
            'submit_document',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions($permissions);

        // Create demo users
        $admin = User::firstOrCreate(
            ['email' => 'admin@iestp.edu.pe'],
            [
                'name' => 'Administrador',
                'institutional_email' => 'admin@iestp.edu.pe',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole('Admin');

        $worker = User::firstOrCreate(
            ['email' => 'trabajador@iestp.edu.pe'],
            [
                'name' => 'Trabajador Biblioteca',
                'institutional_email' => 'trabajador@iestp.edu.pe',
                'password' => bcrypt('password'),
            ]
        );
        $worker->assignRole('Trabajador');

        $student = User::firstOrCreate(
            ['email' => 'estudiante@iestp.edu.pe'],
            [
                'name' => 'Juan Estudiante',
                'institutional_email' => 'estudiante@iestp.edu.pe',
                'password' => bcrypt('password'),
            ]
        );
        $student->assignRole('Estudiante');

        $jefeArea = User::firstOrCreate(
            ['email' => 'jefe@iestp.edu.pe'],
            [
                'name' => 'Jefe de Área',
                'institutional_email' => 'jefe@iestp.edu.pe',
                'password' => bcrypt('password'),
            ]
        );
        $jefeArea->assignRole('Jefe_Area');
    }
}
