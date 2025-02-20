<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Events\TournamentUpdated;
use App\Livewire\Tournament\PlayersTable;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class PlayersTableTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::test(PlayersTable::class, ['tournament' => $tournament])
            ->assertStatus(200);
    }

    public function testOrganizerCanRemoveAPlayer(): void
    {
        Event::fake();

        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(PlayersTable::class, ['tournament' => $tournament])
            ->call('remove', $tournament->players->first())
            ->assertDispatched('toast-show');

        $this->assertFalse($tournament->isFull());
        Event::assertDispatched(TournamentUpdated::class);
    }

    public function testNonOrganizerCannotRemoveAPlayer(): void
    {
        Event::fake();

        $tournament = Tournament::factory()->full()->create();
        $user = $tournament->players->first();

        Livewire::actingAs($user)
            ->test(PlayersTable::class, ['tournament' => $tournament])
            ->call('remove', $user)
            ->assertForbidden();

        $this->assertTrue($tournament->isFull());
        Event::assertNotDispatched(TournamentUpdated::class);
    }

    public function testOrganizerCantRemoveAPlayerWhenTournamentIsStarted(): void
    {
        $tournament = Tournament::factory()->started()->create();
        $user = $tournament->players->first();

        Event::fake();

        Livewire::actingAs($tournament->organizer)
            ->test(PlayersTable::class, ['tournament' => $tournament])
            ->call('remove', $user)
            ->assertForbidden();

        $this->assertTrue($tournament->isFull());
        $this->assertTrue($tournament->players->contains($user));
        Event::assertNotDispatched(TournamentUpdated::class);
    }

    public function testOrganizerCantRemovePlayerIfNotInTournament(): void
    {
        $tournament = Tournament::factory()->full()->create();
        $user = User::factory()->create();

        Event::fake();

        Livewire::actingAs($tournament->organizer)
            ->test(PlayersTable::class, ['tournament' => $tournament])
            ->call('remove', $user)
            ->assertForbidden();

        Event::assertNotDispatched(TournamentUpdated::class);
    }

    public function testOrganizerCannotRemoveAPlayerWhenTournamentIsLocked(): void
    {
        Event::fake();

        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(PlayersTable::class, ['tournament' => $tournament, 'locked' => true])
            ->call('remove', $tournament->players->first())
            ->assertStatus(409);

        $this->assertTrue($tournament->isFull());
        Event::assertNotDispatched(TournamentUpdated::class);
    }
}
