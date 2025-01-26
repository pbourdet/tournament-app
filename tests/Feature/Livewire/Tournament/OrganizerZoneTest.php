<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\OrganizerZone;
use App\Models\Tournament;
use Livewire\Livewire;
use Tests\TestCase;

class OrganizerZoneTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::actingAs($tournament->organizer)
            ->test(OrganizerZone::class, ['tournament' => $tournament])
            ->assertStatus(200);
    }
}
