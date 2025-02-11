<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Livewire\Tournament\Organize\Groups;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ItemNotFoundException;
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

    public function testOrganizerCanAddPlayerToGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();

        $group = $tournament->groupPhase->groups->first();
        $contestant = $tournament->contestants()->first();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id)
            ->assertSuccessful();

        $group = $group->refresh();
        $this->assertTrue($group->getContestants()->contains($contestant));
        $this->assertInstanceOf(User::class, $group->getContestants()->first());
    }

    public function testOrganizerCanAddTeamToGroup(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->withGroupPhase()->create();

        $group = $tournament->groupPhase->groups->first();
        $contestant = $tournament->contestants()->first();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id)
            ->assertSuccessful();

        $group = $group->refresh();
        $this->assertTrue($group->getContestants()->contains($contestant));
        $this->assertInstanceOf(Team::class, $group->getContestants()->first());
    }

    public function testOrganizerCannotAddWrongContestantTypeToGroup(): void
    {
        $tournament = Tournament::factory()->full()->teamBased()->withGroupPhase()->create();

        $group = $tournament->groupPhase->groups->first();
        $contestant = $tournament->players->first();

        $this->expectException(ItemNotFoundException::class);
        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id);

        $group = $group->refresh();
        $this->assertFalse($group->getContestants()->contains($contestant));
    }

    public function testOrganizerCannotAddContestantAlreadyInAGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first();
        $contestant = $tournament->contestants()->first();
        $group->addContestants([$contestant]);

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertCount(1, $group->getContestants());
    }

    public function testOrganizerCannotAddContestantToFullGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first();
        $group->addContestants($tournament->contestants()->take($group->size));

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $tournament->contestants()->last()->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertCount($group->size, $group->getContestants());
    }

    public function testNonOrganizerCantAddContestantToGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first();
        $contestant = $tournament->contestants()->first();

        Livewire::actingAs($tournament->players->first())
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertFalse($group->getContestants()->contains($contestant));
    }
}
