<?php
require 'bootstrap/app.php';

$app = new Illuminate\Foundation\Application(
    realpath(__DIR__)
);

$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== ESTADÍSTICAS DE BASE DE DATOS ===\n";
echo "Usuarios: " . \App\Models\User::count() . "\n";
echo "Materiales: " . \App\Models\Material::count() . "\n";
echo "Préstamos: " . \App\Models\Prestamo::count() . "\n";
echo "Multas: " . \App\Models\Multa::count() . "\n";
echo "Reservas: " . \App\Models\Reserva::count() . "\n";
echo "Documentos: " . \App\Models\Documento::count() . "\n";

$user = \App\Models\User::first();
if ($user) {
    echo "\n=== USUARIO PARA PRUEBA ===\n";
    echo "Email: " . $user->email . "\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Préstamos activos: " . $user->loans()->where('estado', 'activo')->count() . "\n";
    echo "Multas pendientes: " . $user->fines()->where('estado', 'pendiente')->count() . "\n";
}
?>
