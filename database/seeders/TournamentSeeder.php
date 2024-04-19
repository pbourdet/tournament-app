<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tournament;
use App\Models\User;
use Illuminate\Database\Seeder;

class TournamentSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function (User $user) {
            Tournament::factory(2)->create([
                'organizer_id' => $user->id,
            ]);
        });
    }
}
