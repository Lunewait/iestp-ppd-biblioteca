<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $table = 'reservas';

    protected $fillable = [
        'user_id',
        'material_id',
        'fecha_reserva',
        'fecha_expiracion',
        'status',
        'posicion_cola',
    ];

    protected $casts = [
        'fecha_reserva' => 'datetime',
        'fecha_expiracion' => 'datetime',
    ];

    /**
     * Get the user who made the reservation
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the material reserved
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}
