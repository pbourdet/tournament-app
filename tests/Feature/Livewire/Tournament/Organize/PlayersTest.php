<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Livewire\Tournament\Organize\Players;
use App\Models\Tournament;
use Livewire\Livewire;
use Tests\TestCase;

class PlayersTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::test(Players::class, ['tournament' => Tournament::factory()->create()])
            ->assertStatus(200);
    }
}
