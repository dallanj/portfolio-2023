<?php

namespace App\Enums;

use App\Enums\EnumTrait;

enum Applications: string
{
    use EnumTrait;

    case TERMINAL = 'terminal';
    case ABOUT = 'about';
    case PROJECTS = 'projects';
    case CONTACT = 'contact';
    case LINKEDIN = 'linkedin';
    case GITHUB = 'github';
    case RESUME = 'resume';

    public function label(): string
    {
        return match ($this) {
            self::TERMINAL => 'Terminal',
            self::ABOUT => 'About',
            self::PROJECTS => 'Projects',
            self::CONTACT => 'Contact',
            self::LINKEDIN => 'Linked-In',
            self::GITHUB => 'Github',
            self::RESUME => 'Resume',
        };
    }

    public function isApplication(): bool
    {
        return match ($this) {
            self::LINKEDIN, self::GITHUB, self::RESUME => false,
            default => true,
        };
    }

    public function action(): ?string
    {
        return match ($this) {
            self::LINKEDIN => 'https://www.linkedin.com/',
            self::GITHUB => 'https://github.com/',
            self::RESUME => 'https://dallan.ca/',
            default => null,
        };
    }
}
