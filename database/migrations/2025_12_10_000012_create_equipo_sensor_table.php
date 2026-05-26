<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo_sensor', function (Blueprint $table) {
            $table->unsignedInteger('Id_Equipo');
            $table->unsignedInteger('Id_Sensor');
            $table->date('Fecha_Asignacion');
            $table->date('Fecha_Fin')->nullable();
            $table->timestamps();

            $table->primary(['Id_Equipo', 'Id_Sensor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_sensor');
    }
};
