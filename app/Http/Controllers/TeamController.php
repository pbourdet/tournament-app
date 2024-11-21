<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Requests\TeamStoreRequest;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class TeamController extends Controller
{
    public function store(TeamStoreRequest $request, Tournament $tournament): RedirectResponse
    {
        Gate::authorize('manage', $tournament);

        $lock = $this->checkLock($tournament);
        /** @var array<int, string> $members */
        $members = $request->members;

        $team = Team::create([
            'name' => $request->name ?? User::findOrFail(current($members))->name,
            'tournament_id' => $tournament->id,
        ]);
        $team->members()->attach($members);

        $lock->release();

        return redirect()->route('dashboard')->with(ToastType::SUCCESS->value, __('Team :name created', ['name' => $team->name]));
    }

    private function checkLock(Tournament $tournament): Lock
    {
        $lock = Cache::lock($tournament->getTeamsLockKey(), 20);

        if (!$lock->get()) {
            abort(409);
        }

        return $lock;
    }
}
