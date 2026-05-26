<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Trabajador;
use App\Models\User;

class BackfillTrabajadorUser extends Command
{
    protected $signature = 'backfill:trabajador-user';
    protected $description = 'Rellena el campo user_id en trabajadors buscando usuarios por email';

    public function handle()
    {
        $this->info('Buscando trabajadors sin user_id...');

        $trabajadores = Trabajador::whereNull('user_id')->get();
        $updated = 0;

        DB::beginTransaction();
        try {
            foreach ($trabajadores as $t) {
                if (!$t->email) continue;
                $user = User::where('email', $t->email)->first();
                if ($user) {
                    $t->user_id = $user->id;
                    $t->save();
                    $updated++;
                }
            }
            DB::commit();
            $this->info("Proceso finalizado. Trabajadores actualizados: $updated");
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error durante el proceso: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
