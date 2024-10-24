<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\ToastType;
use App\Http\Requests\TournamentStoreRequest;
use App\Models\Tournament;
use App\Models\TournamentInvitation;
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

    public function create(): View|RedirectResponse
    {
        if (!Gate::allows('create', Tournament::class)) {
            return redirect()->back()->with(ToastType::DANGER->value, __('You cannot create more tournaments'));
        }

        return view('tournaments.create');
    }

    public function store(TournamentStoreRequest $request): RedirectResponse
    {
        Gate::authorize('create', Tournament::class);

        /** @var User $user */
        $user = $request->user();

        $tournament = Tournament::create([
            'organizer_id' => $user->id,
            'name' => $request->name,
            'number_of_players' => $request->number_of_players,
            'description' => $request->description,
            'team_based' => $request->boolean('team_based'),
            'team_size' => $request->boolean('team_based') ? $request->integer('team_size') : null,
        ]);

        TournamentInvitation::fromTournament($tournament);

        if ($request->boolean('join_tournament')) {
            $tournament->players()->attach($user);
        }

        return redirect()->route('tournaments.show', ['tournament' => $tournament])->with(ToastType::SUCCESS->value, __('Tournament :name created !', ['name' => $tournament->name]));
    }

    public function show(Tournament $tournament, User $user): View
    {
        Gate::authorize('view', $tournament);

        $tournament->load('teams.members');

        return view('tournaments.show', compact('tournament'));
    }
}
