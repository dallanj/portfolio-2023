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
    case SETTINGS = 'settings';
    case PGP = 'pgp';
    
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
            self::SETTINGS => 'Settings',
            self::PGP => 'Pretty Good Privacy',
        };
    }

    /**
     * Application image name (png format)
     *
     * @return string
     */
    public function image(): string
    {
        return match ($this) {
            self::TERMINAL => 'terminal-app',
            self::ABOUT => 'about',
            self::PROJECTS => 'filemanager-app',
            self::RESUME => 'ebook-reader-app',
            self::CONTACT => 'mail-app',
            self::LINKEDIN => 'linkedin',
            self::GITHUB => 'github',
            self::SETTINGS => 'system-settings',
            self::PGP => 'gcr-gnupg',
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
            self::LINKEDIN,
            self::GITHUB,
            self::SETTINGS,
            self::PGP => false,
            default => true,
        };
    }
    
    /**
     * Application actions
     *
     * @return array|null
     */
    public function action(): ?array
    {
        return match ($this) {
            self::LINKEDIN => [
                'type' => 'url',
                'value' => config('links.linkedin_profile', 'https://linkedin.com'),
            ],
            self::GITHUB => [
                'type' => 'url',
                'value' => config('links.github_profile', 'https://github.com'),
            ],
            self::SETTINGS => [
                'type' => 'modal',
                'value' => 'SettingsMenuModal',
                'props' => [
                    'position' => 'justify-content: flex-end; align-items: flex-start; padding-top: 10px;',
                ],
            ],
            self::PGP => [
                'type' => 'modal',
                'value' => 'PublicKeyModal',
                'props' => [
                    'title' => 'My PGP Public Key',
                    'subtitle' => 'This key may be subject to change in the future.',
                    'position' => 'justify-content: safe center; align-items: center;',
                ],
            ],
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
            default => [],
        };
    }
}
