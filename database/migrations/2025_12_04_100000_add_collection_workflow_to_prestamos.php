<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Agrega campos y estados para el flujo completo de préstamos:
     * 1. Solicitud (pending)
     * 2. Aprobado - 24h para recoger (approved)
     * 3. Recogido - inician 7 días (collected)
     * 4. Devuelto (returned)
     * 5. Expirado - no recogió en 24h (expired)
     */
    public function up(): void
    {
        Schema::table('prestamos', function (Blueprint $table) {
            // Agregar campos para el flujo de recogida
            $table->dateTime('fecha_limite_recogida')->nullable()->after('fecha_prestamo');
            $table->dateTime('fecha_recogida')->nullable()->after('fecha_limite_recogida');
        });

        // Actualizar los estados de approval_status
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE prestamos 
                MODIFY COLUMN approval_status 
                ENUM('pending', 'approved', 'collected', 'returned', 'rejected', 'cancelled', 'expired') 
                DEFAULT 'pending'
            ");
        } elseif ($driver === 'pgsql') {
            // PostgreSQL: necesitamos recrear el tipo
            DB::statement("ALTER TABLE prestamos ALTER COLUMN approval_status TYPE VARCHAR(20)");
            // Primero eliminar constraint si existe
            DB::statement("ALTER TABLE prestamos DROP CONSTRAINT IF EXISTS prestamos_approval_status_check");
            DB::statement("
                ALTER TABLE prestamos 
                ADD CONSTRAINT prestamos_approval_status_check 
                CHECK (approval_status IN ('pending', 'approved', 'collected', 'returned', 'rejected', 'cancelled', 'expired'))
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prestamos', function (Blueprint $table) {
            $table->dropColumn(['fecha_limite_recogida', 'fecha_recogida']);
        });

        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement("
                ALTER TABLE prestamos 
                MODIFY COLUMN approval_status 
                ENUM('pending', 'approved', 'rejected', 'cancelled') 
                DEFAULT 'pending'
            ");
        } elseif ($driver === 'pgsql') {
            DB::statement("ALTER TABLE prestamos DROP CONSTRAINT IF EXISTS prestamos_approval_status_check");
            DB::statement("
                ALTER TABLE prestamos 
                ADD CONSTRAINT prestamos_approval_status_check 
                CHECK (approval_status IN ('pending', 'approved', 'rejected', 'cancelled'))
            ");
        }
    }
};
