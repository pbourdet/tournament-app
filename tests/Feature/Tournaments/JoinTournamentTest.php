<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JoinTournamentTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanJoinATournament(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();

        $response = $this->actingAs($user)->post(route('tournament.join', ['tournament' => $tournament]));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseCount('tournament_player', 1);
    }

    public function testUserCannotJoinAFullTournament(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create([
            'number_of_players' => 2,
        ]);
        $tournament->players()->attach(User::factory()->create());
        $tournament->players()->attach(User::factory()->create());

        $response = $this->actingAs($user)->post(route('tournament.join', ['tournament' => $tournament]));

        $response->assertForbidden();
    }

    public function testUserCannotJoinATournamentTwice(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();
        $tournament->players()->attach($user);

        $response = $this->actingAs($user)->post(route('tournament.join', ['tournament' => $tournament]));

        $response->assertForbidden();
    }
}
