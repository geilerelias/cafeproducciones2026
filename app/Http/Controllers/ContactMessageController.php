<?php

namespace App\Http\Controllers;

use App\Mail\ContactMessageMail;
use App\Support\Feedback;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class ContactMessageController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:160'],
            'phone' => ['nullable', 'string', 'max:60'],
            'subject' => ['nullable', 'string', 'max:160'],
            'message' => ['required', 'string', 'min:10', 'max:4000'],
            'website' => ['nullable', 'string', 'max:120'],
        ]);

        if (! empty($validated['website'])) {
            return back()->with('success', 'Mensaje recibido correctamente.');
        }

        unset($validated['website']);

        try {
            Mail::to(config('mail.from.address'))->send(new ContactMessageMail($validated));
        } catch (Throwable $exception) {
            report($exception);

            return back()->withErrors([
                'contact' => 'No pudimos enviar el mensaje en este momento. Intenta nuevamente o escribenos por WhatsApp.',
            ])->withInput();
        }

        return Feedback::success(
            'Gracias por contactarnos. Te responderemos pronto desde CAFE Producciones.',
            'Mensaje enviado',
        );
    }
}
