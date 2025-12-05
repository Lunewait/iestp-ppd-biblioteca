<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Esta migración agrega comentarios descriptivos a las tablas
     * para mejor documentación y evitar confusión.
     */
    public function up(): void
    {
        // Comentarios para tablas del sistema de biblioteca
        $this->addTableComment('materials', 'Catálogo principal de materiales (libros, documentos, recursos)');
        $this->addTableComment('material_fisicos', 'Información específica de materiales físicos (stock, ubicación, ISBN)');
        $this->addTableComment('material_digitals', 'Información específica de materiales digitales (URL, formato, tamaño archivo)');
        
        // Comentarios para tablas de gestión de préstamos
        $this->addTableComment('prestamos', 'Registro de préstamos de materiales a usuarios');
        $this->addTableComment('approval_logs', 'Historial de aprobaciones/rechazos de solicitudes de PRÉSTAMOS');
        $this->addTableComment('multas', 'Multas generadas por retraso en devolución de materiales');
        $this->addTableComment('reservas', 'Reservas de materiales por parte de usuarios');
        
        // Comentarios para tablas del repositorio institucional
        $this->addTableComment('repositorio_documentos', 'Tesis, investigaciones y trabajos académicos institucionales');
        $this->addTableComment('aprobaciones', 'Aprobaciones de los Jefes de Área para DOCUMENTOS DEL REPOSITORIO (no confundir con approval_logs que es para préstamos)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No es necesario revertir comentarios
        // pero si quisieras, podrías limpiarlos aquí
    }

    /**
     * Helper para agregar comentarios a tablas
     */
    private function addTableComment(string $table, string $comment): void
    {
        $driver = DB::connection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE `{$table}` COMMENT '{$comment}'");
        } elseif ($driver === 'pgsql') {
            DB::statement("COMMENT ON TABLE {$table} IS '{$comment}'");
        }
        // SQLite no soporta comentarios de tabla
    }
};
