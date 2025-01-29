<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\Teams;
use App\Models\Tournament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        Event::fake();
    }

    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->withAllTeams()->create();

        Livewire::test(Teams::class, ['tournament' => $tournament])
            ->assertStatus(200);
    }
}
