<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Auditoria extends Model
{
    protected $table = 'auditoria';
    protected $primaryKey = 'Id_Auditoria';

    protected $fillable = [
        'Id_Usuario', 'Accion', 'Tabla_Afectada', 'Id_Registro',
        'Detalle', 'IP_Origen', 'Fecha',
    ];

    public $timestamps = true;

    protected $casts = [
        'Fecha' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo('App\Models\User', 'Id_Usuario', 'id');
    }
}