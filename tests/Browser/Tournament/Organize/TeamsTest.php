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

        $this->assertDatabaseCount('team_user', 4);
    }

    public function testAddPlayerToTeam(): void
    {
        $tournament = Tournament::factory()->teamBased()->full()->create(['number_of_players' => 4]);

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@link-organize-teams')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'teams'])
                ->press('@select-members')
                ->press('@select-member-1')
                ->waitForText(__('Player added to team !'))
            ;
        });

        $this->assertDatabaseCount('team_user', 1);
    }

    public function testRemovePlayerFromTeam(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create(['number_of_players' => 4]);

        $this->browse(function (Browser $browser) use ($tournament) {
            $browser
                ->loginAs($tournament->organizer)
                ->visit(route('tournaments.organize', ['tournament' => $tournament]))
                ->click('@link-organize-teams')
                ->waitForRoute('tournaments.organize', ['tournament' => $tournament, 'page' => 'teams'])
                ->press('@remove-member-0')
                ->waitForText(__('Player removed from team !'))
            ;
        });

        $this->assertDatabaseCount('team_user', 3);
    }

    public function testEditTeamName(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();
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

    public function testTeamMemberCanEditTeamName(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();
        $team = $tournament->teams()->firstOrFail();

        $this->browse(function (Browser $browser) use ($tournament, $team) {
            $browser
                ->loginAs($team->members->first())
                ->visit(route('tournaments.show', ['tournament' => $tournament]))
                ->click('@link-teams')
                ->waitForRoute('tournaments.show', ['tournament' => $tournament, 'page' => 'teams'])
                ->press("@edit-team-{$team->id}")
                ->pause(100)
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
