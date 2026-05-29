<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('auditoria', function (Blueprint $table) {
            $table->dropForeign(['Id_Usuario']);
            $table->bigInteger('Id_Usuario')->nullable()->change();
            $table->foreign('Id_Usuario')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('auditoria', function (Blueprint $table) {
            $table->dropForeign(['Id_Usuario']);
            $table->integer('Id_Usuario')->unsigned()->nullable()->change();
            $table->foreign('Id_Usuario')->references('Id_Usuario')->on('usuario')->onDelete('set null');
        });
    }
};
