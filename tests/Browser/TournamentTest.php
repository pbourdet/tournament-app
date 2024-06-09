<?php

declare(strict_types=1);

namespace Tests\Browser;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TournamentTest extends DuskTestCase
{
    use DatabaseTruncation;

    public function testJoinTournament(): void
    {
        $user = User::factory()->create();

        $tournament = Tournament::factory()->create();
        $fullTournament = Tournament::factory()->full()->create(['number_of_players' => 1]);
        $joinedTournament = Tournament::factory()->withPlayer($user)->create();

        $this->browse(function (Browser $browser) use ($user, $fullTournament, $tournament, $joinedTournament) {
            $browser
                ->loginAs($user)
                ->visit(route('dashboard'))
                ->press('button[type="button"]')
                ->type('invitation-code', 'ABCDEF')
                ->waitForText(__('No tournament with this invitation code.'), 5)
                ->clear('invitation-code')
                ->type('invitation-code', $fullTournament->invitation->code)
                ->waitForText(__('This tournament is full.'), 5)
                ->clear('invitation-code')
                ->type('invitation-code', $joinedTournament->invitation->code)
                ->waitForText(__('You are already taking part in this tournament.'), 5)
                ->clear('invitation-code')
                ->type('invitation-code', $tournament->invitation->code)
                ->waitForText($tournament->name)
                ->press('button[type="submit"]')
                ->waitForText(__('You joined tournament :name', ['name' => $tournament->name]))
            ;
        });

        $this->assertDatabaseHas('tournament_player', [
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
        ]);
    }

    public function testCreateTournament(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(route('tournaments.create'))
                ->type('name', 'My tournament')
                ->type('number_of_players', '4')
                ->type('description', 'This is a test tournament')
                ->press('button[type="submit"]')
                ->waitForText(__('Tournament :name created !', ['name' => 'My tournament']))
            ;

            $browser->assertRouteIs('dashboard');
        });

        $this->assertDatabaseCount('tournaments', 1);
        $this->assertDatabaseCount('tournament_player', 1);
    }

    public function testCreateTeamBasedTournament(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser
                ->loginAs($user)
                ->visit(route('tournaments.create'))
                ->type('name', 'My team tournament')
                ->type('number_of_players', '4')
                ->click('#toggle_join_tournament')
                ->click('#toggle_team_based')
                ->type('team_size', '2')
                ->type('description', 'This is a test team tournament')
                ->press('button[type="submit"]')
                ->waitForText(__('Tournament :name created !', ['name' => 'My team tournament']))
            ;

            $browser->assertRouteIs('dashboard');
        });

        $this->assertDatabaseCount('tournaments', 1);
        $this->assertDatabaseCount('tournament_player', 0);
    }
}
