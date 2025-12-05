<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'institutional_email',
        'password',
        'blocked_for_loans',
        'blocked_reason',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked_for_loans' => 'boolean',
        ];
    }

    /**
     * Get loans for this user
     */
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'user_id');
    }

    /**
     * Get reservations for this user
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'user_id');
    }

    /**
     * Get fines for this user
     */
    public function multas()
    {
        return $this->hasMany(Multa::class, 'user_id');
    }

    /**
     * Get documents submitted by this user
     */
    public function documentos()
    {
        return $this->hasMany(RepositorioDocumento::class, 'user_id');
    }

    /**
     * Get active loans for this user
     */
    public function getActiveLoans()
    {
        return $this->prestamos()->where('status', 'activo')->with('material')->get();
    }

    /**
     * Get overdue loans for this user
     */
    public function getOverdueLoans()
    {
        return $this->prestamos()
            ->where('status', 'activo')
            ->where('fecha_devolucion_esperada', '<', now())
            ->with('material')
            ->get();
    }

    /**
     * Get pending fines for this user
     */
    public function getPendingFines()
    {
        return $this->multas()->where('status', 'pendiente')->get();
    }

    /**
     * Get total pending fines amount
     */
    public function getTotalPendingFines()
    {
        return $this->multas()->where('status', 'pendiente')->sum('monto');
    }

    /**
     * Check if user has unpaid fines
     */
    public function hasUnpaidFines()
    {
        return $this->getTotalPendingFines() > 0;
    }

    /**
     * Get active reservations
     */
    public function getActiveReservations()
    {
        return $this->reservas()->where('estado', 'activa')->with('material')->get();
    }

    /**
     * Get loan statistics
     */
    public function getLoanStatistics()
    {
        return [
            'total' => $this->prestamos()->count(),
            'active' => $this->prestamos()->where('status', 'activo')->count(),
            'returned' => $this->prestamos()->where('status', 'devuelto')->count(),
            'overdue' => $this->getOverdueLoans()->count(),
        ];
    }

    /**
     * Get user summary
     */
    public function getSummary()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'institutional_email' => $this->institutional_email,
            'roles' => $this->roles->pluck('name')->toArray(),
            'active_loans' => $this->getActiveLoans()->count(),
            'pending_fines' => $this->getTotalPendingFines(),
            'active_reservations' => $this->getActiveReservations()->count(),
            'has_unpaid_fines' => $this->hasUnpaidFines(),
        ];
    }

    /**
     * Check if user can request loans
     */
    public function canRequestLoans()
    {
        return !$this->blocked_for_loans;
    }

    /**
     * Block user from requesting loans
     */
    public function blockLoans($reason = 'Multas pendientes')
    {
        $this->update([
            'blocked_for_loans' => true,
            'blocked_reason' => $reason,
        ]);
    }

    /**
     * Unblock user to allow loan requests
     */
    public function unblockLoans()
    {
        $this->update([
            'blocked_for_loans' => false,
            'blocked_reason' => null,
        ]);
    }
}
