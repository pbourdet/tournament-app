<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Requests\TeamStoreRequest;
use App\Jobs\GenerateTeams;
use App\Models\Team;
use App\Models\Tournament;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    public function store(TeamStoreRequest $request, Tournament $tournament): RedirectResponse
    {
        Gate::authorize('manage', $tournament);

        $lock = $this->checkLock($tournament);

        $team = Team::create([
            'name' => $request->name ?? $tournament->getNextTeamName(),
            'tournament_id' => $tournament->id,
        ]);
        $team->members()->attach($request->members);

        $lock->release();

        return redirect()->route('dashboard')->with(ToastType::SUCCESS->value, __('Team :name created', ['name' => $team->name]));
    }

    public function generate(Tournament $tournament): Response
    {
        Gate::authorize('manage', $tournament);

        if (!$tournament->team_based || !$tournament->isFull()) {
            abort(403);
        }

        $this->checkLock($tournament);

        GenerateTeams::dispatch($tournament);

        return response('', 202);
    }

    private function checkLock(Tournament $tournament): Lock
    {
        $lock = Cache::lock(sprintf('tournament:%s:generate-teams', $tournament->id), 20);

        if (!$lock->get()) {
            abort(409);
        }

        return $lock;
    }
}
