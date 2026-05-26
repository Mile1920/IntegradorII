<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tool_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('trabajador_id')->nullable()->after('user_id');
            $table->integer('cantidad')->default(1)->after('herramienta');

            $table->foreign('trabajador_id')->references('id')->on('trabajadors');
        });

        // Migrar datos existentes
        DB::statement('UPDATE tool_requests SET trabajador_id = (SELECT id FROM trabajadors WHERE user_id = tool_requests.user_id LIMIT 1)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tool_requests', function (Blueprint $table) {
            $table->dropForeign(['trabajador_id']);
            $table->dropColumn(['trabajador_id', 'cantidad']);
        });
    }
};
