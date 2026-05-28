<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirma tu correo - {{ config('brand.name') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: 'Segoe UI', Arial, sans-serif; color: #18181b; line-height: 1.6;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f5; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="background: linear-gradient(135deg, #18181b 0%, #3f1815 55%, #a8322b 100%); padding: 32px 28px; text-align: center;">
                            @if (! empty($logoUrl))
                                <img src="{{ $logoUrl }}" alt="{{ config('brand.name') }}" width="72" height="72" style="display: block; margin: 0 auto 16px; border-radius: 8px; background: #ffffff; padding: 8px;">
                            @endif
                            <p style="margin: 0; font-size: 12px; font-weight: 700; letter-spacing: 0.22em; text-transform: uppercase; color: #f0c8be;">Acceso al portal</p>
                            <h1 style="margin: 10px 0 0; font-size: 26px; font-weight: 800; color: #ffffff;">{{ config('brand.name') }}</h1>
                            <p style="margin: 8px 0 0; font-size: 14px; color: #f5d5cf;">{{ config('brand.tagline') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 36px 32px 12px;">
                            <p style="margin: 0 0 8px; font-size: 18px; font-weight: 700; color: #18181b;">
                                Hola, {{ $userName }}.
                            </p>
                            <p style="margin: 0 0 20px; font-size: 15px; color: #52525b;">
                                Gracias por registrarte en el portal de {{ config('brand.name') }}. Para activar tu cuenta y acceder al panel, confirma tu direccion de correo electronico.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto 24px;">
                                <tr>
                                    <td style="border-radius: 8px; background-color: #a8322b;">
                                        <a href="{{ $verificationUrl }}" target="_blank" rel="noopener" style="display: inline-block; padding: 14px 28px; font-size: 15px; font-weight: 800; color: #ffffff; text-decoration: none;">
                                            Confirmar correo electronico
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 12px; font-size: 14px; color: #52525b;">
                                Este enlace expira en 60 minutos por seguridad. Si no creaste una cuenta con nosotros, puedes ignorar este mensaje.
                            </p>
                            <p style="margin: 0; font-size: 13px; color: #71717a;">
                                Si el boton no funciona, copia y pega este enlace en tu navegador:
                            </p>
                            <p style="margin: 8px 0 0; font-size: 12px; word-break: break-all;">
                                <a href="{{ $verificationUrl }}" style="color: #a8322b;">{{ $verificationUrl }}</a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 8px 32px 32px;">
                            <p style="margin: 0 0 4px; font-size: 14px; color: #18181b;">Saludos cordiales,</p>
                            <p style="margin: 0; font-size: 14px; font-weight: 700; color: #a8322b;">Equipo {{ config('brand.name') }}</p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color: #fafafa; border-top: 1px solid #e4e4e7; padding: 20px 32px; text-align: center;">
                            <p style="margin: 0 0 6px; font-size: 12px; color: #71717a;">
                                {{ config('brand.name') }} &middot; Riohacha, La Guajira, Colombia
                            </p>
                            <p style="margin: 0; font-size: 12px; color: #71717a;">
                                <a href="mailto:{{ config('brand.email') }}" style="color: #a8322b; text-decoration: none;">{{ config('brand.email') }}</a>
                                &middot; {{ config('brand.phone') }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
