<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitud_equipo', function (Blueprint $table) {
            $table->increments('Id_Solicitud');
            $table->text('Descripcion');
            $table->enum('Estado', ['pendiente', 'aprobada', 'rechazada', 'entregada'])->default('pendiente');
            $table->timestamp('Fecha_Solicitud')->useCurrent();
            $table->unsignedInteger('Id_Trabajador');
            $table->unsignedInteger('Id_Area');
            $table->unsignedInteger('Aprobado_Por')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitud_equipo');
    }
};
