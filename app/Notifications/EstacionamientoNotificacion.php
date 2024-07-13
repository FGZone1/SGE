<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EstacionamientoNotificacion extends Notification implements ShouldQueue
{
    use Queueable;

    protected $mensaje;

    public function __construct($mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Notificación de Estacionamiento')
                    ->line($this->mensaje)
                    ->action('Consultar Estacionamiento', url('/'))
                    ->line('Gracias por usar nuestro servicio!');
    }
}
