<?php

declare(strict_types=1);

namespace Tests\Feature\Teams;

use App\Jobs\GenerateTeams;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class GenerateTeamsTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanTriggerTeamGeneration(): void
    {
        Queue::fake();

        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->full()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.generate', ['tournament' => $tournament]));

        Queue::assertPushed(GenerateTeams::class);
        $response->assertAccepted();
    }

    public function testUserCannotTriggerTeamGenerationOnANonTeamBasedTournament(): void
    {
        Queue::fake();

        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->full()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.generate', ['tournament' => $tournament]));

        Queue::assertNothingPushed();
        $response->assertForbidden();
    }

    public function testUserCannotTriggerTeamGenerationOnANonFullTournament(): void
    {
        Queue::fake();

        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.generate', ['tournament' => $tournament]));

        Queue::assertNothingPushed();
        $response->assertForbidden();
    }

    public function testUserCannotTriggerTeamGenerationIfTournamentHasAllTeamsAlready(): void
    {
        Queue::fake();

        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->withAllTeams()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.generate', ['tournament' => $tournament]));

        Queue::assertNothingPushed();
        $response->assertForbidden();
    }

    public function testUserCannotTriggerTeamGenerationOnATournamentTheyDoNotOrganize(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->full()->create([
            'organizer_id' => $organizer->id,
        ]);

        $response = $this->actingAs($user)->post(route('tournaments.teams.generate', ['tournament' => $tournament]));

        Queue::assertNothingPushed();
        $response->assertForbidden();
    }

    public function testUserCannotTriggerTeamGenerationIfTeamsAreAlreadyBeingGenerated(): void
    {
        Queue::fake();

        $organizer = User::factory()->create();

        $tournament = Tournament::factory()->teamBased()->full()->create([
            'organizer_id' => $organizer->id,
        ]);

        Cache::lock(sprintf('tournament:%s:generate-teams', $tournament->id), 20)->get();

        $response = $this->actingAs($organizer)->post(route('tournaments.teams.generate', ['tournament' => $tournament]));

        Queue::assertNothingPushed();
        $response->assertConflict();
    }
}
