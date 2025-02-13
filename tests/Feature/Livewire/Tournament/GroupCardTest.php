<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\GroupCard;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class GroupCardTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->withGroupPhase()->create();

        Livewire::test(GroupCard::class, ['tournament' => $tournament, 'group' => $tournament->groupPhase->groups->first()])
            ->assertStatus(200);
    }
}
