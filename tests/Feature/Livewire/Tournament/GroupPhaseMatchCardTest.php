<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Enums\ResultOutcome;
use App\Enums\TournamentStatus;
use App\Jobs\GenerateMatches;
use App\Livewire\Tournament\MatchCard;
use App\Models\GroupPhase;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;
use Tests\TestCase;

class GroupPhaseMatchCardTest extends TestCase
{
    use RefreshDatabase;

    public function testTournamentDoesNotEndIfGroupPhaseIsNotFinished(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 4]);
        $phase = GroupPhase::factory()->forTournament($tournament)->withFullGroups()->withMatches()->create();
        $tournament->start();

        $matches = $phase->rounds->first()->matches;
        $firstMatch = $matches->first();

        Livewire::actingAs($tournament->organizer)
            ->test(MatchCard::class, ['match' => $firstMatch])
            ->set(sprintf('contestants.%s.outcome', $firstMatch->contestants[0]->contestant_id), ResultOutcome::WIN)
            ->set(sprintf('contestants.%s.outcome', $firstMatch->contestants[1]->contestant_id), ResultOutcome::LOSS)
            ->call('addResult')
            ->assertSuccessful();

        $this->assertSame(TournamentStatus::IN_PROGRESS, $tournament->refresh()->status);
    }

    public function testTournamentFinishesIfGroupPhaseEndsWithoutEliminationPhase(): void
    {
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 4]);
        $phase = GroupPhase::factory()->forTournament($tournament)->withFullGroups()->withMatches()->create();
        $tournament->start();

        $matches = $phase->rounds->first()->matches;
        $firstMatch = $matches->first();

        Livewire::actingAs($tournament->organizer)
            ->test(MatchCard::class, ['match' => $firstMatch])
            ->set(sprintf('contestants.%s.outcome', $firstMatch->contestants[0]->contestant_id), ResultOutcome::WIN)
            ->set(sprintf('contestants.%s.outcome', $firstMatch->contestants[1]->contestant_id), ResultOutcome::LOSS)
            ->call('addResult')
            ->assertSuccessful();

        $secondMatch = $matches->last();

        Livewire::actingAs($tournament->organizer)
            ->test(MatchCard::class, ['match' => $secondMatch])
            ->set(sprintf('contestants.%s.outcome', $secondMatch->contestants[0]->contestant_id), ResultOutcome::WIN)
            ->set(sprintf('contestants.%s.outcome', $secondMatch->contestants[1]->contestant_id), ResultOutcome::LOSS)
            ->call('addResult')
            ->assertSuccessful();

        $this->assertSame(TournamentStatus::FINISHED, $tournament->refresh()->status);
    }

    public function testEliminationMatchesAreGeneratedAfterGroupPhaseEnds(): void
    {
        $tournament = Tournament::factory()->full()->withEliminationPhase()->create(['number_of_players' => 4]);
        $phase = GroupPhase::factory()->forTournament($tournament)->withFullGroups()->withMatches()->create();
        $tournament->start();

        $matches = $phase->rounds->first()->matches;
        $firstMatch = $matches->first();

        Queue::fake();

        Livewire::actingAs($tournament->organizer)
            ->test(MatchCard::class, ['match' => $firstMatch])
            ->set(sprintf('contestants.%s.outcome', $firstMatch->contestants[0]->contestant_id), ResultOutcome::WIN)
            ->set(sprintf('contestants.%s.outcome', $firstMatch->contestants[1]->contestant_id), ResultOutcome::LOSS)
            ->call('addResult')
            ->assertSuccessful();

        $secondMatch = $matches->last();

        Livewire::actingAs($tournament->organizer)
            ->test(MatchCard::class, ['match' => $secondMatch])
            ->set(sprintf('contestants.%s.outcome', $secondMatch->contestants[0]->contestant_id), ResultOutcome::WIN)
            ->set(sprintf('contestants.%s.outcome', $secondMatch->contestants[1]->contestant_id), ResultOutcome::LOSS)
            ->call('addResult')
            ->assertSuccessful();

        Queue::assertPushed(GenerateMatches::class);
    }
}
