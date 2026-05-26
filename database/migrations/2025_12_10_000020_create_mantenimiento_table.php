<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento', function (Blueprint $table) {
            $table->increments('Id_Mantenimiento');
            $table->enum('Tipo_Mantenimiento', ['preventivo', 'correctivo', 'predictivo']);
            $table->dateTime('Fecha');
            $table->text('Resultado')->nullable();
            $table->text('Descripcion')->nullable();
            $table->timestamp('Fecha_Creacion')->useCurrent();
            $table->unsignedInteger('Id_Equipo');
            $table->unsignedInteger('Id_Trabajador')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento');
    }
};
