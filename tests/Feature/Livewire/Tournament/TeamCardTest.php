<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\TeamCard;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeamCardTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();

        Livewire::test(TeamCard::class, ['team' => $tournament->teams->first(), 'tournament' => $tournament, 'locked' => false])
            ->assertStatus(200);
    }

    public function testOrganizerCanUpdateTeamName(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();
        $team = $tournament->teams->first();

        Livewire::actingAs($tournament->organizer)
            ->test(TeamCard::class, ['team' => $team, 'tournament' => $tournament, 'locked' => false])
            ->set('newName', 'New Team Name')
            ->call('update')
            ->assertDispatched('toast-show');

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'New Team Name']);
    }

    public function testMemberOfTeamCanUpdateName(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();
        $team = $tournament->teams->first();
        $player = $team->members->first();

        Livewire::actingAs($player)
            ->test(TeamCard::class, ['team' => $team, 'tournament' => $tournament, 'locked' => false])
            ->set('newName', 'New Team Name')
            ->call('update')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'New Team Name']);
    }

    public function testNonMemberOfTeamCannotUpdateTeamName(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();
        $team = $tournament->teams->first();
        $player = $tournament->teams()->where('id', '!=', $team->id)->first()->members->first();

        Livewire::actingAs($player)
            ->test(TeamCard::class, ['team' => $team, 'tournament' => $tournament, 'locked' => false])
            ->set('newName', 'New Team Name')
            ->call('update')
            ->assertForbidden();

        $this->assertDatabaseMissing('teams', ['id' => $team->id, 'name' => 'New Team Name']);
    }
}
