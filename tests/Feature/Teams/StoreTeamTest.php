<?php

declare(strict_types=1);

namespace Tests\Feature\Teams;

use App\Enums\ToastType;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class StoreTeamTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateATeam(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(4)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'members' => $users->slice(0, 2)->pluck('id')->toArray(),
        ]);
        $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'name' => 'team name',
            'members' => $users->slice(2, 2)->pluck('id')->toArray(),
        ]);

        $this->assertCount(2, $tournament->teams);
        $this->assertDatabaseCount('team_user', 4);
        $this->assertDatabaseCount('teams', 2);
        $this->assertDatabaseHas('teams', ['name' => 'team name']);
        $response->assertRedirectToRoute('dashboard');
        $response->assertSessionHas(ToastType::SUCCESS->value, 'Team team name created');
    }

    public function testUserCannotCreateTeamInNotTeamBasedTournament(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'members' => $users->pluck('id')->toArray(),
        ]);

        $this->assertCount(0, $tournament->teams);
        $this->assertDatabaseCount('team_user', 0);
        $this->assertDatabaseCount('teams', 0);
        $response->assertForbidden();
    }

    public function testUserCannotCreateATeamWithNonExistingUsers(): void
    {
        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'name' => 'team name',
            'members' => [Uuid::uuid4()->toString(), Uuid::uuid4()->toString()],
        ]);

        $this->assertCount(0, $tournament->teams);
        $this->assertDatabaseCount('team_user', 0);
        $this->assertDatabaseCount('teams', 0);
        $response->assertSessionHasErrors('members.1');
    }

    public function testUserCannotCreateTeamWithInvalidNumberOfMembers(): void
    {
        $organizer = User::factory()->create();
        $user = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'name' => 'team name',
            'members' => [$user->id],
        ]);

        $this->assertCount(0, $tournament->teams);
        $this->assertDatabaseCount('team_user', 0);
        $this->assertDatabaseCount('teams', 0);
        $response->assertSessionHasErrors('members');
    }

    public function testUserCannotCreateTeamWithMembersAlreadyInTeams(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);
        Team::factory()->withMembers($users)->create(['tournament_id' => $tournament->id]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'name' => 'team name',
            'members' => $users->pluck('id')->toArray(),
        ]);

        $this->assertCount(1, $tournament->teams);
        $this->assertDatabaseCount('team_user', 2);
        $this->assertDatabaseCount('teams', 1);
        $response->assertSessionHasErrors('members');
    }

    public function testUserCannotCreateTeamWithMembersNotInTournament(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'name' => 'team name',
            'members' => $users->pluck('id')->toArray(),
        ]);

        $this->assertCount(0, $tournament->teams);
        $this->assertDatabaseCount('team_user', 0);
        $this->assertDatabaseCount('teams', 0);
        $response->assertSessionHasErrors('members');
    }

    public function testUserCannotCreateTeamIfTeamGenerationIsInProgress(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);

        Cache::lock(sprintf('tournament:%s:generate-teams', $tournament->id), 20)->get();

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'name' => 'team name',
            'members' => $users->pluck('id')->toArray(),
        ]);

        $this->assertCount(0, $tournament->teams);
        $this->assertDatabaseCount('team_user', 0);
        $this->assertDatabaseCount('teams', 0);
        $response->assertConflict();
    }

    public function testUserCannotCreateTeamInTournamentTheyDoNotOrganize(): void
    {
        $user = User::factory()->create();
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($user)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'name' => 'team name',
            'members' => $users->pluck('id')->toArray(),
        ]);

        $this->assertCount(0, $tournament->teams);
        $this->assertDatabaseCount('team_user', 0);
        $this->assertDatabaseCount('teams', 0);
        $response->assertForbidden();
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

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'members' => [$users[0]->id, $users[1]->id],
        ]);

        $this->assertCount(2, $tournament->teams);
        $this->assertDatabaseCount('team_user', 4);
        $this->assertDatabaseCount('teams', 2);
        $response->assertForbidden();
    }
}
