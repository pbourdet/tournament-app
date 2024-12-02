<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function show(Tournament $tournament): View
    {
        Gate::authorize('view', $tournament);

        $tournament->load('teams.members');

        return view('tournaments.show', compact('tournament'));
    }
}
