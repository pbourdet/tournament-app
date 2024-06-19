<?php

declare(strict_types=1);

namespace Tests\Feature\Teams;

use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class StoreTeamTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanCreateATeam(): void
    {
        $organizer = User::factory()->create();
        $users = User::factory(2)->create();

        $tournament = Tournament::factory()->teamBased()->withPlayers($users)->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.store', ['tournament' => $tournament]), [
            'name' => 'team name',
            'members' => $users->pluck('id')->toArray(),
        ]);

        $this->assertCount(1, $tournament->teams);
        $this->assertDatabaseCount('team_user', 2);
        $this->assertDatabaseCount('teams', 1);
        $response->assertRedirectToRoute('dashboard');
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
}
