<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rol_sistema', function (Blueprint $table) {
            $table->increments('Id_Rol');
            $table->string('Nombre_Rol', 50)->unique();
            $table->text('Descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rol_sistema');
    }
};
