<?php

declare(strict_types=1);

namespace Tests\Feature\Tournaments;

use App\Models\Tournament;
use App\Models\TournamentInvitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TournamentInvitationTest extends TestCase
{
    use RefreshDatabase;

    public function testTournamentOrganizerCanCreateNewInvitation(): void
    {
        $organizer = User::factory()->create();
        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournament_invitation.create', ['tournament' => $tournament]));

        $this->assertDatabaseCount('tournament_invitations', 1);
        $response->assertOk();
    }

    public function testPreviousInvitationAreDeleted(): void
    {
        $organizer = User::factory()->create();
        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
        ]);

        TournamentInvitation::factory()->create([
            'tournament_id' => $tournament->id,
            'code' => 'ABCDEF',
        ]);

        $response = $this->actingAs($organizer)->post(route('tournament_invitation.create', ['tournament' => $tournament]));

        $this->assertDatabaseCount('tournament_invitations', 1);
        $this->assertEquals(0, TournamentInvitation::where('code', 'ABCDEF')->count());
        $response->assertOk();
    }

    public function testOtherUsersCantCreateInvitationForTournament(): void
    {
        $organizer = User::factory()->create();
        $otherUser = User::factory()->create();
        $tournament = Tournament::factory()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($otherUser)->post(route('tournament_invitation.create', ['tournament' => $tournament]));

        $this->assertDatabaseCount('tournament_invitations', 0);
        $response->assertForbidden();
    }
}
