<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTournamentTest extends TestCase
{
    use RefreshDatabase;

    public function testTournamentCreationScreenCanBeRendered(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('tournaments.create'));

        $response->assertStatus(200);
        $response->assertSee(__('Create a tournament'));
    }

    public function testUserCanCreateTournament(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tournaments.store'), [
            'description' => 'description',
            'number_of_players' => 32,
            'name' => 'name',
        ]);

        $this->assertCount(1, $user->managedTournaments);
        $this->assertCount(0, $user->tournaments);
        $response->assertRedirectToRoute('dashboard');
    }

    public function testUserCanCreateAndJoinTournament(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tournaments.store'), [
            'description' => 'description',
            'join_tournament' => 'on',
            'number_of_players' => 32,
            'name' => 'name',
        ]);

        $this->assertCount(1, $user->managedTournaments);
        $this->assertCount(1, $user->tournaments);
        $response->assertRedirectToRoute('dashboard');
    }

    public function testUserCanCreateMoreThanTwoTournaments(): void
    {
        $user = User::factory()->create();
        Tournament::factory(2)->create([
            'organizer_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tournaments.store'), [
            'description' => 'description',
            'number_of_players' => 32,
            'name' => 'name',
        ]);

        $response->assertForbidden();
    }
}
