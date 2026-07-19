<?php

namespace JeanPierreGassin\LaraVellum\Http\Controllers;

use Illuminate\Http\Response;
use JeanPierreGassin\LaraVellum\Enums\Asset;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

readonly class AssetController
{
    private const int CACHE_SECONDS = 365 * 24 * 60 * 60; // one year

    public function css(): BinaryFileResponse
    {
        return $this->serve(Asset::Css);
    }

    public function js(): BinaryFileResponse
    {
        return $this->serve(Asset::Js);
    }

    private function serve(Asset $asset): BinaryFileResponse
    {
        $path = $asset->path();

        if (!is_file($path)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $response = (new BinaryFileResponse($path))
            ->setMaxAge(self::CACHE_SECONDS)
            ->setImmutable()
            ->setPublic();

        $response->headers->set('Content-Type', $asset->contentType().'; charset=UTF-8');

        return $response;
    }
}
