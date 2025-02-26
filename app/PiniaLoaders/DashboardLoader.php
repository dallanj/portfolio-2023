<?php

namespace App\PiniaLoaders;

use Illuminate\Support\Collection;
use App\Enums\Applications;

class DashboardLoader
{
    /**
     * Get all of the applications
     * 
     * @return Collection
     */
    public function applications(): Collection
    {
        return collect(Applications::cases())->map(fn($app) => [
            'label' => $app->label(),
            'value' => $app->value,
            'application' => $app->isApplication(),
            'action' => $app->action(),
            'left' => $app->position()['left'] ?? 100,
            'top' => $app->position()['top'] ?? 100,
            'width' => $app->position()['width'] ?? 300,
            'height' => $app->position()['height'] ?? 300,
        ]);
    }
};
