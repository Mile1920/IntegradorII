<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class AssignAdminRole extends Command
{
    protected $signature = 'admin:make-super';
    protected $description = 'Asigna el rol administrador-principal al usuario admin';

    public function handle()
    {
        $user = User::where('email', 'admin@minaporco.com')->first();

        if ($user) {
            $user->assignRole('administrador-principal');
            $this->info('¡Rol administrador-principal asignado correctamente a ' . $user->name . '!');
        } else {
            $this->error('No se encontró el usuario admin@minaporco.com');
        }
    }
}