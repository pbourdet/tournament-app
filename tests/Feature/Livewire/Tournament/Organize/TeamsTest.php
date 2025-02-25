<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Jobs\GenerateTeams;
use App\Livewire\Tournament\Organize\Teams;
use App\Models\Team;
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

    public function testOrganizerCanRemovePlayerFromTeam(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('removeMember', $team, $team->members->first()->id)
            ->assertSuccessful();

        $this->assertFalse($team->refresh()->isFull());
    }

    public function testNonOrganizerCantRemovePlayerFromTeam(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->withFullTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Livewire::actingAs($user)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('removeMember', $team, $team->members->first()->id)
            ->assertForbidden()
            ->assertNotDispatched('toast-show');

        $this->assertTrue($team->refresh()->isFull());
    }

    public function testOrganizerCantRemovePlayerForOtherTournament(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();
        $otherTournament = Tournament::factory()->withFullTeams()->create();
        $team = $otherTournament->teams->firstOrFail();
        $player = $team->members->first();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('removeMember', $team, $player->id)
            ->assertForbidden();

        $this->assertTrue($team->refresh()->isFull());
    }

    public function testOrganizerCantRemovePlayerFromTeamIfGenerationIsOngoing(): void
    {
        $tournament = Tournament::factory()->withFullTeams()->create();
        $team = $tournament->teams->firstOrFail();

        Cache::lock($tournament->getLockKey(), 20)->get();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('removeMember', $team, $team->members->first()->id)
            ->assertConflict()
            ->assertNotDispatched('toast-show');

        $this->assertTrue($team->refresh()->isFull());
    }

    public function testOrganizerCanAddPlayerToTeam(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->create();
        $team = $tournament->teams->firstOrFail();
        $player = $tournament->players->firstOrFail();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('addMember', $team, $player->id)
            ->assertSuccessful();

        $this->assertTrue($team->refresh()->members->contains($player));
    }

    public function testNonOrganizerCantAddPlayerToTeam(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->full()->teamBased()->create();
        $team = $tournament->teams->firstOrFail();
        $player = $tournament->players->firstOrFail();

        Livewire::actingAs($user)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('addMember', $team, $player->id)
            ->assertForbidden();

        $this->assertFalse($team->refresh()->members->contains($player));
    }

    public function testOrganizerCannotAddPlayerInFullTeam(): void
    {
        $users = User::factory(3)->create();
        $tournament = Tournament::factory()->full()->teamBased()->withPlayers($users)->create();
        $team = $tournament->teams->firstOrFail();
        $team->addMembers($users->take(2));
        $team->refresh();
        $player = $users->last();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('addMember', $team, $player->id)
            ->assertForbidden();

        $this->assertFalse($team->refresh()->members->contains($player));
    }

    public function testOrganizerCannotAddPlayerThatAreNotInTournament(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->create();
        $team = $tournament->teams->firstOrFail();
        $player = User::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('addMember', $team, $player->id)
            ->assertForbidden();

        $this->assertFalse($team->refresh()->members->contains($player));
    }

    public function testUserCannotAddUserToTeamNotInTournament(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->create();
        $otherTournament = Tournament::factory()->full()->teamBased()->create();
        $team = $otherTournament->teams->firstOrFail();
        $player = $tournament->players->firstOrFail();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('addMember', $team, $player->id)
            ->assertForbidden();

        $this->assertTrue($tournament->teams->every(fn (Team $team) => $team->members->isEmpty()));
    }

    public function testOrganizerCannotAddMemberToTeamIfPlayerAlreadyHasATeam(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->create();
        $team = $tournament->teams->firstOrFail();
        $player = $tournament->players->firstOrFail();
        $team->addMember($player);

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('addMember', $team, $player->id)
            ->assertForbidden();

        $this->assertCount(1, $team->refresh()->members);
    }

    public function testOrganizerCannotAddPlayerToTeamIfGenerationIsOngoing(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->create();
        $team = $tournament->teams->firstOrFail();
        $player = $tournament->players->firstOrFail();

        Cache::lock($tournament->getLockKey(), 20)->get();

        Livewire::actingAs($tournament->organizer)
            ->test(Teams::class, ['tournament' => $tournament])
            ->call('addMember', $team, $player->id)
            ->assertConflict();

        $this->assertFalse($team->refresh()->members->contains($player));
    }
}
