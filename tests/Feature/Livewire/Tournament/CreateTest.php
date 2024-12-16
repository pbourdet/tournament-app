<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Tournament;

use App\Enums\ToastType;
use App\Livewire\Tournament\Create;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    public function testRendersSuccessfully(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Create::class)
            ->assertStatus(200);
    }

    public function testUserCanGoToNextAndPreviousSteps(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Create::class)
            ->assertSee(__('and a description'))
            ->set('form.name', 'name')
            ->call('next')
            ->assertSet('currentStep', 2)
            ->assertSee(__('How many players will participate in your tournament ?'))
            ->call('previous')
            ->assertSet('currentStep', 1)
            ->assertSee(__('and a description'));
    }

    public function testUserCantGoBelowStepOneOrOverStepFour(): void
    {
        Livewire::actingAs(User::factory()->create())
            ->test(Create::class)
            ->call('previous')
            ->assertSet('currentStep', 1)
            ->set('form.name', 'name')
            ->call('next')
            ->call('next')
            ->call('next')
            ->assertSet('currentStep', 4)
            ->call('next')
            ->assertSet('currentStep', 4);
    }

    public function testUserCanCreateTournament(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('form.name', 'name')
            ->set('form.numberOfPlayers', 32)
            ->call('save')
            ->assertRedirect(route('tournaments.show', ['tournament' => Tournament::first()]))
            ->assertDispatched('toast-show');

        $this->assertCount(1, $user->managedTournaments);
        $this->assertCount(1, $user->tournaments);
        $this->assertDatabaseHas('tournaments', [
            'name' => 'name',
            'number_of_players' => 32,
            'team_based' => false,
            'team_size' => null,
        ]);
        $this->assertDatabaseHas('tournament_invitations', [
            'tournament_id' => Tournament::firstOrFail()->id,
        ]);
    }

    public function testUserCanCreateTeamBasedTournament(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('form.name', 'name')
            ->set('form.numberOfPlayers', 32)
            ->set('form.teamBased', true)
            ->set('form.teamSize', 4)
            ->set('form.joinTournament', false)
            ->call('save')
            ->assertRedirect(route('tournaments.show', ['tournament' => Tournament::first()]));

        $this->assertCount(1, $user->managedTournaments);
        $this->assertCount(0, $user->tournaments);
        $this->assertDatabaseHas('tournaments', [
            'name' => 'name',
            'number_of_players' => 32,
            'team_based' => true,
            'team_size' => 4,
        ]);
        $this->assertDatabaseHas('tournament_invitations', [
            'tournament_id' => Tournament::firstOrFail()->id,
        ]);
    }

    public function testUserCannotCreateTeamBasedTournamentWithWrongValues(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Create::class)
            ->set('form.name', 'name')
            ->set('form.numberOfPlayers', 17)
            ->set('form.teamBased', true)
            ->set('form.teamSize', 4)
            ->call('save')
            ->assertHasErrors(['form.teamSize']);
    }

    public function testUserCantAccessCreatePageIfHeHasAlreadyCreatedTwoTournaments(): void
    {
        $user = User::factory()->create();
        Tournament::factory(2)->create([
            'organizer_id' => $user->id,
        ]);

        Livewire::actingAs($user)
            ->test(Create::class)
            ->assertRedirect(route('dashboard'));
    }

    public function testUserCantCreateMoreThanTwoTournaments(): void
    {
        $user = User::factory()->create();

        $livewireTest = Livewire::actingAs($user)
            ->test(Create::class)
            ->set('form.name', 'name');

        Tournament::factory(2)->create([
            'organizer_id' => $user->id,
        ]);

        $livewireTest
            ->call('save')
            ->assertForbidden();
    }
}
