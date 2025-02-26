<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Enums\TournamentStatus;
use App\Jobs\StartTournament;
use App\Livewire\Tournament\Overview;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class OverviewTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Overview::class, ['tournament' => Tournament::factory()->create()])
            ->assertStatus(200);
    }

    public function testOrganizerCanStartTournament(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->full()->create();
        $tournament->eliminationPhase()->create(['number_of_contestants' => 2]);
        $tournament->update(['status' => TournamentStatus::READY_TO_START]);

        Livewire::actingAs($tournament->organizer)
            ->test(Overview::class, ['tournament' => $tournament])
            ->call('start')
            ->assertSuccessful()
            ->assertDispatched('toast-show')
            ->assertSet('locked', true);

        Queue::assertPushed(StartTournament::class);
    }

    public function testNonOrganizerCantStartTournament(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->full()->create();
        $tournament->eliminationPhase()->create(['number_of_contestants' => 2]);
        $tournament->update(['status' => TournamentStatus::READY_TO_START]);

        Livewire::actingAs($tournament->players->first())
            ->test(Overview::class, ['tournament' => $tournament])
            ->call('start')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');

        Queue::assertNotPushed(StartTournament::class);
    }

    public function testOrganizerCantStartTournamentIfNotReady(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Overview::class, ['tournament' => $tournament])
            ->call('start')
            ->assertForbidden()
            ->assertNotDispatched('toast-show');

        Queue::assertNotPushed(StartTournament::class);
    }

    public function testOrganizerCantStartTournamentIfGenerationAlreadyOngoing(): void
    {
        Queue::fake();
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 4]);
        $tournament->eliminationPhase()->create(['number_of_contestants' => 2]);
        $tournament->update(['status' => TournamentStatus::READY_TO_START]);

        Cache::lock($tournament->getLockKey(), 20)->get();

        Livewire::actingAs($tournament->organizer)
            ->test(Overview::class, ['tournament' => $tournament])
            ->call('start')
            ->assertConflict()
            ->assertNotDispatched('toast-show');

        Queue::assertNotPushed(StartTournament::class);
    }
}
