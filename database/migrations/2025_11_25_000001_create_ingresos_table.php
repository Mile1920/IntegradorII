<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajador_id')->constrained('trabajadors')->onDelete('cascade');
            $table->foreignId('area_id')->nullable()->constrained('areas')->nullOnDelete();
            $table->string('subnivel')->nullable();
            $table->enum('tipo', ['ingreso', 'salida'])->default('ingreso');
            $table->timestamp('registrado_en')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos');
    }
};
