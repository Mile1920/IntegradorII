<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trabajador_area', function (Blueprint $table) {
            $table->unsignedInteger('Id_Trabajador');
            $table->unsignedInteger('Id_Area');
            $table->date('Fecha_Asignacion');
            $table->date('Fecha_Fin')->nullable();
            $table->primary(['Id_Trabajador', 'Id_Area', 'Fecha_Asignacion']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trabajador_area');
    }
};
