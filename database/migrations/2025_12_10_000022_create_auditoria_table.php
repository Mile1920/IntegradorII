<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auditoria', function (Blueprint $table) {
            $table->increments('Id_Auditoria');
            $table->unsignedInteger('Id_Usuario')->nullable();
            $table->string('Accion', 100);
            $table->string('Tabla_Afectada', 50);
            $table->unsignedInteger('Id_Registro')->nullable();
            $table->text('Detalle')->nullable();
            $table->string('IP_Origen', 45)->nullable();
            $table->timestamp('Fecha')->useCurrent();
            $table->timestamps();

            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('usuario')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auditoria');
    }
};
