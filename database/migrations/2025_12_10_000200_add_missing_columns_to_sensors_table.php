<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sensors', function (Blueprint $table) {
            if (!Schema::hasColumn('sensors', 'fecha_instalacion')) {
                $table->date('fecha_instalacion')->nullable()->after('ubicacion');
            }
            if (!Schema::hasColumn('sensors', 'id_tipo_sensor')) {
                $table->unsignedInteger('id_tipo_sensor')->nullable()->after('fecha_instalacion');
            }
            if (!Schema::hasColumn('sensors', 'id_area')) {
                $table->unsignedInteger('id_area')->nullable()->after('id_tipo_sensor');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sensors', function (Blueprint $table) {
            $columns = ['fecha_instalacion', 'id_tipo_sensor', 'id_area'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('sensors', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
