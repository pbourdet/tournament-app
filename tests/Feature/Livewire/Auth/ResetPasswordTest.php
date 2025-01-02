<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(ResetPassword::class, ['token' => 'token'])
            ->assertStatus(200);
    }

    public function testUserCanResetPassword(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        Livewire::withQueryParams(['email' => $user->email])
            ->test(ResetPassword::class, ['token' => $token])
            ->set('passwordForm.password', 'password')
            ->set('passwordForm.passwordConfirmation', 'password')
            ->call('resetPassword')
            ->assertRedirect(route('login'));
    }

    public function testUserCantResetPasswordWithInvalidToken(): void
    {
        $user = User::factory()->create();

        Livewire::withQueryParams(['email' => $user->email])
            ->test(ResetPassword::class, ['token' => 'fake'])
            ->set('passwordForm.password', 'password')
            ->set('passwordForm.passwordConfirmation', 'password')
            ->call('resetPassword')
            ->assertDispatched('toast-show')
            ->assertNoRedirect();
    }

    public function testUserCantResetPasswordWithInvalidEmail(): void
    {
        $user = User::factory()->create();
        $token = Password::broker()->createToken($user);

        Livewire::withQueryParams(['email' => 'test@tournament.test'])
            ->test(ResetPassword::class, ['token' => $token])
            ->set('passwordForm.password', 'password')
            ->set('passwordForm.passwordConfirmation', 'password')
            ->call('resetPassword')
            ->assertDispatched('toast-show')
            ->assertNoRedirect();
    }
}
