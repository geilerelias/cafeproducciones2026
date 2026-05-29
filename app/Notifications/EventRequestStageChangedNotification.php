<?php

namespace App\Notifications;

use App\Models\EventRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventRequestStageChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public EventRequest $eventRequest,
        public string $previousStageName,
        public string $newStageName,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Actualizacion de tu solicitud '.$this->eventRequest->reference)
            ->greeting('Hola '.$notifiable->name.',')
            ->line('Tu solicitud de evento «'.$this->eventRequest->title.'» cambio de etapa.')
            ->line('Etapa anterior: '.$this->previousStageName)
            ->line('Etapa actual: '.$this->newStageName)
            ->action('Ver seguimiento', route('event-requests.show', $this->eventRequest))
            ->line('Gracias por confiar en CAFE Producciones.');
    }
}
