<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'author',
        'type',
        'code',
        'keywords',
    ];

    /**
     * Get the physical material details
     */
    public function materialFisico()
    {
        return $this->hasOne(MaterialFisico::class);
    }

    /**
     * Get the digital material details
     */
    public function materialDigital()
    {
        return $this->hasOne(MaterialDigital::class);
    }

    /**
     * Get loans for this material
     */
    public function prestamos()
    {
        return $this->hasMany(Prestamo::class, 'material_id');
    }

    /**
     * Get reservations for this material
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'material_id');
    }

    /**
     * Check if material is available for loan
     */
    public function isAvailable()
    {
        if ($this->type === 'digital' || $this->type === 'hibrido') {
            return true;
        }

        if ($this->materialFisico && $this->materialFisico->available > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get active loans count
     */
    public function getActiveLoansCount()
    {
        return $this->prestamos()->where('status', 'activo')->count();
    }

    /**
     * Get reservations count
     */
    public function getReservationsCount()
    {
        return $this->reservas()->where('estado', 'activa')->count();
    }

    /**
     * Get total loans count
     */
    public function getTotalLoansCount()
    {
        return $this->prestamos()->count();
    }

    /**
     * Search materials by keyword
     */
    public static function searchByKeyword($keyword)
    {
        return self::where('title', 'like', "%{$keyword}%")
                   ->orWhere('author', 'like', "%{$keyword}%")
                   ->orWhere('code', 'like', "%{$keyword}%")
                   ->orWhere('description', 'like', "%{$keyword}%");
    }

    /**
     * Filter materials by type
     */
    public static function filterByType($type)
    {
        if ($type === 'all') {
            return self::query();
        }

        return self::where('type', $type);
    }

    /**
     * Get materials with low stock
     */
    public static function getLowStock($threshold = 5)
    {
        return self::whereHas('materialFisico', function ($query) use ($threshold) {
            $query->where('available', '<', $threshold);
        })->with('materialFisico');
    }

    /**
     * Get most borrowed materials
     */
    public static function getMostBorrowed($limit = 10)
    {
        return self::withCount('prestamos')
                   ->orderBy('prestamos_count', 'desc')
                   ->limit($limit)
                   ->get();
    }
}
