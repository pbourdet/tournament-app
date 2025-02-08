<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Jobs\GenerateTeams;
use App\Livewire\Tournament\Organize\Teams;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Teams::class, ['tournament' => Tournament::factory()->teamBased()->create()])
            ->assertStatus(200);
    }

    public function testRendersForNonTeamBasedTournament(): void
    {
        Livewire::test(Teams::class, ['tournament' => Tournament::factory()->create()])
            ->assertSuccessful()
            ->assertSee(__('Your tournament is not played in teams. You can change this setting in the "General" tab.'));
    }

    public function testOrganizerCanGenerateTeams(): void
    {
        Queue::fake();

        $tournament = Tournament::factory()->full()->teamBased()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        Queue::assertPushed(GenerateTeams::class);
    }

    public function testNonOrganizerCantGenerateTeams(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $tournament = Tournament::factory()->teamBased()->withPlayers([$user])->full()->create();

        Livewire::actingAs($user)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');

        Queue::assertNotPushed(GenerateTeams::class);
    }

    public function testOrganizerCantGenerateTeamsOnNonFullTournament(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->teamBased()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');

        Queue::assertNotPushed(GenerateTeams::class);
    }

    public function testOrganizerCantGenerateTeamsOnNonTeamBasedTournament(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');

        Queue::assertNotPushed(GenerateTeams::class);
    }

    public function testOrganizerCantGenerateTeamsOnTournamentWithFullTeams(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->withFullTeams()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');

        Queue::assertNotPushed(GenerateTeams::class);
    }

    public function testOrganizerCantGenerateTeamsOnTournamentIfGenerationAlreadyOngoing(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->full()->teamBased()->create();

        Cache::lock($tournament->getLockKey(), 20)->get();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertConflict()
            ->assertNotDispatched('toast-show');

        Queue::assertNotPushed(GenerateTeams::class);
    }
}
