<?php

use JeanPierreGassin\LaraVellum\Enums\Asset;
use JeanPierreGassin\LaraVellum\Support\AssetUrlGenerator;

use function Pest\Laravel\get;

function assetUrls(): AssetUrlGenerator
{
    return app(AssetUrlGenerator::class);
}

/**
 * Swaps the compiled dist file for the duration of the assertions, then puts
 * the original back. The original is copied to a sibling file rather than held
 * in memory, so it stays recoverable on disk if the run is interrupted.
 * Passing null for the content removes the file instead of rewriting it.
 */
function withAssetContent(Asset $asset, ?string $content, Closure $assertions): void
{
    $path = $asset->path();
    $backup = "$path.backup";

    if (!copy($path, $backup)) {
        throw new RuntimeException("Could not back up the dist asset at $path");
    }

    try {
        $content === null ? unlink($path) : file_put_contents($path, $content);

        $assertions();
    } finally {
        copy($backup, $path);
        unlink($backup);
    }
}

it('fingerprints the asset url with a content hash', function () {
    expect(assetUrls()->url(Asset::Css))->toMatch('/\/assets\/app\.css\?v=[a-f0-9]{12}$/');
});

it('derives the fingerprint from content, not the file timestamp', function () {
    $original = assetUrls()->url(Asset::Css);

    withAssetContent(Asset::Css, 'body{color:red}', function () use ($original) {
        expect(assetUrls()->url(Asset::Css))->not->toBe($original);
    });

    // Restoring the content rewrites the file, so the mtime has moved on. The
    // fingerprint coming back means it tracks content rather than the clock.
    expect(assetUrls()->url(Asset::Css))->toBe($original);
});

it('omits the fingerprint when the asset file is missing', function () {
    withAssetContent(Asset::Js, null, function () {
        expect(assetUrls()->url(Asset::Js))->not->toContain('?v=');
    });
});

it('returns 404 when the requested asset file is missing', function () {
    withAssetContent(Asset::Js, null, function () {
        get(route('lara-vellum.assets.js'))->assertNotFound();
    });
});

it('serves assets with a long-lived immutable cache', function () {
    $cacheControl = get(route('lara-vellum.assets.css'))->headers->get('cache-control');

    expect($cacheControl)
        ->toContain('max-age=31536000')
        ->toContain('immutable');
});
