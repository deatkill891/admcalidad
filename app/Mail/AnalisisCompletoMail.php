<?php

namespace App\Mail;

use App\Models\Muestra;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnalisisCompletoMail extends Mailable
{
    use Queueable, SerializesModels;

    // Hacemos públicas las variables para que la vista del correo pueda usarlas
    public $muestra;
    public $limites;

    /**
     * Crea una nueva instancia del mensaje.
     * Ahora acepta la muestra Y los límites correspondientes.
     */
    public function __construct(Muestra $muestra, array $limites)
    {
        $this->muestra = $muestra;
        $this->limites = $limites;
    }

    /**
     * Define el "sobre" del mensaje.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Resultados de Análisis - Folio #' . $this->muestra->IdMuestra,
        );
    }

    /**
     * Define el contenido (la plantilla).
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.analisis-completo',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}