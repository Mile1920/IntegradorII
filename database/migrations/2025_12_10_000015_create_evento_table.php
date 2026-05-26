<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evento', function (Blueprint $table) {
            $table->increments('Id_Evento');
            $table->string('Tipo_Evento', 50);
            $table->enum('Nivel_Critico', ['info', 'advertencia', 'critico', 'emergencia'])->default('info');
            $table->dateTime('Fecha_Hora')->useCurrent();
            $table->text('Descripcion_Ad')->nullable();
            $table->unsignedInteger('Id_Area')->nullable();
            $table->unsignedInteger('Id_Salud')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evento');
    }
};
