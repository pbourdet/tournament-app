<?php

declare(strict_types=1);

namespace Tests\Browser\Tournament\Organize;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class EliminationTest extends DuskTestCase
{
    use DatabaseTruncation;

    public function testCreateAnEliminationPhase(): void
    {
        $tournament = Tournament::factory()->teamBased()->full()->create(['number_of_players' => 4]);

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@link-organize-elimination')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'elimination'])
                ->select('@number-of-contestants', '4')
                ->click('@create-elimination')
                ->waitForText(__('Elimination phase created successfully !'))
            ;
        });

        $this->assertDatabaseCount('elimination_phases', 1);
    }
}
