<?php

declare(strict_types=1);

namespace App\Providers;

use App\Services\Generators\EliminationMatchesGenerator;
use App\Services\Generators\EliminationRoundsGenerator;
use App\Services\Generators\GroupsMatchesGenerator;
use App\Services\Generators\GroupsRoundsGenerator;
use Illuminate\Support\ServiceProvider;

class GeneratorServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->tagMatchesGenerators();
        $this->tagRoundsGenerators();
    }

    public function boot(): void
    {
    }

    private function tagMatchesGenerators(): void
    {
        $implementations = [
            EliminationMatchesGenerator::class,
            GroupsMatchesGenerator::class,
        ];

        $this->app->tag($implementations, 'matches_generators');
    }

    private function tagRoundsGenerators(): void
    {
        $implementations = [
            EliminationRoundsGenerator::class,
            GroupsRoundsGenerator::class,
        ];

        $this->app->tag($implementations, 'rounds_generators');
    }
}
