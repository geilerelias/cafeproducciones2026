<?php

namespace App\Support;

use Illuminate\Http\RedirectResponse;

class Feedback
{
    /**
     * @return array{type: string, title: string, message: string}
     */
    public static function payload(string $type, string $title, string $message): array
    {
        return [
            'type' => $type,
            'title' => $title,
            'message' => $message,
        ];
    }

    public static function success(string $message, string $title = 'Listo'): RedirectResponse
    {
        return back()->with([
            'feedback' => self::payload('success', $title, $message),
            'success' => $message,
            'success_title' => $title,
        ]);
    }

    public static function redirectSuccess(string $url, string $message, string $title = 'Listo'): RedirectResponse
    {
        return redirect($url)->with([
            'feedback' => self::payload('success', $title, $message),
            'success' => $message,
            'success_title' => $title,
        ]);
    }

    public static function error(string $message, string $title = 'No se pudo completar'): RedirectResponse
    {
        return back()->with([
            'feedback' => self::payload('error', $title, $message),
            'error' => $message,
            'error_title' => $title,
        ]);
    }
}
