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

Broadcast::channel('classroom.{id}', function ($user, $id) {
    if ($user->user_type == 'Student') {
        return (int) $user->profile->class_id === (int) $id;
    }
    return false;
});

Broadcast::channel('party.{id}', function ($user, $id) {
    if ($user->user_type == 'Student') {
        return (int) $user->profile->character->party_id === (int) $id;
    }
    return false;
});

Broadcast::channel('character.{id}', function ($user, $id) {
    if ($user->user_type == 'Student') {
        return (int) $user->id === (int) $id;
    }
    return false;
});