<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Models\TournamentInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function join(Tournament $tournament): RedirectResponse
    {
        Gate::authorize('join', $tournament);

        $tournament->players()->attach(Auth::user());

        return redirect()->route('dashboard');
    }

    public function show(string $code): View
    {
        $tournamentInvitation = TournamentInvitation::where('code', mb_strtoupper($code))->first();

        return view('tournaments.join', ['tournament' => $tournamentInvitation?->tournament]);
    }
}
