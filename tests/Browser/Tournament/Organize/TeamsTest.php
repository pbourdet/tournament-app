<?php

declare(strict_types=1);

namespace Tests\Browser\Tournament\Organize;

use App\Models\Tournament;
use Illuminate\Foundation\Testing\DatabaseTruncation;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class TeamsTest extends DuskTestCase
{
    use DatabaseTruncation;

    public function testGenerateTournamentTeams(): void
    {
        $tournament = Tournament::factory()->teamBased()->full()->create(['number_of_players' => 4]);

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@link-organize-teams')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'teams'])
                ->press('@generate-teams')
                ->waitForText(__('Teams generation in progress...'))
            ;
        });

        $this->assertDatabaseCount('teams', 2);
    }

    public function testCreateTeam(): void
    {
        $tournament = Tournament::factory()->teamBased()->full()->create(['number_of_players' => 4]);

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@link-organize-teams')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'teams'])
                ->press('@create-team-modal')
                ->type('createForm.name', 'Team 1')
                ->press('@select-members')
                ->press('@select-member-0')
                ->press('@select-member-1')
                ->press('@create-team')
                ->waitForText(__('Team :name created !', ['name' => 'Team 1']))
            ;
        });

        $this->assertDatabaseHas('teams', [
            'name' => 'Team 1',
        ]);
    }

    public function testDeleteTeam(): void
    {
        $tournament = Tournament::factory()->teamBased()->withAllTeams()->full()->create(['number_of_players' => 4]);
        $team = $tournament->teams()->firstOrFail();

        $this->browse(function (Browser $browser) use ($tournament, $team) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@link-organize-teams')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'teams'])
                ->press("@delete-team-{$team->id}")
                ->acceptDialog()
                ->waitForText(__('Team :name deleted !', ['name' => $team->name]))
            ;
        });

        $this->assertDatabaseMissing('teams', [
            'id' => $team->id,
        ]);
    }

    public function testEditTeamName(): void
    {
        $tournament = Tournament::factory()->teamBased()->withAllTeams()->full()->create(['number_of_players' => 4]);
        $team = $tournament->teams()->firstOrFail();

        $this->browse(function (Browser $browser) use ($tournament, $team) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@link-organize-teams')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'teams'])
                ->press("@edit-team-{$team->id}")
                ->type('newName', 'Team 1')
                ->press("@update-team-{$team->id}")
                ->waitForText(__('Team :name updated !', ['name' => 'Team 1']))
            ;
        });

        $this->assertDatabaseHas('teams', [
            'id' => $team->id,
            'name' => 'Team 1',
        ]);
    }
}
