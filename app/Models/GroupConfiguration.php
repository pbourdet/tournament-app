<?php

declare(strict_types=1);

namespace App\Models;

use Webmozart\Assert\Assert;

class GroupConfiguration implements PhaseConfiguration
{
    public int $numberOfGroups;

    public int $contestantsQualifying;

    public static function fromArray(array $data): self
    {
        Assert::keyExists($data, 'numberOfGroups');
        Assert::keyExists($data, 'contestantsQualifying');
        Assert::positiveInteger($data['numberOfGroups']);
        Assert::positiveInteger($data['contestantsQualifying']);

        $config = new self();
        $config->numberOfGroups = $data['numberOfGroups'];
        $config->contestantsQualifying = $data['contestantsQualifying'];

        return $config;
    }

    public function toArray(): array
    {
        return [
            'numberOfGroups' => $this->numberOfGroups,
            'contestantsQualifying' => $this->contestantsQualifying,
        ];
    }
}
