<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Loans table
        Schema::create('prestamos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->dateTime('fecha_prestamo');
            $table->dateTime('fecha_devolucion_esperada');
            $table->dateTime('fecha_devolucion_actual')->nullable();
            $table->enum('status', ['activo', 'devuelto', 'vencido'])->default('activo');
            $table->foreignId('registrado_por')->constrained('users')->cascadeOnDelete();
            $table->text('notas')->nullable();
            $table->timestamps();
        });

        // Fines table
        Schema::create('multas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prestamo_id')->nullable()->constrained('prestamos')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->decimal('monto', 8, 2);
            $table->text('razon');
            $table->enum('status', ['pendiente', 'pagada', 'condonada'])->default('pendiente');
            $table->dateTime('fecha_vencimiento')->nullable();
            $table->dateTime('fecha_pago')->nullable();
            $table->foreignId('registrado_por')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Reservations table
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnDelete();
            $table->dateTime('fecha_reserva');
            $table->dateTime('fecha_expiracion');
            $table->enum('status', ['activa', 'cancelada', 'recogida'])->default('activa');
            $table->integer('posicion_cola')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('multas');
        Schema::dropIfExists('prestamos');
    }
};
