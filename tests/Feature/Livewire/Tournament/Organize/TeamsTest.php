<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Livewire\Tournament\Organize\Teams;
use Livewire\Livewire;
use Tests\TestCase;

class TeamsTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::test(Teams::class)
            ->assertStatus(200);
    }
}
