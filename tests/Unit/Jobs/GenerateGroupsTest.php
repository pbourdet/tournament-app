<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Events\TournamentUpdated;
use App\Jobs\GenerateGroups;
use App\Models\Group;
use App\Models\Tournament;
use App\Notifications\GroupsGenerated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class GenerateGroupsTest extends TestCase
{
    use RefreshDatabase;

    public function testItGeneratesGroups(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create(['number_of_players' => 8]);

        Notification::fake();
        Event::fake();

        new GenerateGroups($tournament->groupPhase)->handle();

        $tournament = $tournament->refresh();
        $this->assertTrue($tournament->groupPhase->groups->every(fn (Group $group) => $group->isFull()));
        $this->assertDatabaseCount('group_contestant', 8);
        Notification::assertSentTo($tournament->organizer, GroupsGenerated::class);
        Event::assertDispatched(TournamentUpdated::class);
    }

    public function testItDoesNothingIfGroupsAreFull(): void
    {
        $tournament = Tournament::factory()->full()->withGroupPhase()->create();
        $tournament->groupPhase->groups->each(fn ($group) => $group->addContestants($tournament->contestants()->take($group->size)));

        Notification::fake();
        Event::fake();

        new GenerateGroups($tournament->groupPhase->refresh())->handle();

        $tournament = $tournament->refresh();
        $this->assertTrue($tournament->groupPhase->groups->every(fn (Group $group) => $group->isFull()));
        Notification::assertNothingSent();
        Event::assertNotDispatched(TournamentUpdated::class);
    }
}
