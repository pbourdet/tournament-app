<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Phase;

use App\Enums\PhaseType;
use App\Livewire\Tournament\Phase\Qualification;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QualificationTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Qualification::class, ['tournament' => Tournament::factory()->create()])
            ->assertStatus(200);
    }

    public function testOrganizerCanCreateAGroupPhase(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 8]);

        Livewire::actingAs($tournament->organizer)
            ->test(Qualification::class, ['tournament' => $tournament])
            ->set('groupForm.numberOfGroups', 2)
            ->set('groupForm.contestantsQualifying', 2)
            ->call('create')
            ->assertSuccessful();

        $tournament = $tournament->refresh();

        $this->assertNotNull($tournament->qualificationPhase);
        $this->assertSame(2, $tournament->qualificationPhase->configuration->numberOfGroups);
        $this->assertSame(2, $tournament->qualificationPhase->configuration->contestantsQualifying);
        $this->assertDatabaseCount('group_contestant', 8);
        $this->assertSame(PhaseType::GROUP, $tournament->qualificationPhase->type);
    }

    public function testContestantsAreSplitBetweenGroupsUponGroupPhaseCreation(): void
    {
        $users = User::factory(7)->create();
        $tournament = Tournament::factory()->withPlayers($users)->create(['number_of_players' => 16]);

        Livewire::actingAs($tournament->organizer)
            ->test(Qualification::class, ['tournament' => $tournament])
            ->set('groupForm.numberOfGroups', 4)
            ->set('groupForm.contestantsQualifying', 2)
            ->call('create')
            ->assertSuccessful();

        $tournament = $tournament->refresh();

        $this->assertDatabaseCount('groups', 4);
        $this->assertDatabaseCount('group_contestant', 7);
        foreach ($tournament->qualificationPhase->groups as $group) {
            $this->assertLessThanOrEqual(2, $group->contestants->count());
        }
    }

    public function testNonOrganizerCantCreateQualificationPhase(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 8]);

        Livewire::actingAs($tournament->players->first())
            ->test(Qualification::class, ['tournament' => $tournament])
            ->set('groupForm.numberOfGroups', 2)
            ->set('groupForm.contestantsQualifying', 2)
            ->call('create')
            ->assertForbidden();

        $tournament = $tournament->refresh();

        $this->assertNull($tournament->qualificationPhase);
    }
}
