<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Jobs\GenerateGroups;
use App\Livewire\Tournament\Organize\Groups;
use App\Models\GroupPhase;
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

    public function testRendersSuccessfullyWithoutGroupPhase(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->assertStatus(200)
            ->assertSee(__('Group phase has not been set up yet.'))
        ;
    }

    public function testRendersSuccessfullyWithGroupPhase(): void
    {
        $tournament = Tournament::factory()->withGroupPhase()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->assertStatus(200)
            ->assertSee(__('The matches will be displayed once the group phase has started.'))
        ;
    }

    public function testRendersSuccessfullyWithStartedTournament(): void
    {
        $tournament = Tournament::factory()->full()->create();
        GroupPhase::factory()->for($tournament)->withMatches()->create();
        $tournament->refresh();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->assertStatus(200)
            ->assertSeeLivewire('tournament.match-card')
        ;
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
        $group->addContestant($contestant);

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertCount(1, $group->getContestants());
    }

    public function testOrganizerCannotAddContestantNotFromTournament(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first();
        $contestant = User::factory()->create();

        $this->expectException(ItemNotFoundException::class);

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id);

        $group = $group->refresh();
        $this->assertCount(0, $group->getContestants());
    }

    public function testOrganizerCannotAddContestantToGroupIfTournamentStarted(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $tournament->start();
        $group = $tournament->groupPhase->groups->first();
        $contestant = $tournament->contestants()->first();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertCount(0, $group->getContestants());
    }

    public function testOrganizerCannotAddToGroupNotFromTournament(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $otherTournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $otherTournament->groupPhase->groups->first();
        $contestant = $tournament->contestants()->first();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('addContestant', $group, $contestant->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertCount(0, $group->getContestants());
    }

    public function testOrganizerCannotAddContestantToFullGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first();
        $group->addContestants($tournament->contestants()->take($group->size));
        $group->refresh();

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
        $group->refresh();
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
        $group->refresh();
        $contestant = $group->getContestants()->first();

        Livewire::actingAs($tournament->players->first())
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('removeContestant', $group, $contestant->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertTrue($group->getContestants()->contains($contestant));
    }

    public function testOrganizerCannotRemoveContestantWhenTournamentStarted(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $tournament->start();
        $group = $tournament->groupPhase->groups->first();
        $group->addContestants($tournament->contestants()->take($group->size));
        $group->refresh();
        $contestant = $group->getContestants()->first();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('removeContestant', $group, $contestant->id)
            ->assertForbidden();

        $group = $group->refresh();
        $this->assertTrue($group->getContestants()->contains($contestant));
    }

    public function testOrganizerCannotRemoveContestantNotInTheGroup(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $tournament->groupPhase->groups->first();
        $group->addContestants($tournament->contestants()->take($group->size));
        $contestant = $tournament->contestantsWithoutGroup()->first();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('removeContestant', $group, $contestant->id)
            ->assertForbidden();
    }

    public function testOrganizerCannotRemoveContestantFromGroupNotInTournament(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $otherTournament = Tournament::factory()->full()->withGroupPhase()->create();
        $group = $otherTournament->groupPhase->groups->first();
        $group->addContestants($otherTournament->contestants()->take($group->size));
        $group->refresh();
        $contestant = $group->getContestants()->first();

        $this->expectException(ItemNotFoundException::class);

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('removeContestant', $group, $contestant->id);
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

    public function testOrganizerCanDeleteGroupPhase(): void
    {
        $tournament = Tournament::factory()->withGroupPhase()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('deletePhase', $tournament->groupPhase->id)
            ->assertSuccessful();

        $this->assertDatabaseCount('group_phases', 0);
    }

    public function testNonOrganizerCantDeleteGroupPhase(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();

        Livewire::actingAs($tournament->players->first())
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('deletePhase', $tournament->groupPhase->id)
            ->assertForbidden();

        $this->assertDatabaseCount('group_phases', 1);
    }

    public function testOrganizerCantDeletePhaseFromOtherTournament(): void
    {
        $tournament = Tournament::factory()->withGroupPhase()->create();
        $otherTournament = Tournament::factory()->withGroupPhase()->create();

        $this->expectException(ItemNotFoundException::class);

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('deletePhase', $otherTournament->groupPhase->id);

        $this->assertDatabaseCount('group_phases', 1);
    }

    public function testOrganizerCannotDeletePhaseIfTournamentStarted(): void
    {
        $tournament = Tournament::factory()->withGroupPhase()->create();
        $tournament->start();

        Livewire::actingAs($tournament->organizer)
            ->test(Groups::class, ['tournament' => $tournament])
            ->call('deletePhase', $tournament->groupPhase->id)
            ->assertForbidden();

        $this->assertDatabaseCount('group_phases', 1);
    }
}
