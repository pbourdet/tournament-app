<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Phase;

use App\Enums\TournamentStatus;
use App\Livewire\Tournament\Phase\Elimination;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EliminationTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::test(Elimination::class, ['tournament' => $tournament])
            ->assertStatus(200);
    }

    public function testUserCanCreateAnEliminationPhase(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 8)
            ->call('create')
            ->assertDispatched('toast-trigger')
            ->assertSuccessful();

        $this->assertDatabaseCount('elimination_phases', 1);
    }

    public function testTournamentStatusIsUpdatedIfFullAfterCreation(): void
    {
        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 8)
            ->call('create');

        $this->assertSame(TournamentStatus::READY_TO_START, $tournament->fresh()->status);
    }

    public function testTournamentStatusIsNotUpdatedIfNotFullAfterCreation(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 8)
            ->call('create');

        $this->assertSame(TournamentStatus::WAITING_FOR_PLAYERS, $tournament->fresh()->status);
    }

    public function testUserCantCreateAnEliminationPhaseWithInvalidValues(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 7)
            ->call('create')
            ->assertHasErrors(['form.numberOfContestants']);

        $this->assertDatabaseCount('elimination_phases', 0);
    }

    public function testUserCantCreateAnEliminationPhaseIfAlreadyExists(): void
    {
        $tournament = Tournament::factory()->create();
        $tournament->eliminationPhase()->create(['number_of_contestants' => 8]);

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 8)
            ->call('create')
            ->assertForbidden();

        $this->assertDatabaseCount('elimination_phases', 1);
    }

    public function testUserCantCreateAnEliminationPhaseIfNotOrganizer(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs(User::factory()->create())
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 8)
            ->call('create')
            ->assertForbidden();

        $this->assertDatabaseCount('elimination_phases', 0);
    }
}
