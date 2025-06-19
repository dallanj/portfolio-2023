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
    case RESUME = 'resume';
    case LINKEDIN = 'linkedin';
    case GITHUB = 'github';
    
    /**
     * Application title
     *
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::TERMINAL => 'Terminal',
            self::ABOUT => 'About',
            self::PROJECTS => 'Projects',
            self::RESUME => 'Resume',
            self::CONTACT => 'Contact',
            self::LINKEDIN => 'Linked-In',
            self::GITHUB => 'Github',
        };
    }
    
    /**
     * Is application
     *
     * @return bool
     */
    public function isApplication(): bool
    {
        return match ($this) {
            self::LINKEDIN, self::GITHUB => false,
            default => true,
        };
    }
    
    /**
     * Application actions
     *
     * @return string
     */
    public function action(): ?string
    {
        return match ($this) {
            self::LINKEDIN => 'https://www.linkedin.com/',
            self::GITHUB => 'https://github.com/',
            default => null,
        };
    } 
    
    /**
     * Default application window position and sizes
     *
     * @return array
     */
    public function position(): ?array
    {
        return match ($this) {
            self::TERMINAL => [
                'left' => 90,
                'top' => 35,
                'width' => 400,
                'height' => 350,
            ],
            self::ABOUT => [
                'left' => 420,
                'top' => 35,
                'width' => 420,
                'height' => 350,
            ],
            self::PROJECTS => [
                'left' => 90,
                'top' => 375,
                'width' => 500,
                'height' => 400,
            ],
            self::RESUME => [
                'left' => 100,
                'top' => 60,
                'width' => 1000,
                'height' => 600,
            ],
            self::CONTACT => [
                'left' => 300,
                'top' => 100,
                'width' => 300,
                'height' => 350,
            ],
            self::LINKEDIN => [
                'left' => 700,
                'top' => 300,
                'width' => 250,
                'height' => 250,
            ],
            self::GITHUB => [
                'left' => 700,
                'top' => 300,
                'width' => 250,
                'height' => 250,
            ],
        };
    }
}
