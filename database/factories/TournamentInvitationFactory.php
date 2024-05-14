<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\TournamentInvitation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<TournamentInvitation> */
class TournamentInvitationFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'code' => mb_strtoupper(Str::random(6)),
            'expires_at' => now()->addDays(7),
        ];
    }
}
