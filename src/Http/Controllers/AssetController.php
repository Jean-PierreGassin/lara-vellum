<?php

namespace JeanPierreGassin\LaraVellum\Http\Controllers;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

readonly class AssetController
{
    private const string STYLESHEET = __DIR__.'/../../../resources/dist/vellum.css';

    private const string SCRIPT = __DIR__.'/../../../resources/dist/vellum.js';

    private const int CACHE_SECONDS = 31536000;

    public function css(): BinaryFileResponse
    {
        return $this->serve(self::STYLESHEET, 'text/css');
    }

    public function js(): BinaryFileResponse
    {
        return $this->serve(self::SCRIPT, 'text/javascript');
    }

    private function serve(string $path, string $contentType): BinaryFileResponse
    {
        if (!is_file($path)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        $response = (new BinaryFileResponse($path))
            ->setMaxAge(self::CACHE_SECONDS)
            ->setPublic();

        $response->headers->set('Content-Type', "$contentType; charset=UTF-8");

        return $response;
    }
}
