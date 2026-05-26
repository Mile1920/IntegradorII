<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. CREAR LOS ROLES CON GUION MEDIO (exacto como los usas en el middleware)
        Role::firstOrCreate(['name' => 'administrador-principal']);
        Role::firstOrCreate(['name' => 'administrador-area']);
        Role::firstOrCreate(['name' => 'tecnico']);
        Role::firstOrCreate(['name' => 'trabajador']);

        // 2. ADMINISTRADOR PRINCIPAL (el que ve TODO)
        $admin = User::updateOrCreate(
            ['email' => 'admin@minaporco.com'],
            [
                'name' => 'Administrador Principal',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('administrador-principal');

        // 3. ADMINISTRADOR DE ÁREA (solo ve Áreas y Cargos)
        $adminArea = User::updateOrCreate(
            ['email' => 'adminarea@minaporco.com'],
            [
                'name' => 'Administrador Área',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $adminArea->assignRole('administrador-area');

        // 4. TÉCNICO Y TRABAJADOR (email verificado para poder acceder al dashboard)
        $tecnico = User::updateOrCreate(
            ['email' => 'tecnico@minaporco.com'],
            [
                'name' => 'Técnico Prueba',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $tecnico->assignRole('tecnico');

        $trabajador = User::updateOrCreate(
            ['email' => 'trabajador@minaporco.com'],
            [
                'name' => 'Trabajador Prueba',
                'password' => Hash::make('12345678'),
                'email_verified_at' => now(),
            ]
        );
        $trabajador->assignRole('trabajador');
    }
}