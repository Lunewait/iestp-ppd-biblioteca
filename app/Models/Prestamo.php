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
        'fecha_limite_recogida',
        'fecha_recogida',
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
        'fecha_limite_recogida' => 'datetime',
        'fecha_recogida' => 'datetime',
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

    /**
     * Check if loan is waiting for collection (approved but not collected)
     */
    public function isWaitingCollection()
    {
        return $this->approval_status === 'approved' 
            && $this->fecha_recogida === null;
    }

    /**
     * Check if collection period has expired
     */
    public function isCollectionExpired()
    {
        if (!$this->isWaitingCollection()) {
            return false;
        }

        return $this->fecha_limite_recogida && now()->greaterThan($this->fecha_limite_recogida);
    }

    /**
     * Check if loan is currently active (collected and not returned)
     */
    public function isActive()
    {
        return $this->approval_status === 'collected' 
            && $this->status === 'activo';
    }

    /**
     * Get count of active requests by user
     * Counts: pending, approved, and collected (not returned/rejected/expired)
     */
    public static function getActiveRequestsCount($userId)
    {
        return self::where('user_id', $userId)
                   ->whereIn('approval_status', ['pending', 'approved', 'collected'])
                   ->count();
    }

    /**
     * Mark loan as expired (student didn't collect in time)
     */
    public function markAsExpired()
    {
        $this->update([
            'approval_status' => 'expired',
            'status' => 'vencido'
        ]);

        // Return material to available stock
        if ($this->material->materialFisico) {
            $this->material->materialFisico->increment('available');
        }
    }

    /**
     * Mark loan as collected by student
     */
    public function markAsCollected()
    {
        $loanDays = config('library.default_loan_days', 7);
        
        $this->update([
            'approval_status' => 'collected',
            'status' => 'activo',
            'fecha_recogida' => now(),
            'fecha_devolucion_esperada' => now()->addDays($loanDays),
        ]);

        // Log the collection
        $this->approvalLogs()->create([
            'reviewer_id' => auth()->id(),
            'action' => 'collected',
            'notes' => 'Material recogido por el estudiante. Préstamo iniciado.',
        ]);
    }
}
