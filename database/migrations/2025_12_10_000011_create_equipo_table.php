<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo', function (Blueprint $table) {
            $table->increments('Id_Equipo');
            $table->string('Nombre', 100);
            $table->string('Tipo', 50)->nullable();
            $table->string('Ubicacion', 50)->nullable();
            $table->enum('Estado', ['operativo', 'falla', 'mantenimiento', 'baja'])->default('operativo');
            $table->timestamp('Fecha_Creacion')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo');
    }
};
