<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Profile;

use App\Livewire\Profile\Edit;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

class EditTest extends TestCase
{
    use RefreshDatabase;

    public function testRenderSuccessfully(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Edit::class)
            ->assertStatus(200);
    }

    public function testUserCanUpdateHisProfileInformation(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
            ->set('informationForm.username', 'JohnDoe')
            ->set('informationForm.email', 'test@tournament.test')
            ->call('updateInformation')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'JohnDoe',
            'email' => 'test@tournament.test',
            'email_verified_at' => null,
        ]);
    }

    public function testItDoesNotResetEmailVerifiedIfEmailWasNotModified(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
            ->set('informationForm.username', 'JohnDoe')
            ->set('informationForm.email', $user->email)
            ->call('updateInformation')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'username' => 'JohnDoe',
            'email' => $user->email,
            'email_verified_at' => $user->email_verified_at,
        ]);
    }

    public function testUserCanSendVerificationEmail(): void
    {
        Notification::fake();

        $user = User::factory()->unverified()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
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
            ->test(Edit::class)
            ->call('sendVerification')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        Notification::assertNotSentTo($user, VerifyEmail::class);
    }
}
