<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\Models\Phase;
use Illuminate\Container\Attributes\Tag;

readonly class PhaseMatchesGenerator
{
    /**
     * @param array<int, Generator<Phase>> $roundsGenerator
     * @param array<int, Generator<Phase>> $matchesGenerators
     */
    public function __construct(
        #[Tag('rounds_generators')] private iterable $roundsGenerator,
        #[Tag('matches_generators')] private iterable $matchesGenerators,
    ) {
    }

    public function generate(Phase $phase): void
    {
        foreach ($this->roundsGenerator as $generator) {
            if ($generator->supports($phase)) {
                $generator->generate($phase);
            }
        }

        foreach ($this->matchesGenerators as $generator) {
            if ($generator->supports($phase)) {
                $generator->generate($phase);
            }
        }
    }
}
