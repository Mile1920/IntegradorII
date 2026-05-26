<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajadors', function (Blueprint $table) {
            if (!Schema::hasColumn('trabajadors', 'pin')) {
                $table->string('pin', 20)->nullable()->unique()->after('celular');
            }
            if (!Schema::hasColumn('trabajadors', 'foto_perfil')) {
                $table->string('foto_perfil', 255)->nullable()->after('pin');
            }
            if (!Schema::hasColumn('trabajadors', 'n_ficha')) {
                $table->integer('n_ficha')->nullable()->unique()->after('fecha_nacimiento');
            }
            if (!Schema::hasColumn('trabajadors', 'fecha_ingreso')) {
                $table->date('fecha_ingreso')->nullable()->after('n_ficha');
            }
        });
    }

    public function down(): void
    {
        Schema::table('trabajadors', function (Blueprint $table) {
            $columns = ['pin', 'foto_perfil', 'n_ficha', 'fecha_ingreso'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('trabajadors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
