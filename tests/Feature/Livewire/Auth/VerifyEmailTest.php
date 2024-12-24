<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\VerifyEmail;
use App\Models\User;
use Livewire\Livewire;
use Tests\TestCase;

class VerifyEmailTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::actingAs(User::factory()->unverified()->create())
        ->test(VerifyEmail::class)
            ->assertStatus(200);
    }

    public function testRedirectsToDashboardIfEmailIsVerified(): void
    {
        Livewire::actingAs($user = User::factory()->create())
            ->test(VerifyEmail::class)
            ->assertRedirect(route('dashboard'));
    }
}
