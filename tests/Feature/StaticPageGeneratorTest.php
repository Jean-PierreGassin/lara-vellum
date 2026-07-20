<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use JeanPierreGassin\LaraVellum\Enums\PostStatus;
use JeanPierreGassin\LaraVellum\Models\Post;
use JeanPierreGassin\LaraVellum\Support\StaticPageGenerator;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

function generator(): StaticPageGenerator
{
    return app(StaticPageGenerator::class);
}

/**
 * @param  array<string, mixed>  $attributes
 */
function publishedPost(array $attributes = []): Post
{
    return Post::create(array_merge([
        'title' => 'Static Post',
        'body' => '# Heading',
        'status' => PostStatus::Published,
    ], $attributes));
}

it('writes a content-hashed page rendered from the post', function () {
    $generated = generator()->generate(publishedPost());

    Storage::disk('local')->assertExists($generated->path);
    expect($generated->path)->toBe("vellum/pages/$generated->hash.html")
        ->and(Storage::disk('local')->get($generated->path))
        ->toContain('Static Post')
        ->toContain('<h1>Heading</h1>');
});

it('produces a different hash and prunes the old file when content changes', function () {
    $post = publishedPost();
    $first = generator()->generate($post);
    $post->update(['content_hash' => $first->hash]);

    $post->update(['body' => '# Changed heading']);
    $second = generator()->generate($post->refresh());

    expect($second->hash)->not->toBe($first->hash);
    Storage::disk('local')->assertMissing($first->path);
    Storage::disk('local')->assertExists($second->path);
});

it('keeps a single file when regenerating identical content', function () {
    $post = publishedPost();
    $first = generator()->generate($post);
    $post->update(['content_hash' => $first->hash]);

    $second = generator()->generate($post->refresh());

    expect($second->hash)->toBe($first->hash);
    Storage::disk('local')->assertExists($first->path);
});

it('forgets a post static file', function () {
    $post = publishedPost();
    $generated = generator()->generate($post);
    $post->update(['content_hash' => $generated->hash]);

    generator()->forget($post->refresh());

    Storage::disk('local')->assertMissing($generated->path);
});

it('does nothing when forgetting a post that was never generated', function () {
    generator()->forget(publishedPost(['content_hash' => null]));

    expect(Storage::disk('local')->allFiles())->toBe([]);
});
