<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NuevaSolicitudRecibida extends Mailable
{
    use Queueable, SerializesModels;

    public $datosCorreo;
    public $tipoDestinatario; // 'docente' o 'admin'

    public function __construct($datosCorreo, $tipoDestinatario = 'docente')
    {
        $this->datosCorreo = $datosCorreo;
        $this->tipoDestinatario = $tipoDestinatario;
    }

    public function envelope(): Envelope
    {
        $asunto = $this->tipoDestinatario === 'admin' 
            ? 'NUEVA SOLICITUD PENDIENTE: ' . $this->datosCorreo['servicio'] 
            : 'Confirmación de Solicitud: ' . $this->datosCorreo['titulo'];

        return new Envelope(
            subject: $asunto,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.nueva_solicitud',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}