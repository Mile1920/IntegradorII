<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lectura_sensor', function (Blueprint $table) {
            $table->increments('Id_Lectura');
            $table->decimal('Valor', 10, 3);
            $table->string('Unidad', 20)->nullable();
            $table->timestamp('Fecha_Hora')->useCurrent();
            $table->unsignedInteger('Id_Sensor');
            $table->timestamps();

            $table->index(['Id_Sensor', 'Fecha_Hora'], 'idx_lectura_sensor_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lectura_sensor');
    }
};
