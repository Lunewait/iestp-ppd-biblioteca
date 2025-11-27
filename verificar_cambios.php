#!/usr/bin/env php
<?php

/**
 * Script de Verificación del Sistema de Biblioteca IESTP
 * 
 * Este script verifica que todas las correcciones se hayan aplicado correctamente
 */

echo "🔍 Verificando Sistema de Biblioteca IESTP...\n\n";

$errors = [];
$warnings = [];
$success = [];

// Verificar archivos modificados
echo "📁 Verificando archivos modificados...\n";

$files = [
    'resources/views/components/navbar.blade.php' => 'Navbar con navegación por roles',
    'database/seeders/RolePermissionSeeder.php' => 'Permisos actualizados',
    'resources/views/users/create.blade.php' => 'Formulario de creación corregido',
    'resources/views/users/edit.blade.php' => 'Formulario de edición corregido',
    'resources/views/users/index.blade.php' => 'Botón de importar agregado',
    'routes/web.php' => 'Rutas de importación agregadas',
];

foreach ($files as $file => $description) {
    if (file_exists($file)) {
        $success[] = "✅ $description: $file";
    } else {
        $errors[] = "❌ Archivo no encontrado: $file";
    }
}

// Verificar archivos nuevos
echo "\n📄 Verificando archivos nuevos...\n";

$newFiles = [
    'app/Http/Controllers/UserImportController.php' => 'Controlador de importación',
    'resources/views/users/import.blade.php' => 'Vista de importación',
    'SOLUCION_PROBLEMAS.md' => 'Documentación completa',
    'RESUMEN_CAMBIOS.md' => 'Resumen de cambios',
];

foreach ($newFiles as $file => $description) {
    if (file_exists($file)) {
        $success[] = "✅ $description: $file";
    } else {
        $errors[] = "❌ Archivo nuevo no encontrado: $file";
    }
}

// Verificar contenido específico
echo "\n🔎 Verificando contenido de archivos...\n";

// Verificar navbar
$navbarContent = file_get_contents('resources/views/components/navbar.blade.php');
if (strpos($navbarContent, 'Gestionar Préstamos') !== false) {
    $success[] = "✅ Navbar contiene 'Gestionar Préstamos'";
} else {
    $warnings[] = "⚠️ Navbar no contiene 'Gestionar Préstamos'";
}

if (strpos($navbarContent, "hasRole('Estudiante')") !== false) {
    $success[] = "✅ Navbar tiene separación por rol Estudiante";
} else {
    $warnings[] = "⚠️ Navbar no tiene separación por rol Estudiante";
}

// Verificar RolePermissionSeeder
$seederContent = file_get_contents('database/seeders/RolePermissionSeeder.php');
if (strpos($seederContent, "'view_fines'") !== false && 
    strpos($seederContent, "Role::firstOrCreate(['name' => 'Estudiante'])") !== false) {
    $success[] = "✅ Estudiantes tienen permiso view_fines";
} else {
    $errors[] = "❌ Estudiantes no tienen permiso view_fines en el seeder";
}

// Verificar rutas
$routesContent = file_get_contents('routes/web.php');
if (strpos($routesContent, 'users.import.form') !== false) {
    $success[] = "✅ Rutas de importación agregadas";
} else {
    $errors[] = "❌ Rutas de importación no encontradas";
}

// Resumen
echo "\n" . str_repeat("=", 60) . "\n";
echo "📊 RESUMEN DE VERIFICACIÓN\n";
echo str_repeat("=", 60) . "\n\n";

echo "✅ Éxitos: " . count($success) . "\n";
foreach ($success as $item) {
    echo "   $item\n";
}

if (count($warnings) > 0) {
    echo "\n⚠️ Advertencias: " . count($warnings) . "\n";
    foreach ($warnings as $item) {
        echo "   $item\n";
    }
}

if (count($errors) > 0) {
    echo "\n❌ Errores: " . count($errors) . "\n";
    foreach ($errors as $item) {
        echo "   $item\n";
    }
    echo "\n⚠️ Se encontraron errores. Por favor, revisa los archivos.\n";
    exit(1);
} else {
    echo "\n🎉 ¡Todas las verificaciones pasaron exitosamente!\n";
    echo "\n📝 Próximos pasos:\n";
    echo "   1. Ejecutar: php artisan db:seed --class=RolePermissionSeeder\n";
    echo "   2. Limpiar caché: php artisan cache:clear\n";
    echo "   3. Probar la importación de usuarios\n";
    echo "   4. Verificar permisos de cada rol\n";
    exit(0);
}
