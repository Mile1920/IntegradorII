<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('token_autenticacion', function (Blueprint $table) {
            $table->increments('Id_Token');
            $table->string('Token_Hash', 255);
            $table->enum('Tipo', ['jwt', 'sesion', 'api', 'recuperacion'])->default('jwt');
            $table->timestamp('Creado_En')->useCurrent();
            $table->dateTime('Fecha_Inicio_Sesion')->nullable();
            $table->dateTime('Expiracion');
            $table->enum('Estado', ['activo', 'expirado', 'revocado'])->default('activo');
            $table->unsignedInteger('Id_Usuario');
            $table->timestamps();

            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('usuario')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('token_autenticacion');
    }
};
