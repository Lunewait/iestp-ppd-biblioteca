<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialDigital extends Model
{
    use HasFactory;

    protected $table = 'material_digitals';

    protected $fillable = [
        'material_id',
        'url',
        'downloadable',
        'file_type',
        'file_path',
        'access_count',
        'license',
    ];

    /**
     * Get the material this record belongs to
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }
}
