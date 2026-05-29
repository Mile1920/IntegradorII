<?php

namespace App\Console\Commands;

use App\Models\Ingreso;
use App\Models\Trabajador;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class AlertarSalidaPendiente extends Command
{
    protected $signature = 'alertar:salida-pendiente';
    protected $description = 'Envía alerta si un trabajador no registró salida después de 8 horas';

    public function handle()
    {
        $limite = now()->subHours(8);

        $ingresosSinSalida = Ingreso::where('tipo', 'ingreso')
            ->where('registrado_en', '<=', $limite)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('ingresos', 'salidas')
                    ->whereColumn('salidas.trabajador_id', 'ingresos.trabajador_id')
                    ->where('salidas.tipo', 'salida')
                    ->whereRaw('salidas.registrado_en > ingresos.registrado_en');
            })
            ->with('trabajador')
            ->get();

        if ($ingresosSinSalida->isEmpty()) {
            $this->info('No hay ingresos sin salida pendientes.');
            return Command::SUCCESS;
        }

        $contador = 0;
        foreach ($ingresosSinSalida as $ingreso) {
            $trabajador = $ingreso->trabajador;
            if (!$trabajador || !$trabajador->email) continue;

            try {
                Mail::raw(
                    "ALERTA: Salida no registrada\n\n" .
                    "Trabajador: {$trabajador->nombre_completo}\n" .
                    "PIN: {$trabajador->pin}\n" .
                    "Ingreso: {$ingreso->registrado_en->format('d/m/Y H:i')}\n" .
                    "Han transcurrido más de 8 horas sin registrar salida.\n\n" .
                    "Por favor, registre su salida lo antes posible.",
                    function ($message) use ($trabajador) {
                        $message->to($trabajador->email)
                            ->subject('Alerta: Salida no registrada - Mina Porco');
                    }
                );
                $contador++;

                Log::warning("[Alerta 8h] Salida pendiente para {$trabajador->nombre_completo} ({$trabajador->email})");
            } catch (\Exception $e) {
                Log::error("[Alerta 8h] Error al enviar correo a {$trabajador->email}: " . $e->getMessage());
            }
        }

        $this->info("Alertas enviadas: {$contador}");
        return Command::SUCCESS;
    }
}
