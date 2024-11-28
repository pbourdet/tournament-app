<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Models\Tournament;
use App\Models\User;
use App\Notifications\PlayerJoined;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function join(Tournament $tournament): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        if (!Gate::allows('join', $tournament)) {
            return redirect()->back()->with(ToastType::DANGER->value, __('You cannot join this tournament.'));
        }

        $tournament->players()->attach($user);

        $tournament->organizer?->notify((new PlayerJoined($tournament, $user))->afterCommit());

        return redirect()->back()->with(ToastType::SUCCESS->value, __('You joined tournament :name', ['name' => $tournament->name]));
    }

    public function show(Tournament $tournament): View
    {
        Gate::authorize('view', $tournament);

        $tournament->load('teams.members');

        return view('tournaments.show', compact('tournament'));
    }
}
