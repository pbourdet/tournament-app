<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\SendVerificationEmail;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class SendVerificationEmailTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::test(SendVerificationEmail::class)
            ->assertStatus(200);
    }

    public function testUserCanSendVerificationEmail(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        Livewire::actingAs($user)
            ->test(SendVerificationEmail::class)
            ->call('sendVerification')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function testUserCannotSendVerificationEmailIfAlreadyVerified(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(SendVerificationEmail::class)
            ->call('sendVerification')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        Notification::assertNotSentTo($user, VerifyEmail::class);
    }

    public function testUserHitsRateLimitWhenSendingVerificationEmail(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        Livewire::actingAs($user)
            ->test(SendVerificationEmail::class)
            ->call('sendVerification')
            ->call('sendVerification')
            ->call('sendVerification')
            ->call('sendVerification')
            ->call('sendVerification')
            ->call('sendVerification')
            ->assertDispatched('toast-show');

        Notification::assertSentTimes(VerifyEmail::class, 5);
    }
}
