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
        // PostgreSQL doesn't support modifying ENUMs directly in the same way as MySQL
        // Instead we drop the check constraint and add a new one
        DB::statement("ALTER TABLE prestamos DROP CONSTRAINT IF EXISTS prestamos_status_check");
        DB::statement("ALTER TABLE prestamos ADD CONSTRAINT prestamos_status_check CHECK (status IN ('activo', 'devuelto', 'vencido', 'cancelado', 'pending'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE prestamos DROP CONSTRAINT IF EXISTS prestamos_status_check");
        DB::statement("ALTER TABLE prestamos ADD CONSTRAINT prestamos_status_check CHECK (status IN ('activo', 'devuelto', 'vencido'))");
    }
};
