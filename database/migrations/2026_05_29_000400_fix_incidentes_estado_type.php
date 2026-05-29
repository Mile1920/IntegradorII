<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE incidentes ALTER COLUMN estado TYPE VARCHAR(20)");
    }

    public function down(): void
    {
        // No revertimos porque los datos ya pueden tener nuevos valores
    }
};
