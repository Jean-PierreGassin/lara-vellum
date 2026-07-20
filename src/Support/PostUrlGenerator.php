<?php

namespace JeanPierreGassin\LaraVellum\Support;

use Illuminate\Contracts\Routing\UrlGenerator;

readonly class PostUrlGenerator
{
    public function __construct(
        private UrlGenerator $url,
    ) {}

    public function forSlug(string $slug): string
    {
        return $this->url->route(
            name: 'lara-vellum.posts.show',
            parameters: ['slug' => $slug],
        );
    }
}
