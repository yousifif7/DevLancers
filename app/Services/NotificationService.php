<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\AppNotification;

class NotificationService
{
    public static function send(User $user, string $title, string $message, ?string $url = null, string $type = 'info'): void
    {
        try {
            $user->notify(new AppNotification($title, $message, $url, $type));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
