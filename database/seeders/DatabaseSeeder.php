<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

// Importa tu seeder personalizado
use Database\Seeders\AdminUserSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Aquí se ejecuta TODO lo que necesitas al hacer migrate:fresh --seed
        $this->call([
            AdminUserSeeder::class,
            // Si en el futuro creas más seeders (cargos, áreas, turnos, etc.)
            // los agregas aquí abajo, uno por línea:
            // CargoSeeder::class,
            // AreaSeeder::class,
        ]);
    }
}