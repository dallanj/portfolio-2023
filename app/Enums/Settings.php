<?php

namespace App\Enums;

use App\Enums\EnumTrait;

enum Settings: string
{
    use EnumTrait;

    case LOCALE = 'locale';
    case DOCKPOSITION = 'dock-position';
    case ACCESSIBILITY = 'accessibility';

    public function label(): string
    {
        return match ($this) {
            self::LOCALE => 'Language',
            self::DOCKPOSITION => 'Dock Position',
            self::ACCESSIBILITY => 'Accessibility',
        };
    }
}
