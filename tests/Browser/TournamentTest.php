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
}
