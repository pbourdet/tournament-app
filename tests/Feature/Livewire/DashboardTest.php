<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Dashboard;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Dashboard::class)
            ->assertStatus(200);
    }
}
