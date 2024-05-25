<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ToastType;
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
        if (!Gate::allows('join', $tournament)) {
            return redirect()->back()->with(ToastType::DANGER->value, __('You cannot join this tournament.'));
        }

        $tournament->players()->attach(Auth::user());

        return redirect()->back()->with(ToastType::SUCCESS->value, __('You joined tournament :name', ['name' => $tournament->name]));
    }

    public function show(string $code): View
    {
        $tournamentInvitation = TournamentInvitation::where('code', mb_strtoupper($code))->first();

        return view('tournaments.join', ['tournament' => $tournamentInvitation?->tournament]);
    }
}
