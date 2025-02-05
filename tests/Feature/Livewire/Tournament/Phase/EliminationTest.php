<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Phase;

use App\Livewire\Tournament\MatchCard;
use App\Livewire\Tournament\Phase\Elimination;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EliminationTest extends TestCase
{
    use RefreshDatabase;

    public function testItRendersWithoutEliminationPhase(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::test(Elimination::class, ['tournament' => $tournament])
            ->assertSee(__('No elimination phase has yet been set up for this tournament.'));
    }

    public function testItRendersWithAnEliminationPhase(): void
    {
        $tournament = Tournament::factory()->withEliminationPhase()->create();

        Livewire::test(Elimination::class, ['tournament' => $tournament])
            ->assertSee(__('The matches will be displayed once the elimination phase has started.'));
    }

    public function testItRendersWithAStartedEliminationPhase(): void
    {
        $tournament = Tournament::factory()->started()->create();

        Livewire::test(Elimination::class, ['tournament' => $tournament])
            ->assertSeeLivewire(MatchCard::class);
    }
}
