<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Enums\TournamentStatus;
use App\Livewire\Tournament\Organize\Elimination;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\ItemNotFoundException;
use Livewire\Livewire;
use Tests\TestCase;

class EliminationTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersWithoutAnEliminationPhase(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->assertSee(__('How many :contestants will compete in this phase ?', ['contestants' => 'players']))
            ->assertStatus(200);
    }

    public function testRendersWithAnEliminationPhase(): void
    {
        $tournament = Tournament::factory()->withEliminationPhase()->create();

        Livewire::test(Elimination::class, ['tournament' => $tournament])
            ->assertDontSee(__('How many :contestants will compete in this phase ?', ['contestants' => 'players']))
            ->assertStatus(200);
    }

    public function testUserCanCreateAnEliminationPhase(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 8)
            ->call('create')
            ->assertDispatched('toast-show')
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

    public function testOrganizerCantCreateEliminationIfTournamentStarted(): void
    {
        $tournament = Tournament::factory()->withGroupPhase()->create();
        $tournament->start();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 8)
            ->call('create')
            ->assertForbidden();
    }

    public function testUserCantCreateAnEliminationPhaseIfAlreadyExists(): void
    {
        $tournament = Tournament::factory()->withEliminationPhase()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->set('form.numberOfContestants', 2)
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

    public function testUserCanDeleteAnEliminationPhase(): void
    {
        $tournament = Tournament::factory()->withEliminationPhase()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->call('deletePhase', $tournament->eliminationPhase->id)
            ->assertSuccessful();

        $this->assertDatabaseCount('elimination_phases', 0);
    }

    public function testNonOrganizerCantDeleteAnEliminationPhase(): void
    {
        $tournament = Tournament::factory()->full()->withEliminationPhase()->create();

        Livewire::actingAs($tournament->players->first())
            ->test(Elimination::class, ['tournament' => $tournament])
            ->call('deletePhase', $tournament->eliminationPhase->id)
            ->assertForbidden();

        $this->assertDatabaseCount('elimination_phases', 1);
    }

    public function testOrganizerCannotDeleteEliminationPhaseFromOtherTournament(): void
    {
        $tournament = Tournament::factory()->withEliminationPhase()->create();
        $otherTournament = Tournament::factory()->withEliminationPhase()->create();

        $this->expectException(ItemNotFoundException::class);
        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->call('deletePhase', $otherTournament->eliminationPhase->id)
            ->assertForbidden();

        $this->assertDatabaseCount('elimination_phases', 1);
    }

    public function testUserCantDeleteAnEliminationPhaseIfTournamentStarted(): void
    {
        $tournament = Tournament::factory()->withEliminationPhase()->create();
        $tournament->start();

        Livewire::actingAs($tournament->organizer)
            ->test(Elimination::class, ['tournament' => $tournament])
            ->call('deletePhase', $tournament->eliminationPhase->id)
            ->assertForbidden();

        $this->assertDatabaseCount('elimination_phases', 1);
    }
}
