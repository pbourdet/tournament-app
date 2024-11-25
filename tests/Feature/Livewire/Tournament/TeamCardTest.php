<?php

declare(strict_types=1);

namespace Feature\Livewire\Tournament;

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
        $tournament = Tournament::factory()->withAllTeams()->create();

        Livewire::test(TeamCard::class, ['team' => $tournament->teams->first(), 'tournament' => $tournament, 'generationInProgress' => false])
            ->assertStatus(200);
    }

    public function testOrganizerCanUpdateTeamName(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();
        $team = $tournament->teams->first();

        Livewire::actingAs($tournament->organizer)
            ->test(TeamCard::class, ['team' => $team, 'tournament' => $tournament, 'generationInProgress' => false])
            ->set('newName', 'New Team Name')
            ->call('update')
            ->assertDispatched('toast-trigger');

        $this->assertDatabaseHas('teams', ['id' => $team->id, 'name' => 'New Team Name']);
    }

    public function testNonOrganizerCannotUpdateTeamName(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();
        $team = $tournament->teams->first();

        Livewire::actingAs($tournament->players->first())
            ->test(TeamCard::class, ['team' => $team, 'tournament' => $tournament, 'generationInProgress' => false])
            ->set('newName', 'New Team Name')
            ->call('update')
            ->assertForbidden();

        $this->assertDatabaseMissing('teams', ['id' => $team->id, 'name' => 'New Team Name']);
    }
}
