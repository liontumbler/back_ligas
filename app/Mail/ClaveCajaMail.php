<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClaveCajaMail extends Mailable
{
    use Queueable, SerializesModels;

    private $claveCajaNueva;
    private $usuario;
    /**
     * Create a new message instance.
     */
    public function __construct($claveCajaNueva, $usuario)
    {
        $this->claveCajaNueva = $claveCajaNueva;
        $this->usuario = $usuario;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Login Clave Caja',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.cambioClave',
            with: [
                'claveCajaNueva' => $this->claveCajaNueva,
                'usuario' => $this->usuario,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
