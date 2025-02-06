<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Phase;

use App\Livewire\Tournament\Phase\Qualification;
use App\Models\GroupPhase;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class QualificationTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfullyWithNoGroupPhase(): void
    {
        Livewire::test(Qualification::class, ['tournament' => Tournament::factory()->create()])
            ->assertSee(__('Group phase has not been set up yet.'))
            ->assertStatus(200);
    }

    public function testRendersWithEmptyGroups(): void
    {
        $tournament = Tournament::factory()->withGroupPhase()->create();

        Livewire::test(Qualification::class, ['tournament' => $tournament])
            ->assertSee(__('No :contestants in this group.', ['contestants' => 'players']))
            ->assertStatus(200);
    }

    public function testRendersWithGroups(): void
    {
        $tournament = Tournament::factory()->create();
        GroupPhase::factory()->forTournament($tournament)->withGroups()->create();

        Livewire::test(Qualification::class, ['tournament' => $tournament])
            ->assertSee(__('Group 1'))
            ->assertSee(__('Group 2'))
            ->assertStatus(200);
    }
}
