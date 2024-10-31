<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function (User $user) {
            Tournament::factory()->create([
                'organizer_id' => $user->id,
            ]);

            $user->managedTournaments()->each(function (Tournament $tournament) {
                $players = User::inRandomOrder()
                    ->take(rand(1, $tournament->number_of_players))
                    ->get();

                $tournament->players()->attach($players);
            });
        });

        $teamUser = User::where('email', 'team@example.com')->firstOrFail();
        $teamTournament = Tournament::factory()->create([
            'organizer_id' => $teamUser->id,
            'number_of_players' => 16,
            'team_based' => true,
            'team_size' => 2,
        ]);

        $players = User::inRandomOrder()->take(5)->get();
        $teamTournament->players()->attach($players);
        $teams = $players->chunk(2);
        foreach ($teams as $team) {
            if (1 === $team->count()) {
                continue;
            }

            $teamTournament->teams()->save(Team::factory()->withMembers($team)->create(['tournament_id' => $teamTournament->id]));
        }

        $fullTeamTournamentUser = User::where('email', 'full-team@example.com')->firstOrFail();
        Tournament::factory()->teamBased()->full()->create([
            'organizer_id' => $fullTeamTournamentUser->id,
            'number_of_players' => 8,
        ]);
    }
}
