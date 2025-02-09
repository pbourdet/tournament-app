<?php

declare(strict_types=1);

namespace Tests\Browser\Tournament\Organize;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class GroupsTest extends DuskTestCase
{
    use DatabaseTruncation;

    public function testCreateGroupPhase(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 8]);

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@qualification-dropdown')
                ->click('@link-organize-groups')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'groups'])
                ->type('@input-number-of-groups', 2)
                ->type('@input-contestants-qualifying', 2)
                ->click('@create-group-phase')
                ->waitForText(__('Phase created !'))
            ;
        });

        $this->assertNotNull($tournament->groupPhase);
        $this->assertSame(2, $tournament->groupPhase->number_of_groups);
        $this->assertSame(2, $tournament->groupPhase->qualifying_per_group);
    }
}
