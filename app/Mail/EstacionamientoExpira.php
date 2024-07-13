<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EstacionamientoExpira extends Mailable
{
    use Queueable, SerializesModels;
    

    public $usuario;
    public $mensaje;
    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        $this->usuario = $usuario;
        $this->mensaje = $mensaje;
    }

    /**
     * Get the message envelope.
     */
    //public function envelope(): Envelope
   // {
    //    return new Envelope(
    //        subject: 'Estacionamiento Expira',
    //    );
   // }
   public function build()
   {
       return $this->subject('Notificación de Estacionamiento')
                   ->markdown('emails.estacionamiento_expira')
                   ->with([
                       'nombre' => $this->usuario->nombre,
                       'mensaje' => $this->mensaje,
                   ]);
   }
    /**
     * Get the message content definition.
     */
    //public function content(): Content
    //{
     //   return new Content(
      //      markdown: 'emails.estacionamiento_expira',
       // );
    //}

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    //public function attachments(): array
    //{
    //    return [];
    //}
}
