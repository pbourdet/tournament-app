<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\Notifications;
use App\Models\Tournament;
use App\Models\User;
use App\Notifications\TournamentStarted;
use Livewire\Livewire;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    public function testRendersSuccessfully(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Notifications::class)
            ->assertStatus(200);
    }

    public function testUserCanDeleteAllNotifications(): void
    {
        $user = User::factory()->create();

        $user->notify(new TournamentStarted(Tournament::factory()->create()));

        $this->assertCount(1, $user->notifications);

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->call('deleteAll');

        $this->assertCount(0, $user->refresh()->notifications);
    }

    public function testUserCanMarkAllNotificationsAsRead(): void
    {
        $user = User::factory()->create();

        $user->notify(new TournamentStarted(Tournament::factory()->create()));

        $this->assertCount(1, $user->unreadNotifications);

        Livewire::actingAs($user)
            ->test(Notifications::class)
            ->call('readAll');

        $this->assertCount(0, $user->refresh()->unreadNotifications);
    }
}
