<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE incidentes DROP CONSTRAINT IF EXISTS incidentes_estado_check");
        DB::statement("ALTER TABLE incidentes DROP CONSTRAINT IF EXISTS incidentes_estado_check1");
        DB::statement("ALTER TABLE incidentes ALTER COLUMN estado SET DEFAULT 'pendiente'");
    }

    public function down(): void
    {
        // No revertir - los datos pueden tener valores no-ENUM
    }
};
