<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RepositorioDocumento extends Model
{
    use HasFactory;

    protected $table = 'repositorio_documentos';

    protected $fillable = [
        'user_id',
        'titulo',
        'descripcion',
        'autor',
        'tipo',
        'file_path',
        'file_original_name',
        'mime_type',
        'file_size',
        'estado',
        'comentarios_revision',
        'revisado_por',
        'fecha_revision',
        'fecha_aprobacion',
        'descargas',
    ];

    protected $casts = [
        'fecha_revision' => 'datetime',
        'fecha_aprobacion' => 'datetime',
    ];

    /**
     * Get the user who submitted the document
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the user who reviewed the document
     */
    public function revisadoPor()
    {
        return $this->belongsTo(User::class, 'revisado_por');
    }

    /**
     * Get approval records
     */
    public function aprobaciones()
    {
        return $this->hasMany(Aprobacion::class, 'documento_id');
    }
}
