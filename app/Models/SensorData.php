<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SensorData extends Model
{
    protected $table = 'sensor_data';

    protected $fillable = ['device_id','tipo','payload','recibido_en'];

    protected $casts = [
        'payload' => 'array',
        'recibido_en' => 'datetime',
    ];

    public function sensor()
    {
        return $this->belongsTo(Sensor::class, 'device_id', 'device_id');
    }
}
