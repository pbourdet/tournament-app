<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Livewire\Tournament\PlayersTable;
use App\Models\Tournament;
use Livewire\Livewire;
use Tests\TestCase;

class PlayersTableTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        $tournament = Tournament::factory()->create();

        Livewire::test(PlayersTable::class, ['tournament' => $tournament])
            ->assertStatus(200);
    }
}
