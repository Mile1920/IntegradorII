<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alarma', function (Blueprint $table) {
            $table->increments('Id_Alarma');
            $table->enum('Tipo_Origen', ['evento', 'salud', 'sensor', 'manual'])->default('sensor');
            $table->enum('Prioridad', ['baja', 'media', 'alta', 'critica'])->default('media');
            $table->string('Metodo_Disparo', 100)->nullable();
            $table->enum('Estado', ['activa', 'atendida', 'descartada'])->default('activa');
            $table->timestamp('Fecha')->useCurrent();
            $table->unsignedInteger('Id_Evento')->nullable();
            $table->unsignedInteger('Id_Salud')->nullable();
            $table->unsignedInteger('Id_Sensor')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alarma');
    }
};
