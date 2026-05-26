<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seguridad_cuenta', function (Blueprint $table) {
            $table->increments('Id_Seguridad');
            $table->unsignedInteger('Id_Usuario')->unique();
            $table->integer('Intentos_Fallidos')->default(0);
            $table->dateTime('Bloqueado_Hasta')->nullable();
            $table->string('Ultima_IP', 45)->nullable();
            $table->timestamp('Ultima_Sesion')->nullable();
            $table->timestamps();

            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('usuario')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguridad_cuenta');
    }
};
