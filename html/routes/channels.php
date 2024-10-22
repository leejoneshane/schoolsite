<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Student;
use App\Models\GameCharacter;

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
        $student = Student::find($user->uuid);
        return (int) $student->class_id === (int) $id;
    }
    return false;
});

Broadcast::channel('party.{id}', function ($user, $id) {
    if ($user->user_type == 'Student') {
        $character = GameCharacter::find($user->uuid);
        return (int) $character->party_id === (int) $id;
    }
    return false;
});

Broadcast::channel('character.{stdno}', function ($user, $stdno) {
    if ($user->user_type == 'Student') {
        $student = Student::find($user->uuid);
        return (int) $student->stdno === (int) $stdno;
    }
    return false;
});

Broadcast::channel('dialog.{stdno}', function ($user, $stdno) {
    if ($user->user_type == 'Student') {
        $student = Student::find($user->uuid);
        return (int) $student->stdno === (int) $stdno;
    }
    return false;
});