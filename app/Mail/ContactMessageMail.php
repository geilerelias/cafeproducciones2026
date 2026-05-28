<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  array{name:string,email:string,phone?:string|null,subject?:string|null,message:string}  $messageData
     */
    public function __construct(public array $messageData)
    {
    }

    public function envelope(): Envelope
    {
        $subject = trim((string) ($this->messageData['subject'] ?? '')) ?: 'Mensaje desde el formulario de contacto';

        return new Envelope(
            replyTo: [
                new Address($this->messageData['email'], $this->messageData['name']),
            ],
            subject: 'CAFE Producciones - '.$subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.contact-message',
            text: 'mail.contact-message-text',
            with: [
                'messageData' => $this->messageData,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
