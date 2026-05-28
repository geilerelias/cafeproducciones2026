Hola, {{ $userName }}.

Gracias por registrarte en el portal de {{ config('brand.name') }}.

Para activar tu cuenta, confirma tu correo electronico visitando este enlace:
{{ $verificationUrl }}

Este enlace expira en 60 minutos. Si no creaste una cuenta con nosotros, puedes ignorar este mensaje.

Saludos cordiales,
Equipo {{ config('brand.name') }}

{{ config('brand.email') }} | {{ config('brand.phone') }}
