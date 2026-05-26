<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ingreso extends Model
{
    protected $table = 'ingresos';

    protected $fillable = ['trabajador_id','area_id','observacion','tipo','registrado_en'];

    public $timestamps = true;

    protected $dates = ['registrado_en','created_at','updated_at'];

    public function trabajador(): BelongsTo
    {
        return $this->belongsTo(Trabajador::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    // Alias for backward compatibility: 'subnivel' was renamed to 'observacion'
    public function getSubnivelAttribute()
    {
        return $this->observacion;
    }

    public function setSubnivelAttribute($value)
    {
        $this->observacion = $value;
    }
}
