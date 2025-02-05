<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\Invitation;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class InvitationTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Invitation::class, ['tournament' => Tournament::factory()->create()])
            ->assertStatus(200);
    }

    public function testOrganizerCanCreateInvitation(): void
    {
        $tournament = Tournament::factory()->withoutInvitation()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(Invitation::class, ['tournament' => $tournament])
            ->call('refresh')
            ->assertSee($tournament->refresh()->invitation->code);

        $this->assertNotNull($tournament->invitation);
        $this->assertDatabaseCount('tournament_invitations', 1);
    }

    public function testOrganizerCanRefreshInvitation(): void
    {
        $tournament = Tournament::factory()->create();
        $initialCode = $tournament->invitation->code;

        Livewire::actingAs($tournament->organizer)
            ->test(Invitation::class, ['tournament' => $tournament])
            ->call('refresh')
            ->assertSee($tournament->refresh()->invitation->code)
            ->assertDontSee($initialCode);

        $this->assertDatabaseCount('tournament_invitations', 1);
    }

    public function testNonOrganizerCannotCreateInvitation(): void
    {
        $tournament = Tournament::factory()->withoutInvitation()->create();

        Livewire::actingAs(User::factory()->create())
            ->test(Invitation::class, ['tournament' => $tournament])
            ->call('refresh')
            ->assertForbidden();

        $this->assertNull($tournament->refresh()->invitation);
        $this->assertDatabaseCount('tournament_invitations', 0);
    }

    public function testOrganizerCanDeleteInvitation(): void
    {
        $tournament = Tournament::factory()->create();
        $initialCode = $tournament->invitation->code;

        Livewire::actingAs($tournament->organizer)
            ->test(Invitation::class, ['tournament' => $tournament])
            ->call('delete')
            ->assertDontSee($initialCode);

        $this->assertNull($tournament->refresh()->invitation);
        $this->assertDatabaseCount('tournament_invitations', 0);
    }

    public function testNonOrganizerCannotDeleteInvitation(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs(User::factory()->create())
            ->test(Invitation::class, ['tournament' => $tournament])
            ->call('delete')
            ->assertForbidden();

        $this->assertNotNull($tournament->refresh()->invitation);
        $this->assertDatabaseCount('tournament_invitations', 1);
    }
}
