<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nuevo mensaje de contacto</title>
</head>
<body style="font-family: Arial, sans-serif; color: #18181b; line-height: 1.6;">
    <h1 style="font-size: 22px;">Nuevo mensaje de contacto</h1>

    <p><strong>Nombre:</strong> {{ $messageData['name'] }}</p>
    <p><strong>Email:</strong> {{ $messageData['email'] }}</p>
    <p><strong>Telefono:</strong> {{ $messageData['phone'] ?? 'No indicado' }}</p>
    <p><strong>Tema:</strong> {{ $messageData['subject'] ?? 'Sin tema' }}</p>

    <hr style="border: 0; border-top: 1px solid #e4e4e7; margin: 24px 0;">

    <p style="white-space: pre-line;">{{ $messageData['message'] }}</p>
</body>
</html>
