<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ToolRequest extends Model
{
    use HasFactory;

    protected $fillable = ['trabajador_id','area_id','herramienta','cantidad','observacion','estado'];

    public function trabajador()
    {
        return $this->belongsTo(Trabajador::class, 'trabajador_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }
}
