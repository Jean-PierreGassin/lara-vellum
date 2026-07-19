<?php

namespace JeanPierreGassin\LaraVellum\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\UrlGenerator;

readonly class PostUrlGenerator
{
    public function __construct(
        private UrlGenerator $url,
        private Repository $config,
    ) {}

    public function forSlug(string $slug): string
    {
        $prefix = trim((string) $this->config->get(key: 'lara-vellum.routing.public.prefix', default: ''), '/');
        $path = $prefix === '' ? $slug : "$prefix/$slug";

        return $this->url->to($path);
    }
}
