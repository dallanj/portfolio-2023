<?php

namespace App\PiniaLoaders;

use Illuminate\Support\Collection;
use App\Enums\Settings;

class SettingsLoader
{
    /**
     * Get all of the users
     * 
     * @return Collection
     */
    public function all(): Collection
    {
        return collect(Settings::cases())->map(fn($app) => [
            'label' => $app->label(),
            'value' => $app->value,
        ]);
    }
};
