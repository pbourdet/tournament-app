<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Enums\ResultOutcome;
use App\Enums\TournamentStatus;
use App\Livewire\Tournament\MatchCard;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EliminationMatchCardTest extends TestCase
{
    use RefreshDatabase;

    private Tournament $tournament;

    public function rendersSuccessfully(): void
    {
        Livewire::test(MatchCard::class)
            ->assertStatus(200);
    }

    public function setUp(): void
    {
        parent::setUp();
        $this->tournament = Tournament::factory()->started()->create(['number_of_players' => 4]);
    }

    public function testRendersSuccessfully(): void
    {
        Livewire::test(MatchCard::class, ['match' => $this->tournament->eliminationPhase->rounds->first()->matches->first()])
            ->assertStatus(200);
    }

    public function testOrganizerCanSubmitMatchResult(): void
    {
        $match = $this->tournament->eliminationPhase->rounds->first()->matches->first();
        $contestants = $match->contestants;

        Livewire::actingAs($this->tournament->organizer)
            ->test(MatchCard::class, ['match' => $match])
            ->set(sprintf('contestants.%s.outcome', $contestants[0]->contestant_id), ResultOutcome::WIN)
            ->set(sprintf('contestants.%s.outcome', $contestants[1]->contestant_id), ResultOutcome::LOSS)
            ->call('addResult')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        $this->assertDatabaseCount('results', 2);
        $this->assertDatabaseHas('results', ['outcome' => ResultOutcome::WIN]);
        $this->assertDatabaseHas('results', ['outcome' => ResultOutcome::LOSS]);
    }

    public function testTournamentEndsIfMatchIsAFinal(): void
    {
        $this->tournament = Tournament::factory()->started()->create(['number_of_players' => 2]);
        $finalMatch = $this->tournament->eliminationPhase->rounds->last()->matches->first();
        $contestants = $finalMatch->contestants;

        Livewire::actingAs($this->tournament->organizer)
            ->test(MatchCard::class, ['match' => $finalMatch])
            ->set(sprintf('contestants.%s.outcome', $contestants[0]->contestant_id), ResultOutcome::WIN)
            ->set(sprintf('contestants.%s.outcome', $contestants[1]->contestant_id), ResultOutcome::LOSS)
            ->call('addResult')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        $this->assertSame(TournamentStatus::FINISHED, $this->tournament->refresh()->status);
    }

    public function testNonOrganizerCantAddMatchResult(): void
    {
        $match = $this->tournament->eliminationPhase->rounds->first()->matches->first();

        Livewire::actingAs($this->tournament->players->first())
            ->test(MatchCard::class, ['match' => $match])
            ->call('addResult')
            ->assertForbidden();

        $this->assertDatabaseCount('results', 0);
    }

    public function testOrganizerCantAddResultIfTournamentIsNotStarted(): void
    {
        $this->tournament = Tournament::factory()->full()->withEliminationPhaseAndMatches()->create(['number_of_players' => 4]);

        $match = $this->tournament->eliminationPhase->rounds->first()->matches->first();

        Livewire::actingAs($this->tournament->organizer)
            ->test(MatchCard::class, ['match' => $match])
            ->call('addResult')
            ->assertForbidden();

        $this->assertDatabaseCount('results', 0);
    }
}
