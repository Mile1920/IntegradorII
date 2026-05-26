<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incidente extends Model
{
    protected $table = 'incidentes';

    protected $fillable = ['trabajador_id','area_id','descripcion','gravedad','estado','cerrado_por','cerrado_en'];

    protected $casts = [
        'cerrado_en' => 'datetime',
    ];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function cerrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cerrado_por');
    }
}
