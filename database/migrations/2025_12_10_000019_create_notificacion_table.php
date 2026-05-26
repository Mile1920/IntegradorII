<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion', function (Blueprint $table) {
            $table->increments('Id_Notificacion');
            $table->unsignedInteger('Id_Usuario');
            $table->unsignedInteger('Id_Alarma')->nullable();
            $table->text('Mensaje');
            $table->boolean('Leida')->default(false);
            $table->timestamp('Fecha_Envio')->useCurrent();
            $table->dateTime('Fecha_Lectura')->nullable();
            $table->timestamps();

            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('usuario')->onDelete('cascade');
            $table->foreign('Id_Alarma')->references('Id_Alarma')->on('alarma')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};
