<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            if (!Schema::hasColumn('areas', 'estado')) {
                $table->enum('estado', ['activa', 'inactiva', 'mantenimiento'])->default('activa')->after('esp32_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('areas', function (Blueprint $table) {
            if (Schema::hasColumn('areas', 'estado')) {
                $table->dropColumn('estado');
            }
        });
    }
};
