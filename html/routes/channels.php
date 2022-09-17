<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('public', function () {
    return true;
});

Broadcast::channel('admin', function ($user) {
    return $user->is_admin;
});

Broadcast::channel('private.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
