<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Jobs\GenerateGroups;
use App\Livewire\Tournament\Organize\Groups;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ItemNotFoundException;
use Livewire\Livewire;
use Tests\TestCase;

class GroupsTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
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

    public function testOrganizerCannotCreateGroupPhaseIfTournamentIsStarted(): void
    {
        $tournament = Tournament::factory()->started()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->set('form.numberOfGroups', 2)
            ->set('form.contestantsQualifying', 2)
            ->call('create')
            ->assertForbidden();

        $this->assertDatabaseCount('group_phases', 0);
    }

    public function testNonOrganizerCantCreateGroupPhase(): void
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

    public function testOrganizerCanRemoveContestantFromGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first();
        $group->addContestants($tournament->contestants()->take($group->size));
        $contestant = $group->getContestants()->first();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('removeContestant', $group, $contestant->id)
            ->assertSuccessful();

        $group = $group->refresh();
        $this->assertFalse($group->getContestants()->contains($contestant));
    }

    public function testNonOrganizerCantRemoveContestantFromGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first();
        $group->addContestants($tournament->contestants()->take($group->size));
        $contestant = $group->getContestants()->first();

        Livewire::actingAs($tournament->players->first())
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('removeContestant', $group, $contestant->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertTrue($group->getContestants()->contains($contestant));
    }

    public function testOrganizerCanGenerateGroups(): void
    {
        Queue::fake();

        $tournament = Tournament::factory()->full()->withGroupPhase()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('generateGroups')
            ->assertSuccessful();

        Queue::assertPushed(GenerateGroups::class);
    }

    public function testOrganizerCantGenerateGroupsIfTournamentStarted(): void
    {
        Queue::fake();

        $tournament = Tournament::factory()->withGroupPhase()->create();
        $tournament->start();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('generateGroups')
            ->assertForbidden();

        Queue::assertNotPushed(GenerateGroups::class);
    }

    public function testNonOrganizerCantGenerateGroups(): void
    {
        Queue::fake();

        $tournament = Tournament::factory()->full()->withGroupPhase()->create();

        Livewire::actingAs($tournament->players->first())
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('generateGroups')
            ->assertForbidden();

        Queue::assertNotPushed(GenerateGroups::class);
    }

    public function testOrganizerCantGenerateGroupWithoutGroupPhase(): void
    {
        Queue::fake();

        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('generateGroups')
            ->assertForbidden();

        Queue::assertNotPushed(GenerateGroups::class);
    }

    public function testOrganizerCantGenerateGroupWithFullGroups(): void
    {
        Queue::fake();

        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $tournament->groupPhase->groups->each(fn ($group) => $group->addContestants($tournament->contestants()->take($group->size)));

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('generateGroups')
            ->assertForbidden();

        Queue::assertNotPushed(GenerateGroups::class);
    }
}
