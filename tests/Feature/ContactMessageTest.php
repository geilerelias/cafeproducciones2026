<?php

use App\Mail\ContactMessageMail;
use Illuminate\Support\Facades\Mail;

test('contact form sends a contact message email', function () {
    Mail::fake();

    $response = $this->post(route('contact.store'), [
        'name' => 'Cliente Demo',
        'email' => 'cliente@example.com',
        'phone' => '3001234567',
        'subject' => 'Cotizacion de evento',
        'message' => 'Necesito una cotizacion para un evento corporativo en Riohacha.',
    ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success');

    Mail::assertSent(ContactMessageMail::class, function (ContactMessageMail $mail) {
        return $mail->messageData['email'] === 'cliente@example.com'
            && $mail->messageData['subject'] === 'Cotizacion de evento';
    });
});

test('contact form ignores honeypot submissions', function () {
    Mail::fake();

    $response = $this->post(route('contact.store'), [
        'name' => 'Spam Demo',
        'email' => 'spam@example.com',
        'subject' => 'Spam',
        'message' => 'Este mensaje simula un envio automatizado con honeypot lleno.',
        'website' => 'https://spam.example.com',
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');

    Mail::assertNothingSent();
});
