<?php

declare(strict_types=1);

namespace App\Services\Generators;

use App\Models\Phase;

/**
 * @template TPhase of Phase
 */
interface Generator
{
    /** @param TPhase $phase */
    public function generate(Phase $phase): void;

    /** @param TPhase $phase */
    public function supports(Phase $phase): bool;
}
