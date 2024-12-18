<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\Show;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewTournamentAsOrganizer(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create(['organizer_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Show::class, ['tournament' => $tournament])
            ->assertStatus(200)
            ->assertSee($tournament->name);
    }

    public function testUserCanViewTournamentAsPlayer(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();
        $tournament->players()->attach($user);

        Livewire::actingAs($user)
            ->test(Show::class, ['tournament' => $tournament])
            ->assertStatus(200)
            ->assertSee($tournament->name);
    }

    public function testUserCannotViewTournament(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($user)
            ->test(Show::class, ['tournament' => $tournament])
            ->assertForbidden();
    }
}
