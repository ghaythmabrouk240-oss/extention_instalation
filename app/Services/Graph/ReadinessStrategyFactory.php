<?php

namespace App\Services\Graph;

use InvalidArgumentException;

class ReadinessStrategyFactory
{
    public static function make(string $profile): InstallationReadinessStrategy
    {
        return match ($profile) {
            'CATHETERISME' => new CathLabReadinessStrategy(),
            'IRM' => new MriReadinessStrategy(),
            default => throw new InvalidArgumentException("Profile '{$profile}' is not supported."),
        };
    }
}
