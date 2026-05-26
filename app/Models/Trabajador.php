<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trabajador extends Model
{
    protected $table = 'trabajadors';

    protected $fillable = [
        'nombre',
        'ap_paterno',
        'ap_materno',
        'ci',
        'email',
        'celular',
        'fecha_nacimiento',
        'cargo_id',
        'area_id',
        'user_id',
        'activo',
        'turno',
        'pin',
        'foto_perfil',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'fecha_nacimiento' => 'date'
    ];

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class, 'area_id');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ingresos(): HasMany
    {
        return $this->hasMany(Ingreso::class, 'trabajador_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim("{$this->nombre} {$this->ap_paterno} {$this->ap_materno}");
    }

    // Accessor para calcular la edad del trabajador
    public function getEdadAttribute(): ?int
    {
        if ($this->fecha_nacimiento) {
            return $this->fecha_nacimiento->age;
        }
        return null;
    }
}