<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialFisico extends Model
{
    use HasFactory;

    protected $table = 'material_fisicos';

    protected $fillable = [
        'material_id',
        'isbn',
        'stock',
        'available',
        'publisher',
        'publication_year',
        'location',
    ];

    /**
     * Get the material this record belongs to
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
