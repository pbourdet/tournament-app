<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Profile;

use App\Livewire\Profile\Edit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
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

    public function testUserCanUpdateTheirPassword(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
            ->set('passwordForm.currentPassword', 'password')
            ->set('passwordForm.password', 'new-password')
            ->set('passwordForm.passwordConfirmation', 'new-password')
            ->call('updatePassword')
            ->assertSuccessful()
            ->assertDispatched('toast-show');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    public function testUserMustProvideCorrectPasswordToUpdatePassword(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
            ->set('passwordForm.currentPassword', 'wrong-password')
            ->set('passwordForm.password', 'new-password')
            ->set('passwordForm.passwordConfirmation', 'new-password')
            ->call('updatePassword')
            ->assertHasErrors(['passwordForm.currentPassword']);
    }

    public function testUserCanDeleteTheirAccount(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
            ->set('deletionForm.password', 'password')
            ->call('deleteAccount')
            ->assertRedirect(route('login'))
            ->assertDispatched('toast-show')
        ;

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function testUserMustProvideCorrectPasswordToDeleteAccount(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
            ->set('deletionForm.password', 'wrong-password')
            ->call('deleteAccount')
            ->assertHasErrors(['deletionForm.password'])
        ;

        $this->assertAuthenticatedAs($user);
    }

    public function testUserCanUpdateTheirLanguage(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
            ->set('language', 'fr')
            ->assertSuccessful()
            ->assertDispatched('toast-show')
        ;

        $this->assertSame('fr', $user->refresh()->language);
    }

    public function testUserCannotUpdateTheirLanguageWithUnsupportedLocale(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Edit::class)
            ->set('language', 'es')
            ->assertHasErrors(['language'])
        ;

        $this->assertSame('en', $user->refresh()->language);
    }
}
