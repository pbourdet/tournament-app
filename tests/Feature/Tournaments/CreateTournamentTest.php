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

    public function testUserCanCreateTournament(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('tournament.create'), [
            'description' => 'description',
            'number_of_players' => 32,
            'name' => 'name',
        ]);

        $this->assertCount(1, $user->managedTournaments);
        $response->assertRedirectToRoute('dashboard');
    }

    public function testUserCanCreateMoreThanTwoTournaments(): void
    {
        $user = User::factory()->create();
        Tournament::factory(2)->create([
            'organizer_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('tournament.create'), [
            'description' => 'description',
            'number_of_players' => 32,
            'name' => 'name',
        ]);

        $response->assertForbidden();
    }
}