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
        Schema::table('reservas', function (Blueprint $table) {
            // Make fecha_expiracion nullable
            $table->dateTime('fecha_expiracion')->nullable()->change();

            // Drop the existing enum column and recreate it with new values
            // We use a raw statement because changing enum values via Schema builder can be tricky
        });

        // Modify status column to include new values
        DB::statement("ALTER TABLE reservas MODIFY COLUMN status ENUM('pendiente', 'aprobada', 'completada', 'cancelada', 'expirada', 'activa', 'recogida') DEFAULT 'pendiente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dateTime('fecha_expiracion')->nullable(false)->change();
        });

        DB::statement("ALTER TABLE reservas MODIFY COLUMN status ENUM('activa', 'cancelada', 'recogida') DEFAULT 'activa'");
    }
};
