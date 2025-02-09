<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Livewire\Tournament\Organize\Groups;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GroupsTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Groups::class, ['tournament' => Tournament::factory()->create()])
            ->assertStatus(200);
    }

    public function testOrganizerCanCreateAGroupPhase(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 8]);

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->set('form.numberOfGroups', 2)
            ->set('form.contestantsQualifying', 2)
            ->call('create')
            ->assertSuccessful();

        $tournament = $tournament->refresh();

        $this->assertNotNull($tournament->groupPhase);
        $this->assertSame(2, $tournament->groupPhase->number_of_groups);
        $this->assertSame(2, $tournament->groupPhase->qualifying_per_group);
    }

    public function testContestantsAreSplitBetweenGroupsUponGroupPhaseCreation(): void
    {
        $users = User::factory(7)->create();
        $tournament = Tournament::factory()->withPlayers($users)->create(['number_of_players' => 16]);

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->set('form.numberOfGroups', 4)
            ->set('form.contestantsQualifying', 2)
            ->call('create')
            ->assertSuccessful();

        $tournament = $tournament->refresh();

        $this->assertDatabaseCount('groups', 4);
    }

    public function testNonOrganizerCantCreateQualificationPhase(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 8]);

        Livewire::actingAs($tournament->players->first())
            ->test(Groups::class, ['tournament' => $tournament])
            ->set('form.numberOfGroups', 2)
            ->set('form.contestantsQualifying', 2)
            ->call('create')
            ->assertForbidden();

        $tournament = $tournament->refresh();

        $this->assertNull($tournament->groupPhase);
    }
}
