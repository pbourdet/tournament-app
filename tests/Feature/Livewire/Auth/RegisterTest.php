<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Auth;

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::test(Register::class)
            ->assertStatus(200);
    }

    public function testUserCanRegister(): void
    {
        Livewire::test(Register::class)
            ->set('form.username', 'JohnDoe')
            ->set('form.email', 'test@tournament.test')
            ->set('form.password', 'password')
            ->set('form.passwordConfirmation', 'password')
            ->call('register')
            ->assertRedirect(route('dashboard'))
            ->assertDispatched('toast-show');

        $this->assertAuthenticated();
        $this->assertDatabaseCount('users', 1);
    }

    public function testUserCanRegisterWithProfilePicture(): void
    {
        Storage::fake();

        Livewire::test(Register::class)
            ->set('form.username', 'JohnDoe')
            ->set('form.email', 'test@tournament.test')
            ->set('form.password', 'password')
            ->set('form.passwordConfirmation', 'password')
            ->set('form.profilePicture', UploadedFile::fake()->image('profile.jpg'))
            ->call('register')
            ->assertRedirect(route('dashboard'))
            ->assertDispatched('toast-show');

        $this->assertAuthenticated();
        $this->assertDatabaseCount('users', 1);
        Storage::disk('s3')->assertExists(auth()->user()->profile_picture);
    }

    public function testUserCannotRegisterWithUsedEmailOrUsername(): void
    {
        $user = User::factory()->create();

        Livewire::test(Register::class)
            ->set('form.username', $user->username)
            ->set('form.email', $user->email)
            ->set('form.password', 'password')
            ->set('form.passwordConfirmation', 'password')
            ->call('register')
            ->assertHasErrors(['form.username', 'form.email']);
    }
}
