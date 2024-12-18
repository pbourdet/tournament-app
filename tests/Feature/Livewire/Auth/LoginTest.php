<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Login::class)
            ->assertStatus(200);
    }

    public function testUserCanLogin(): void
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
    }

    public function testUserCannotLoginWithInvalidPassword(): void
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email' => __('auth.failed')]);
    }

    public function testUserHittingRateLimitGetAnError(): void
    {
        $user = User::factory()->create();
        $throttleKey = Str::transliterate(Str::lower($user->email).'|127.0.0.1');
        for ($i = 0; $i < 5; ++$i) {
            RateLimiter::hit($throttleKey);
        }

        Livewire::test(Login::class)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->call('login')
            ->assertHasErrors(['email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($throttleKey)])]);
    }
}
