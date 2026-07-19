<?php

namespace JeanPierreGassin\LaraVellum\Http\Controllers;

use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

readonly class AssetController
{
    private const string ASSET_DIR = 'resources/dist';
    private const string STYLESHEET = 'vellum.css';
    private const string SCRIPT = 'vellum.js';
    private const int CACHE_SECONDS = 365 * 24 * 60 * 60; // one year

    public function css(): BinaryFileResponse
    {
        return $this->serve($this->assetPath(self::STYLESHEET), 'text/css');
    }

    public function js(): BinaryFileResponse
    {
        return $this->serve($this->assetPath(self::SCRIPT), 'text/javascript');
    }

    private function assetPath(string $file): string
    {
        return dirname(__DIR__, 3).'/'.self::ASSET_DIR.'/'.$file;
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
