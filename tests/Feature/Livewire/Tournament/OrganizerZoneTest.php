<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\OrganizerZone;
use App\Models\Tournament;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class OrganizerZoneTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(OrganizerZone::class, ['tournament' => $tournament, 'page' => 'general'])
            ->assertStatus(200);
    }

    public function testItRendersWithTheRightPage(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(OrganizerZone::class, ['tournament' => $tournament, 'page' => 'players'])
            ->assertSet('page', 'players');
    }

    public function testWhenNoPageIsSpecifiedItSetsItToGeneral(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(OrganizerZone::class, ['tournament' => $tournament])
            ->assertSet('page', 'general');
    }

    public function testWhenPageIsNotSupportedItSetsItToGeneral(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(OrganizerZone::class, ['tournament' => $tournament, 'page' => 'not-supported'])
            ->assertSet('page', 'general');
    }

    public function testWhenUserIsNotPartOfTournamentItRedirectsToDashboard(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs(User::factory()->create())
            ->test(OrganizerZone::class, ['tournament' => $tournament])
            ->assertRedirect(route('dashboard'));
    }

    public function testWhenUserIsPartOfTournamentButNotOrganizerItRedirectToShowPage(): void
    {
        $tournament = Tournament::factory()->full()->create();

        Livewire::actingAs($tournament->players->first())
            ->test(OrganizerZone::class, ['tournament' => $tournament])
            ->assertRedirect(route('tournaments.show', ['tournament' => $tournament]));
    }
}
