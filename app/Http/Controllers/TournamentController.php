<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class TournamentController extends Controller
{
    public function join(Tournament $tournament): RedirectResponse
    {
        Gate::authorize('join', $tournament);

        $tournament->players()->attach(Auth::user());

        return redirect()->route('dashboard');
    }
}
