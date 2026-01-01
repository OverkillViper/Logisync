<?php

use App\Services\NotificationService;

function notify(
    $users,
    string $title,
    string $message,
    string $url,
    string $type = 'system'
) {
    NotificationService::send(
        $users,
        $title,
        $message,
        $url,
        $type
    );
}