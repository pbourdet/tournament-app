<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Livewire\Tournament\Organize\Teams;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Teams::class, ['tournament' => Tournament::factory()->teamBased()->create()])
            ->assertStatus(200);
    }

    public function testRendersForNonTeamBasedTournament(): void
    {
        Livewire::test(Teams::class, ['tournament' => Tournament::factory()->create()])
            ->assertSuccessful()
            ->assertSee(__('Your tournament is not played in teams. You can change this setting in the "General" tab.'));
    }
}
