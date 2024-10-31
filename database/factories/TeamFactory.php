<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Team> */
class TeamFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'name' => 'Team '.fake()->name,
        ];
    }

    /** @param array<User>|Collection<int, User> $users */
    public function withMembers(array|Collection $users): static
    {
        return $this->afterCreating(function (Team $team) use ($users): void {
            $team->members()->attach(collect($users)->pluck('id'));
        });
    }
}
