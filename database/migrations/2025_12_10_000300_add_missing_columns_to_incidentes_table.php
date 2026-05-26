<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incidentes', function (Blueprint $table) {
            if (!Schema::hasColumn('incidentes', 'id_usuario')) {
                $table->unsignedInteger('id_usuario')->nullable()->after('area_id');
            }
            if (!Schema::hasColumn('incidentes', 'ap_paterno')) {
                $table->string('ap_paterno', 50)->nullable()->after('id_usuario');
            }
            if (!Schema::hasColumn('incidentes', 'fecha_reporte')) {
                $table->dateTime('fecha_reporte')->nullable()->after('estado');
            }
        });
    }

    public function down(): void
    {
        Schema::table('incidentes', function (Blueprint $table) {
            $columns = ['id_usuario', 'ap_paterno', 'fecha_reporte'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('incidentes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
