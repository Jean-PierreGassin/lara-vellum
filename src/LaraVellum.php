<?php

namespace JeanPierreGassin\LaraVellum;

use Illuminate\Contracts\Config\Repository;

readonly class LaraVellum
{
    public function __construct(
        private Repository $config,
    ) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get(
            key: 'lara-vellum.enabled',
            default: true,
        );
    }
}
