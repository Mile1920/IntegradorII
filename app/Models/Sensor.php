<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sensor extends Model
{
    use HasFactory;

    protected $fillable = ['device_id','nombre','area_id','separacion_m','activo'];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
