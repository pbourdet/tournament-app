<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ViewTournamentTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewTheTournamentAsOrganizer(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create(['organizer_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('tournaments.show', $tournament));

        $response->assertOk();
        $response->assertViewIs('tournaments.show');
    }

    public function testUserCanViewTheTournamentAsPlayer(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();
        $tournament->players()->attach($user);

        $response = $this->actingAs($user)->get(route('tournaments.show', $tournament));

        $response->assertOk();
        $response->assertViewIs('tournaments.show');
    }

    public function testUserCannotViewTheTournament(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();

        $response = $this->actingAs($user)->get(route('tournaments.show', $tournament));

        $response->assertStatus(403);
    }
}
