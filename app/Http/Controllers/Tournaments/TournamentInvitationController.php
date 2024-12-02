<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tournaments;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TournamentInvitationController extends Controller
{
    public function store(Tournament $tournament): View
    {
        Gate::authorize('manage', $tournament);

        TournamentInvitation::where('tournament_id', $tournament->id)->delete();

        $invitation = TournamentInvitation::fromTournament($tournament);

        return view('tournaments.invitations.show', compact('tournament', 'invitation'));
    }
}
