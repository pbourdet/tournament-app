<?php

declare(strict_types=1);

namespace App\Enums;

enum SupportedLocale: string
{
    case ENGLISH = 'en';
    case FRENCH = 'fr';

    public function getLabel(): string
    {
        return match ($this) {
            self::ENGLISH => '🇬🇧 English',
            self::FRENCH => '🇫🇷 Français',
        };
    }
}
