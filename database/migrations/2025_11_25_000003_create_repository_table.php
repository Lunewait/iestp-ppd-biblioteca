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
        // Repository documents table for thesis and digital repositories
        Schema::create('repositorio_documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('autor');
            $table->enum('tipo', ['tesis', 'investigacion', 'trabajo_final', 'otro']);
            $table->string('file_path')->nullable();
            $table->string('file_original_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->enum('estado', ['pendiente', 'publicado', 'rechazado'])->default('pendiente');
            $table->text('comentarios_revision')->nullable();
            $table->string('palabras_clave')->nullable();
            $table->string('licencia')->default('all_rights_reserved');
            $table->foreignId('revisado_por')->nullable()->constrained('users');
            $table->dateTime('fecha_revision')->nullable();
            $table->dateTime('fecha_aprobacion')->nullable();
            $table->integer('descargas')->default(0);
            $table->timestamps();
        });

        // Approval workflow
        Schema::create('aprobaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('repositorio_documentos')->cascadeOnDelete();
            $table->foreignId('jefe_area_id')->constrained('users')->cascadeOnDelete();
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->text('comentarios')->nullable();
            $table->dateTime('fecha_revision')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aprobaciones');
        Schema::dropIfExists('repositorio_documentos');
    }
};
