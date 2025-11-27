<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Material;
use App\Models\User;
use App\Models\Prestamo;
use App\Models\Multa;

echo "=== IESTP Library - Seeded Data Summary ===\n\n";
echo "Materials: " . Material::count() . "\n";
echo "Users: " . User::count() . "\n";
echo "Loans (Prestamos): " . Prestamo::count() . "\n";
echo "Fines (Multas): " . Multa::count() . "\n\n";

echo "=== Materials by Type ===\n";
echo "Physical (fisico): " . Material::where('type', 'fisico')->count() . "\n";
echo "Digital (digital): " . Material::where('type', 'digital')->count() . "\n";
echo "Hybrid (hibrido): " . Material::where('type', 'hibrido')->count() . "\n\n";

echo "=== Users by Role ===\n";
echo "Students (Estudiante): " . User::role('Estudiante')->count() . "\n";
echo "Workers (Trabajador): " . User::role('Trabajador')->count() . "\n";
echo "Area Chiefs (Jefe_Area): " . User::role('Jefe_Area')->count() . "\n";
echo "Admins (Admin): " . User::role('Admin')->count() . "\n\n";

echo "=== Loans by Status ===\n";
echo "Active (activo): " . Prestamo::where('status', 'activo')->count() . "\n";
echo "Returned (devuelto): " . Prestamo::where('status', 'devuelto')->count() . "\n";
echo "Overdue (vencido): " . Prestamo::where('status', 'vencido')->count() . "\n\n";

echo "=== Approval Status ===\n";
echo "Pending: " . Prestamo::where('approval_status', 'pending')->count() . "\n";
echo "Approved: " . Prestamo::where('approval_status', 'approved')->count() . "\n";
echo "Rejected: " . Prestamo::where('approval_status', 'rejected')->count() . "\n";
