<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\PlayersTable;
use App\Models\Tournament;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class PlayersTableTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::test(PlayersTable::class, ['tournament' => $tournament])
            ->assertStatus(200);
    }

    public function testOrganizerCanRemoveAPlayer(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->withPlayers([$user])->create();

        Livewire::actingAs($tournament->organizer)
            ->test(PlayersTable::class, ['tournament' => $tournament])
            ->call('removePlayer', $user->id)
            ->assertDispatched('toast-trigger');

        $this->assertCount(0, $tournament->players);
    }

    public function testNonOrganizerCantRemovePlayer(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->withPlayers([$user])->create();

        Livewire::actingAs(User::factory()->create())
            ->test(PlayersTable::class, ['tournament' => $tournament])
            ->call('removePlayer', $user->id)
            ->assertForbidden();

        $this->assertCount(1, $tournament->players);
    }

    public function testOrganizerCantRemovePlayerIfGenerationInProgress(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->withPlayers([$user])->create();

        Livewire::actingAs($tournament->organizer)
            ->test(PlayersTable::class, ['tournament' => $tournament, 'generationInProgress' => true])
            ->call('removePlayer', $user->id)
            ->assertConflict();

        $this->assertCount(1, $tournament->players);
    }
}
