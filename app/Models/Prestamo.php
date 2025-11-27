<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamos';

    protected $fillable = [
        'user_id',
        'material_id',
        'fecha_prestamo',
        'fecha_devolucion_esperada',
        'fecha_devolucion_actual',
        'status',
        'registrado_por',
        'notas',
        'approval_status',
        'approved_by',
        'approval_reason',
        'approval_date',
    ];

    protected $casts = [
        'fecha_prestamo' => 'datetime',
        'fecha_devolucion_esperada' => 'datetime',
        'fecha_devolucion_actual' => 'datetime',
        'approval_date' => 'datetime',
    ];

    /**
     * Get the user who borrowed
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Alias for usuario relation
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the material borrowed
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    /**
     * Get the worker who registered the loan
     */
    public function registradoPor()
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    /**
     * Get the user who approved the loan
     */
    public function approvedByUser()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get approval logs
     */
    public function approvalLogs()
    {
        return $this->hasMany(ApprovalLog::class, 'prestamo_id');
    }

    /**
     * Get associated fines
     */
    public function multas()
    {
        return $this->hasMany(Multa::class, 'prestamo_id');
    }

    /**
     * Check if loan is overdue
     */
    public function isOverdue()
    {
        if ($this->status !== 'activo') {
            return false;
        }

        return now()->greaterThan($this->fecha_devolucion_esperada);
    }

    /**
     * Alias properties for compatibility
     */
    public function getDateBorrowedAttribute()
    {
        return $this->fecha_prestamo;
    }

    public function getDueDateAttribute()
    {
        return $this->fecha_devolucion_esperada;
    }

    public function getIsReturnedAttribute()
    {
        return $this->status === 'devuelto';
    }

    /**
     * Get days until due date (negative if overdue)
     */
    public function getDaysUntilDue()
    {
        return now()->diffInDays($this->fecha_devolucion_esperada, false);
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdue()
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return now()->diffInDays($this->fecha_devolucion_esperada);
    }

    /**
     * Calculate fine amount if overdue
     */
    public function calculateFineAmount($dailyRate = 1.50)
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return $this->getDaysOverdue() * $dailyRate;
    }

    /**
     * Get active loans by user
     */
    public static function getActiveLoansByUser($userId)
    {
        return self::where('user_id', $userId)
                   ->where('status', 'activo')
                   ->with('material')
                   ->orderBy('fecha_devolucion_esperada')
                   ->get();
    }

    /**
     * Get overdue loans
     */
    public static function getOverdueLoans()
    {
        return self::where('status', 'activo')
                   ->where('fecha_devolucion_esperada', '<', now())
                   ->with(['usuario', 'material'])
                   ->orderBy('fecha_devolucion_esperada')
                   ->get();
    }

    /**
     * Get loans expiring soon
     */
    public static function getExpiringsoon($days = 3)
    {
        return self::where('status', 'activo')
                   ->whereBetween('fecha_devolucion_esperada', [now(), now()->addDays($days)])
                   ->with(['usuario', 'material'])
                   ->orderBy('fecha_devolucion_esperada')
                   ->get();
    }
}
