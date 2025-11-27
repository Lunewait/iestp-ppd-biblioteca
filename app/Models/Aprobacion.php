<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aprobacion extends Model
{
    use HasFactory;

    protected $table = 'aprobaciones';

    protected $fillable = [
        'documento_id',
        'jefe_area_id',
        'estado',
        'comentarios',
        'fecha_revision',
    ];

    protected $casts = [
        'fecha_revision' => 'datetime',
    ];

    /**
     * Get the document being approved
     */
    public function documento()
    {
        return $this->belongsTo(RepositorioDocumento::class, 'documento_id');
    }

    /**
     * Get the area head approving
     */
    public function jefeArea()
    {
        return $this->belongsTo(User::class, 'jefe_area_id');
    }
}
