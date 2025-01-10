<?php

declare(strict_types=1);

namespace App\Models;

use Webmozart\Assert\Assert;

class EliminationConfiguration implements PhaseConfiguration
{
    public int $numberOfContestants;

    public static function fromArray(array $data): self
    {
        Assert::keyExists($data, 'numberOfContestants');
        Assert::positiveInteger($data['numberOfContestants']);

        $config = new self();
        $config->numberOfContestants = $data['numberOfContestants'];

        return $config;
    }

    public function toArray(): array
    {
        return [
            'numberOfContestants' => $this->numberOfContestants,
        ];
    }
}
