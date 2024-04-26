<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationScreenCanBeRendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function testNewUsersCanRegister(): void
    {
        Storage::fake('public');

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'profile_picture' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertNotNull(User::first());
        $this->assertNotNull(User::first()->profile_picture);
    }

    public function testNewUserCannotRegisterWithUsedEmail(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->get('/register');

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
    }
}
