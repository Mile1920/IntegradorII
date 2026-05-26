<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('usuario', function (Blueprint $table) {
            $table->increments('Id_Usuario');
            $table->unsignedInteger('Id_Rol');
            $table->string('Nombre_Usuario', 50)->unique();
            $table->string('Email', 100)->unique();
            $table->string('Contrasena_Hash', 255);
            $table->boolean('Primer_Login')->default(true);
            $table->string('Inicio_Ses_URL', 255)->nullable();
            $table->timestamp('Fecha_Creacion')->useCurrent();
            $table->enum('Estado', ['activo', 'inactivo', 'suspendido'])->default('activo');
            $table->timestamps();

            $table->foreign('Id_Rol')->references('Id_Rol')->on('rol_sistema')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario');
    }
};
