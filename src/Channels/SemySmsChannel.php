<?php

namespace Allanvb\LaravelSemysms\Channels;

use Allanvb\LaravelSemysms\Facades\SemySMS;
use Illuminate\Notifications\Notification;

class SemySmsChannel
{
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toSemySms($notifiable);

        if (!$message->to) {
            $message->to(
                $notifiable->routeNotificationFor('semy-sms')
            );
        }

        return SemySMS::sendOne([
            'to' => $message->to,
            'text' => $message->text
        ]);
    }
}
