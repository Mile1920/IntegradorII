<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipo_sensor', function (Blueprint $table) {
            $table->increments('Id_Tipo_Sensor');
            $table->string('Nombre_Tipo', 50)->unique();
            $table->string('Unidad', 20);
            $table->text('Descripcion')->nullable();
            $table->float('Umbral_Minimo')->nullable();
            $table->float('Umbral_Maximo')->nullable();
            $table->timestamp('Fecha_Creacion')->useCurrent();
            $table->timestamp('Fecha_Actualizacion')->nullable()->useCurrentOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipo_sensor');
    }
};
