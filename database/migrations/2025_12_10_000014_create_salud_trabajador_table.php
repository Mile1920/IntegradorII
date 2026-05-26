<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salud_trabajador', function (Blueprint $table) {
            $table->increments('Id_Salud');
            $table->string('Nivel_Critico', 50)->nullable();
            $table->integer('Pulso')->nullable();
            $table->dateTime('Fecha_Fisico')->nullable();
            $table->enum('Estado', ['normal', 'alerta', 'critico'])->default('normal');
            $table->unsignedInteger('Id_Trabajador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salud_trabajador');
    }
};
