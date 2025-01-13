<?php

declare(strict_types=1);

namespace App\Enums;

enum RoundStage: string
{
    case R128 = 'W128';
    case R64 = 'W64';
    case R32 = 'W32';
    case R16 = 'W16';
    case QF = 'W8';
    case SF = 'W4';
    case F = 'W2';

    /** @return array<int, RoundStage> */
    public static function getRoundsForContestants(int $numberOfContestants): array
    {
        $rounds = [];

        foreach (self::cases() as $round) {
            if (substr($round->value, 1) <= $numberOfContestants) {
                $rounds[] = $round;
            }
        }

        return $rounds;
    }

    public function getNextStage(): ?RoundStage
    {
        return match ($this) {
            self::R128 => self::R64,
            self::R64 => self::R32,
            self::R32 => self::R16,
            self::R16 => self::QF,
            self::QF => self::SF,
            self::SF => self::F,
            default => null,
        };
    }

    public function getMatchCount(): int
    {
        return (int) substr($this->value, 1) / 2;
    }
}
