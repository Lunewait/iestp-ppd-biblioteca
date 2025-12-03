<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modify the status column to include 'cancelado'
        if (DB::connection()->getDriverName() === 'pgsql') {
            // PostgreSQL syntax
            DB::statement("ALTER TABLE prestamos DROP CONSTRAINT IF EXISTS prestamos_status_check");
            DB::statement("ALTER TABLE prestamos ADD CONSTRAINT prestamos_status_check CHECK (status IN ('activo', 'devuelto', 'vencido', 'cancelado', 'pending'))");
        } else {
            // MySQL syntax
            DB::statement("ALTER TABLE prestamos MODIFY status ENUM('activo', 'devuelto', 'vencido', 'cancelado', 'pending') DEFAULT 'activo'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement("ALTER TABLE prestamos DROP CONSTRAINT IF EXISTS prestamos_status_check");
            DB::statement("ALTER TABLE prestamos ADD CONSTRAINT prestamos_status_check CHECK (status IN ('activo', 'devuelto', 'vencido'))");
        } else {
            DB::statement("ALTER TABLE prestamos MODIFY status ENUM('activo', 'devuelto', 'vencido') DEFAULT 'activo'");
        }
    }
};
