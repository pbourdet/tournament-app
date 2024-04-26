<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function testDashboardScreenCanBeRendered(): void
    {
        $user = User::factory()->create();
        Tournament::factory()->create([
            'organizer_id' => $user->id,
        ]);

        $response = $this
            ->actingAs($user)
            ->get('/dashboard');

        $response->assertOk();
    }
}
