<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\Models\Phase;
use App\Models\PhaseConfiguration;

/** @template T of PhaseConfiguration = PhaseConfiguration */
interface Generator
{
    /** @param Phase<PhaseConfiguration> $phase */
    public function supports(Phase $phase): bool;

    /** @param Phase<T> $phase */
    public function generate(Phase $phase): void;
}
