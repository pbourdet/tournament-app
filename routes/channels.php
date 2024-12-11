<?php

declare(strict_types=1);

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Gate;

Broadcast::channel('App.Models.User.{id}', function (User $user, string $id) {
    return $user->id === $id;
});

Broadcast::channel('App.Models.Tournament.{id}', function (User $user, string $id) {
    $tournament = Tournament::findOrFail($id);

    return Gate::allows('view', $tournament);
});
