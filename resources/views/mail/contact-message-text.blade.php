Nuevo mensaje de contacto

Nombre: {{ $messageData['name'] }}
Email: {{ $messageData['email'] }}
Telefono: {{ $messageData['phone'] ?? 'No indicado' }}
Tema: {{ $messageData['subject'] ?? 'Sin tema' }}

Mensaje:
{{ $messageData['message'] }}
