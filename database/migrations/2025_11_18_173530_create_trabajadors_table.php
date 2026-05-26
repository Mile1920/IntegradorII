<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trabajadors', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('ap_paterno')->nullable();
            $table->string('ap_materno')->nullable();
            $table->string('ci', 20)->unique();
            $table->string('email')->unique();
            $table->string('foto')->nullable(); // ← FOTO AÑADIDA
            $table->string('celular')->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->unsignedBigInteger('cargo_id')->nullable();
            $table->unsignedBigInteger('area_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable()->unique();
            $table->boolean('activo')->default(true); // ← ACTIVO AÑADIDO
            $table->timestamps();

            // Claves foráneas
            $table->foreign('cargo_id')->references('id')->on('cargos')->onDelete('set null');
            $table->foreign('area_id')->references('id')->on('areas')->onDelete('set null');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajadors');
    }
};