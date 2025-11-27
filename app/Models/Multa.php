<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Multa extends Model
{
    use HasFactory;

    protected $table = 'multas';

    protected $fillable = [
        'prestamo_id',
        'user_id',
        'monto',
        'razon',
        'status',
        'fecha_vencimiento',
        'fecha_pago',
        'registrado_por',
    ];

    protected $casts = [
        'fecha_vencimiento' => 'datetime',
        'fecha_pago' => 'datetime',
        'monto' => 'decimal:2',
    ];

    /**
     * Get the user fined
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the loan this fine is associated with
     */
    public function prestamo()
    {
        return $this->belongsTo(Prestamo::class, 'prestamo_id');
    }

    /**
     * Get the worker who registered the fine
     */
    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Check if fine is paid
     */
    public function isPaid()
    {
        return $this->status === 'pagada';
    }

    /**
     * Check if fine is pending
     */
    public function isPending()
    {
        return $this->status === 'pendiente';
    }

    /**
     * Check if fine is forgiven
     */
    public function isForgiven()
    {
        return $this->status === 'condonada';
    }

    /**
     * Check if fine is overdue
     */
    public function isOverdue()
    {
        if (!$this->fecha_vencimiento || $this->isPaid() || $this->isForgiven()) {
            return false;
        }

        return now()->greaterThan($this->fecha_vencimiento);
    }

    /**
     * Get pending fines by user
     */
    public static function getPendingByUser($userId)
    {
        return self::where('user_id', $userId)
                   ->where('status', 'pendiente')
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get total pending amount by user
     */
    public static function getTotalPendingByUser($userId)
    {
        return self::where('user_id', $userId)
                   ->where('status', 'pendiente')
                   ->sum('monto');
    }

    /**
     * Get all pending fines
     */
    public static function getAllPending()
    {
        return self::where('status', 'pendiente')
                   ->with(['usuario', 'prestamo'])
                   ->orderBy('created_at', 'desc')
                   ->get();
    }

    /**
     * Get total pending amount
     */
    public static function getTotalPending()
    {
        return self::where('status', 'pendiente')->sum('monto');
    }

    /**
     * Get overdue fines
     */
    public static function getOverdue()
    {
        return self::where('status', 'pendiente')
                   ->whereNotNull('fecha_vencimiento')
                   ->where('fecha_vencimiento', '<', now())
                   ->with(['usuario', 'prestamo'])
                   ->orderBy('fecha_vencimiento')
                   ->get();
    }
}
