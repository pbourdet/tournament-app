<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Events\TeamsGenerated;
use App\Livewire\Tournament\Teams;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();

        Livewire::test(Teams::class, ['tournament' => $tournament])
            ->assertStatus(200);
    }

    public function testOrganizerCanGenerateTeams(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertSuccessful()
            ->assertDispatched('toast-trigger');

        Event::assertDispatched(TeamsGenerated::class);
    }

    public function testNonOrganizerCantGenerateTeams(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->teamBased()->full()->create();

        Livewire::actingAs($user)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        Event::assertNotDispatched(TeamsGenerated::class);
    }

    public function testOrganizerCantGenerateTeamsOnNonFullTournament(): void
    {
        $tournament = Tournament::factory()->teamBased()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        Event::assertNotDispatched(TeamsGenerated::class);
    }

    public function testOrganizerCantGenerateTeamsOnNonTeamBasedTournament(): void
    {
        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        Event::assertNotDispatched(TeamsGenerated::class);
    }

    public function testOrganizerCantGenerateTeamsOnTournamentWithAllTeams(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        Event::assertNotDispatched(TeamsGenerated::class);
    }

    public function testOrganizerCantGenerateTeamsOnTournamentIfGenerationAlreadyOngoing(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->create();

        Cache::lock($tournament->getTeamsLockKey(), 20)->get();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertConflict()
            ->assertNotDispatched('toast-trigger');

        Event::assertNotDispatched(TeamsGenerated::class);
    }

    public function testOrganizerCanDeleteATeam(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('delete', $team)
            ->assertSuccessful()
            ->assertDispatched('toast-trigger');

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function testNonOrganizerCantDeleteTeams(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->withAllTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Livewire::actingAs($user)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('delete', $team)
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }
}
