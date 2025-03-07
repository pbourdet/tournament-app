<?php

declare(strict_types=1);

namespace Tests\Browser\Tournament\Organize;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PlayersTest extends DuskTestCase
{
    use DatabaseTruncation;

    public function testRemovePlayerFromTournament(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 4]);
        $player = $tournament->players->first();

        $this->browse(function (Browser $browser) use ($tournament, $player) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@link-organize-players')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'players'])
                ->press('@remove-player-0')
                ->waitForText(__('Player :name removed from tournament.', ['name' => $player->username]))
            ;
        });

        $this->assertDatabaseCount('tournament_player', 3);
    }
}
