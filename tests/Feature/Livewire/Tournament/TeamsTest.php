<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Events\TeamsGenerated;
use App\Livewire\Tournament\Teams;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Ramsey\Uuid\Uuid;
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

    public function testOrganizerCantDeleteTeamIfGenerationIsOngoing(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Cache::lock($tournament->getTeamsLockKey(), 20)->get();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('delete', $team)
            ->assertConflict()
            ->assertNotDispatched('toast-trigger');

        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }

    public function testUserCanCreateATeam(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(4)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', [$users[0]->id, $users[1]->id])
            ->call('create')
            ->assertSuccessful()
            ->assertDispatched('toast-trigger');

        $this->assertDatabaseHas('teams', ['name' => 'team name']);
        $this->assertDatabaseCount('teams', 1);
    }

    public function testUserCannotCreateTeamInNotTeamBasedTournament(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');
    }

    public function testUserCannotCreateATeamWithNonExistingUsers(): void
    {
        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', [Uuid::uuid4()->toString(), Uuid::uuid4()->toString()])
            ->call('create')
            ->assertHasErrors('createForm.members.*')
            ->assertNotDispatched('toast-trigger');
    }

    public function testUserCannotCreateTeamWithInvalidNumberOfMembers(): void
    {
        $organizer = User::factory()->create();
        $user = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', [$user->id])
            ->call('create')
            ->assertHasErrors('createForm.members')
            ->assertNotDispatched('toast-trigger');
    }

    public function testUserCannotCreateTeamWithMembersAlreadyInTeams(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);
        Team::factory()->withMembers($users)->create(['tournament_id' => $tournament->id]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertHasErrors('createForm.members')
            ->assertNotDispatched('toast-trigger');
    }

    public function testUserCannotCreateTeamWithMembersNotInTournament(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertHasErrors('createForm.members')
            ->assertNotDispatched('toast-trigger');
    }

    public function testUserCannotCreateTeamIfTeamGenerationIsInProgress(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);

        Cache::lock($tournament->getTeamsLockKey(), 20)->get();

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertConflict()
            ->assertNotDispatched('toast-trigger');
    }

    public function testUserCannotCreateTeamInTournamentTheyDoNotOrganize(): void
    {
        $user = User::factory()->create();
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($user)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');
    }

    public function testUserCannotCreateTeamIfTournamentHasAllTeams(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(4)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
            'number_of_players' => 4,
        ]);

        Team::factory()->withMembers($users->slice(0, 2))->create(['tournament_id' => $tournament->id]);
        Team::factory()->withMembers($users->slice(2, 2))->create(['tournament_id' => $tournament->id]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertForbidden()
            ->assertNotDispatched('toast-trigger');
    }
}
