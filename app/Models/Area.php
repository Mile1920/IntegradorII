<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'nombre',
        'nivel',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function trabajadores(): HasMany
    {
        return $this->hasMany(Trabajador::class, 'area_id');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class, 'area_id');
    }
}