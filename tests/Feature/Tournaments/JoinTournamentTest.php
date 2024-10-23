<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Enums\ToastType;
use App\Models\Tournament;
use App\Models\User;
use App\Notifications\PlayerJoined;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class JoinTournamentTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanGetTheJoinView(): void
    {
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();

        $response = $this->actingAs($user)->get(route('tournament-invitations.join', ['code' => $tournament->invitation?->code]));

        $response->assertOk();
        $response->assertViewIs('tournaments.invitations.join');
    }

    public function testUserGetAClearMessageIfTheyCantJoinTheTournament(): void
    {
        $user = User::factory()->create();

        $fullTournament = Tournament::factory()->full()->create(['number_of_players' => 2]);
        $joinedTournament = Tournament::factory()->withPlayers([$user])->create();

        $response = $this->actingAs($user)->get(route('tournament-invitations.join', ['code' => 'fake code']));
        $response->assertOk();
        $response->assertSeeText(__('No tournament with this invitation code.'));

        $response = $this->actingAs($user)->get(route('tournament-invitations.join', ['code' => $fullTournament->invitation?->code]));
        $response->assertOk();
        $response->assertSeeText(__('This tournament is full.'));

        $response = $this->actingAs($user)->get(route('tournament-invitations.join', ['code' => $joinedTournament->invitation?->code]));
        $response->assertOk();
        $response->assertSeeText(__('You are already taking part in this tournament.'));
    }

    public function testUserCanJoinATournament(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();

        $response = $this->actingAs($user)->post(route('tournaments.join', ['tournament' => $tournament]));

        $this->assertDatabaseCount('tournament_player', 1);
        $response->assertSessionHas(ToastType::SUCCESS->value, __('You joined tournament :name', ['name' => $tournament->name]));
        Notification::assertSentTo($tournament->organizer, PlayerJoined::class);
    }

    public function testUserCannotJoinAFullTournament(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create([
            'number_of_players' => 2,
        ]);
        $tournament->players()->attach(User::factory()->create());
        $tournament->players()->attach(User::factory()->create());

        $response = $this->actingAs($user)->post(route('tournaments.join', ['tournament' => $tournament]));

        $this->assertFalse($tournament->players->contains($user));
        $response->assertSessionHas(ToastType::DANGER->value, __('You cannot join this tournament.'));
        Notification::assertNothingSent();
    }

    public function testUserCannotJoinATournamentTwice(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();
        $tournament->players()->attach($user);

        $response = $this->actingAs($user)->post(route('tournaments.join', ['tournament' => $tournament]));

        $this->assertDatabaseCount('tournament_player', 1);
        $response->assertSessionHas(ToastType::DANGER->value, __('You cannot join this tournament.'));
        Notification::assertNothingSent();
    }
}
