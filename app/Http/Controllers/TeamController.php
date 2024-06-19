<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TeamStoreRequest;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    public function store(TeamStoreRequest $request, Tournament $tournament): RedirectResponse
    {
        Gate::authorize('manage', $tournament);

        $team = Team::create([
            'name' => $request->name,
            'tournament_id' => $tournament->id,
        ]);
        $team->members()->attach($request->members);

        return redirect()->route('dashboard')->with('toast_success', __('Team :name created', ['name' => $team->name]));
    }
}
