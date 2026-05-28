<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactAssistantController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'max:800'],
        ]);

        $message = str($validated['message'])->lower()->ascii()->toString();

        $answer = match (true) {
            str_contains($message, 'precio') || str_contains($message, 'cotiza') || str_contains($message, 'costo') => 'Para cotizar necesitamos fecha, ciudad, tipo de evento, numero de asistentes y servicios requeridos. Puedes dejar esos datos aqui o escribir por WhatsApp.',
            str_contains($message, 'sonido') || str_contains($message, 'audio') => 'Ofrecemos sonido profesional para eventos sociales, institucionales y corporativos. Indicanos el aforo y el espacio para estimar equipos.',
            str_contains($message, 'pantalla') || str_contains($message, 'piso led') || str_contains($message, 'led') => 'Tenemos pantallas y piso LED para montajes de alto impacto. Para validar disponibilidad necesitamos fecha, horario y lugar.',
            str_contains($message, 'personal') || str_contains($message, 'trabajo') || str_contains($message, 'empleo') => 'Si buscas trabajar con nosotros, registra tus datos basicos y experiencia. El equipo administrativo revisara tu informacion.',
            str_contains($message, 'ubicacion') || str_contains($message, 'direccion') || str_contains($message, 'riohacha') => 'Estamos en Riohacha, La Guajira. Atendemos eventos en la ciudad y otros municipios segun disponibilidad.',
            default => 'Puedo ayudarte con cotizaciones, disponibilidad, servicios de sonido, pantallas, montaje, personal y datos de contacto. Cuentame fecha, lugar y necesidad principal.',
        };

        return response()->json(['answer' => $answer]);
    }
}
