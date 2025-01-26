<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Livewire\Tournament\Organize\Players;
use Livewire\Livewire;
use Tests\TestCase;

class PlayersTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::test(Players::class)
            ->assertStatus(200);
    }
}
