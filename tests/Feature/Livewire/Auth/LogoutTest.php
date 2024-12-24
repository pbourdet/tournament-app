<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\Logout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Logout::class)
            ->assertStatus(200);
    }

    public function testLogout(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Logout::class)
            ->call('logout')
            ->assertRedirectToRoute('login');

        $this->assertGuest();
    }
}
