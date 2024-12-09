<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\Join;
use App\Models\Tournament;
use App\Models\User;
use App\Notifications\TournamentFull;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class JoinTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Join::class)
            ->assertStatus(200);
    }

    public function testUserSeesAClearMessageIfTheyCantJoinTheTournament(): void
    {
        $user = User::factory()->create();

        $fullTournament = Tournament::factory()->full()->create(['number_of_players' => 2]);
        $joinedTournament = Tournament::factory()->withPlayers([$user])->create();

        Livewire::actingAs($user)
            ->test(Join::class)
            ->set('tournamentCode', 'fake code')
            ->call('find')
            ->assertSee(__('No tournament with this invitation code.'))
            ->set('tournamentCode', $fullTournament->invitation->code)
            ->call('find')
            ->assertSee(__('This tournament is full.'))
            ->set('tournamentCode', $joinedTournament->invitation->code)
            ->call('find')
            ->assertSee(__('You are already taking part in this tournament.'));
    }

    public function testUserCanJoinATournament(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($user)
            ->test(Join::class)
            ->set('tournamentCode', $tournament->invitation->code)
            ->call('join', $tournament);

        $this->assertDatabaseHas('tournament_player', [
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
        ]);
    }

    public function testOrganizerIsNotifiedWhenTournamentIsFull(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $tournament = Tournament::factory()->create(['number_of_players' => 2]);
        $tournament->players()->attach(User::factory()->create());

        Livewire::actingAs($user)
            ->test(Join::class)
            ->set('tournamentCode', $tournament->invitation->code)
            ->call('join', $tournament);

        Notification::assertSentTo($tournament->organizer, TournamentFull::class);
    }

    public function testUserIsNotNotifiedIfTheyJoinTheirOwnTournament(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $tournament = Tournament::factory()->create(['organizer_id' => $user->id]);

        Livewire::actingAs($user)
            ->test(Join::class)
            ->set('tournamentCode', $tournament->invitation->code)
            ->call('join', $tournament);

        Notification::assertNothingSent();
    }

    public function testUserCannotJoinAFullTournament(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $tournament = Tournament::factory()->full()->create(['number_of_players' => 1]);

        Livewire::actingAs($user)
            ->test(Join::class)
            ->set('tournamentCode', $tournament->invitation->code)
            ->call('find')
            ->assertSee(__('This tournament is full.'));

        $this->assertDatabaseMissing('tournament_player', [
            'tournament_id' => $tournament->id,
            'user_id' => $user->id,
        ]);
        Notification::assertNothingSent();
    }

    public function testUserCannotJoinATournamentTwice(): void
    {
        Notification::fake();
        $user = User::factory()->create();
        $tournament = Tournament::factory()->create();
        $tournament->players()->attach($user);

        Livewire::actingAs($user)
            ->test(Join::class)
            ->set('tournamentCode', $tournament->invitation->code)
            ->call('find')
            ->assertSee(__('You are already taking part in this tournament.'));

        $this->assertDatabaseCount('tournament_player', 1);
        Notification::assertNothingSent();
    }
}
