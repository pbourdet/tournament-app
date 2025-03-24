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
        $tournament = Tournament::factory()->full()->create();

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

        $this->assertNotNull($tournament->refresh()->groupPhase);
        $this->assertSame(2, $tournament->groupPhase->number_of_groups);
        $this->assertSame(2, $tournament->groupPhase->qualifying_per_group);
    }

    public function testAddContestantToGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament, 'page' => 'groups']))
                ->click('@tab-groups')
                ->click('@select-contestants')
                ->click(sprintf('@select-contestant-%d', 0))
                ->waitForText(__(':contestant added to group !', ['contestant' => mb_ucfirst($tournament->getContestantsTranslation())]))
            ;
        });
    }

    public function testRemoveContestantFromGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first()->load('contestants');
        $group->addContestants([$tournament->contestants()->first()]);

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament, 'page' => 'groups']))
                ->click('@tab-groups')
                ->click(sprintf('@remove-contestant-%d', 0))
                ->waitForText(__(':contestant removed from group !', ['contestant' => mb_ucfirst($tournament->getContestantsTranslation())]))
            ;
        });
    }

    public function testOrganizerCanGenerateGroups(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create(['number_of_players' => 8]);

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament, 'page' => 'groups']))
                ->click('@tab-groups')
                ->click('@generate-groups')
                ->waitForText(__('Groups generation in progress...'))
            ;
        });

        $this->assertDatabaseCount('group_contestant', 8);
    }

    public function testOrganizerCanDeleteAGroupPhase(): void
    {
        $tournament = Tournament::factory()->withGroupPhase()->create();

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament, 'page' => 'groups']))
                ->click('@delete-group-phase')
                ->waitForText(__('Phase deleted !'))
            ;
        });

        $this->assertDatabaseCount('group_phases', 0);
        $this->assertNull($tournament->refresh()->groupPhase);
    }
}
