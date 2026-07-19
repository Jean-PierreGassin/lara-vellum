<?php

namespace JeanPierreGassin\LaraVellum\Support;

use Illuminate\Contracts\Routing\UrlGenerator;
use JeanPierreGassin\LaraVellum\Enums\Asset;

readonly class AssetUrlGenerator
{
    private const int VERSION_LENGTH = 12;

    public function __construct(
        private UrlGenerator $url,
    ) {}

    public function url(Asset $asset): string
    {
        $version = $this->version($asset);

        return $this->url->route(
            name: $asset->routeName(),
            parameters: $version === null ? [] : ['v' => $version],
        );
    }

    private function version(Asset $asset): ?string
    {
        $path = $asset->path();

        if (!is_file($path)) {
            return null;
        }

        return substr(hash_file('sha256', $path), 0, self::VERSION_LENGTH);
    }
}
