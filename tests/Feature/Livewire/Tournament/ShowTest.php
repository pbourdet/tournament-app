<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Jobs\GenerateTeams;
use App\Livewire\Tournament\Show;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewTournamentAsOrganizer(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create(['organizer_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['tournament' => $tournament])
            ->assertStatus(200)
            ->assertSee($tournament->name);
    }

    public function testUserCanViewTournamentAsPlayer(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();
        $tournament->players()->attach($user);

        Livewire::actingAs($user)
            ->test(Show::class, ['tournament' => $tournament])
            ->assertStatus(200)
            ->assertSee($tournament->name);
    }

    public function testUserCannotViewTournament(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($user)
            ->test(Show::class, ['tournament' => $tournament])
            ->assertForbidden();
    }

    public function testOrganizerCanGenerateTeams(): void
    {
        Queue::fake();

        $tournament = Tournament::factory()->full()->teamBased()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Show::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertSuccessful()
            ->assertDispatched('toast-trigger');

        Queue::assertPushed(GenerateTeams::class);
    }

    public function testNonOrganizerCantGenerateTeams(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $tournament = Tournament::factory()->teamBased()->withPlayers([$user])->full()->create();

        Livewire::actingAs($user)
            ->test(Show::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        Queue::assertNotPushed(GenerateTeams::class);
    }

    public function testOrganizerCantGenerateTeamsOnNonFullTournament(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->teamBased()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Show::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        Queue::assertNotPushed(GenerateTeams::class);
    }

    public function testOrganizerCantGenerateTeamsOnNonTeamBasedTournament(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Show::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        Queue::assertNotPushed(GenerateTeams::class);
    }

    public function testOrganizerCantGenerateTeamsOnTournamentWithAllTeams(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->withAllTeams()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Show::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');

        Queue::assertNotPushed(GenerateTeams::class);
    }

    public function testOrganizerCantGenerateTeamsOnTournamentIfGenerationAlreadyOngoing(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->full()->teamBased()->create();

        Cache::lock($tournament->getTeamsLockKey(), 20)->get();

        Livewire::actingAs($tournament->organizer)
            ->test(Show::class, ['tournament' => $tournament])
            ->call('generate')
            ->assertConflict()
            ->assertNotDispatched('toast-trigger');

        Queue::assertNotPushed(GenerateTeams::class);
    }
}
