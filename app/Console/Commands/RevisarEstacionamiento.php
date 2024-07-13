<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Estacionamiento;
use App\Models\Usuario;
use App\Notifications\EstacionamientoNotificacion;

class RevisarEstacionamiento extends Command
{
    protected $signature = 'estacionamiento:revisar';
    protected $description = 'Revisar los estacionamientos para enviar notificaciones y descontar tiempo';

    public function handle()
    {
        // Obtener todos los estacionamientos que están activos
        $estacionamientos = Estacionamiento::where('estado', 'estacionado')->get();

        foreach ($estacionamientos as $estacionamiento) {
            // Obtener el usuario asociado al estacionamiento
            $usuario = Usuario::find($estacionamiento->dni_usuario);

            // Descontar un minuto del tiempo de estacionamiento
            $estacionamiento->tiempo--;

            // Verificar si el tiempo se ha agotado
            if ($estacionamiento->tiempo <= 0) {
                // Cambiar el estado a libre
                $estacionamiento->estado = 'libre';
                $estacionamiento->save();

                // Notificar al usuario
                $usuario->notify(new EstacionamientoNotificacion('Su tiempo de estacionamiento ha expirado.'));
            } elseif ($estacionamiento->tiempo <= 15) {
                // Notificar si queda menos de 15 minutos
                $usuario->notify(new EstacionamientoNotificacion('Su tiempo de estacionamiento está por finalizar en 15 minutos.'));
            }

            // Guardar los cambios en el estacionamiento
            $estacionamiento->save();
        }
    }
}
