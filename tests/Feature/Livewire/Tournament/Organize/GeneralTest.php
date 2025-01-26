<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament\Organize;

use App\Livewire\Tournament\Organize\General;
use App\Models\Tournament;
use Livewire\Livewire;
use Tests\TestCase;

class GeneralTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::test(General::class, ['tournament' => Tournament::factory()->create()])
            ->assertStatus(200);
    }
}
