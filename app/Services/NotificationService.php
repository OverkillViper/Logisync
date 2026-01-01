<?php

namespace App\Services;

use Illuminate\Support\Collection;
use App\Models\User;
use App\Notifications\SystemNotification;
use InvalidArgumentException;

class NotificationService
{
    public static function send(
        $users,
        string $title,
        string $message,
        string $url,
        string $type = 'system'
    ) {
        
        // ✅ Wrap single user correctly
        if ($users instanceof User) {
            $users = collect([$users]);
        }

        if (is_array($users)) {
            $users = User::whereIn('id', $users)->get();
        } elseif (! $users instanceof Collection) {
            throw new InvalidArgumentException('Invalid notification recipient');
        }

        foreach ($users as $user) {
            if (! $user instanceof User) {
                throw new InvalidArgumentException('Invalid notification recipient');
            }

            $user->notify(
                new SystemNotification($title, $message, $url, $type)
            );
        }
    }
}