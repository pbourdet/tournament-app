<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory(9)->create();

        User::factory()->create([
            'username' => 'Test User',
            'email' => 'test@example.com',
        ]);

        User::factory()->create([
            'username' => 'Team tournament',
            'email' => 'team@example.com',
        ]);

        User::factory()->create([
            'username' => 'Full Team tournament',
            'email' => 'full-team@example.com',
        ]);

        User::factory()->create([
            'username' => 'Full tournament',
            'email' => 'full@example.com',
        ]);
    }
}
