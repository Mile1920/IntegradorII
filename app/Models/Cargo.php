<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $table = 'cargos';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo',
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
        return $this->hasMany(Trabajador::class, 'cargo_id');
    }
}