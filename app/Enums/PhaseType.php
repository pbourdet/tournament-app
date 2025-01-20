<?php

declare(strict_types=1);

namespace App\Enums;

enum PhaseType: string
{
    case ELIMINATION = 'elimination';
    case GROUP = 'group';

    /** @var array<int, PhaseType> */
    public const array QUALIFICATION_TYPES = [
        self::GROUP,
    ];
}
