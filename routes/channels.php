<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    // Only allow the logged-in user to listen to their own channel
    return (int) $user->id === (int) $id;
});