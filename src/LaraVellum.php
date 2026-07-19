<?php

namespace JeanPierreGassin\LaraVellum;

use Illuminate\Contracts\Config\Repository;

class LaraVellum
{
    public function __construct(
        private readonly Repository $config,
    ) {}

    public function isEnabled(): bool
    {
        return (bool) $this->config->get('lara-vellum.enabled', true);
    }
}
