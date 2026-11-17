<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class AccountDecisionNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $decision,
        public string $message,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'decision' => $this->decision,
            'message' => $this->message,
        ];
    }
}
