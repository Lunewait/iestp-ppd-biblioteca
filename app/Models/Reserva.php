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

    /**
     * Check if the reservation has expired (48 hours after approval)
     */
    public function isExpired()
    {
        if ($this->status === 'aprobada' && $this->fecha_expiracion) {
            return now()->greaterThan($this->fecha_expiracion);
        }
        return false;
    }

    /**
     * Mark reservation as expired and return material to stock
     */
    public function markAsExpired()
    {
        $this->update(['status' => 'expirada']);

        // Return material to stock
        if ($this->material && $this->material->materialFisico) {
            $this->material->materialFisico->increment('available');
        }
    }
}
