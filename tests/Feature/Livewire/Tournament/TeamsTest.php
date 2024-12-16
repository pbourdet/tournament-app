<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\Teams;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

        Livewire::test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->assertStatus(200);
    }

    public function testOrganizerCanDeleteATeam(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->call('delete', $team)
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }

    public function testNonOrganizerCantDeleteTeams(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->withAllTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Livewire::actingAs($user)
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->call('delete', $team)
            ->assertForbidden()
            ->assertNotDispatched('toast-show');

        $this->assertDatabaseHas('teams', ['id' => $team->id]);
    }

    public function testOrganizerCantDeleteTeamIfGenerationIsOngoing(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => true])
            ->call('delete', $team)
            ->assertConflict()
            ->assertNotDispatched('toast-show');

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
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', [$users[0]->id, $users[1]->id])
            ->call('create')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

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
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');
    }

    public function testUserCannotCreateATeamWithNonExistingUsers(): void
    {
        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', [Uuid::uuid4()->toString(), Uuid::uuid4()->toString()])
            ->call('create')
            ->assertHasErrors('createForm.members.*')
            ->assertNotDispatched('toast-show');
    }

    public function testUserCannotCreateTeamWithInvalidNumberOfMembers(): void
    {
        $organizer = User::factory()->create();
        $user = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', [$user->id])
            ->call('create')
            ->assertHasErrors('createForm.members')
            ->assertNotDispatched('toast-show');
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
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertHasErrors('createForm.members')
            ->assertNotDispatched('toast-show');
    }

    public function testUserCannotCreateTeamWithMembersNotInTournament(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertHasErrors('createForm.members')
            ->assertNotDispatched('toast-show');
    }

    public function testUserCannotCreateTeamIfTeamGenerationIsInProgress(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);

        Livewire::actingAs($organizer)
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => true])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertConflict()
            ->assertNotDispatched('toast-show');
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
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');
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
            ->test(Teams::class, ['tournament' => $tournament, 'generationInProgress' => false])
            ->set('createForm.name', 'team name')
            ->set('createForm.members', $users->pluck('id')->toArray())
            ->call('create')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');
    }
}
