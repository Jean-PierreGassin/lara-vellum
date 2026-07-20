<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use JeanPierreGassin\LaraVellum\Enums\PostStatus;
use JeanPierreGassin\LaraVellum\Models\Post;

uses(RefreshDatabase::class);

/**
 * @param  array<string, mixed>  $attributes
 */
function makePost(array $attributes = []): Post
{
    return Post::create(array_merge([
        'title' => 'Hello World',
        'body' => '# Hello',
        'status' => PostStatus::Draft,
    ], $attributes));
}

it('derives a slug from the title when none is given', function () {
    expect(makePost(['title' => 'My First Post'])->slug)->toBe('my-first-post');
});

it('appends a numeric suffix to keep slugs unique', function () {
    makePost(['title' => 'Duplicate Title']);

    expect(makePost(['title' => 'Duplicate Title'])->slug)->toBe('duplicate-title-2');
});

it('keeps an explicitly provided slug', function () {
    expect(makePost(['slug' => 'custom-slug'])->slug)->toBe('custom-slug');
});

it('casts status to the enum and meta to an array', function () {
    $post = makePost([
        'status' => PostStatus::Published,
        'meta' => ['seo_title' => 'Custom'],
    ])->refresh();

    expect($post->status)->toBe(PostStatus::Published)
        ->and($post->meta)->toBe(['seo_title' => 'Custom']);
});

it('is published only when published and the publish date has passed', function (PostStatus $status, ?Carbon $publishedAt, bool $expected) {
    $post = makePost([
        'status' => $status,
        'published_at' => $publishedAt,
    ]);

    expect($post->isPublished())->toBe($expected);
})->with([
    'published in the past' => [PostStatus::Published, Carbon::now()->subDay(), true],
    'published in the future' => [PostStatus::Published, Carbon::now()->addDay(), false],
    'published without a date' => [PostStatus::Published, null, false],
    'draft with a past date' => [PostStatus::Draft, Carbon::now()->subDay(), false],
]);

it('scopes to only live published posts', function () {
    $live = makePost(['title' => 'Live', 'status' => PostStatus::Published, 'published_at' => Carbon::now()->subDay()]);
    makePost(['title' => 'Scheduled', 'status' => PostStatus::Published, 'published_at' => Carbon::now()->addDay()]);
    makePost(['title' => 'Draft', 'status' => PostStatus::Draft]);

    $published = Post::query()->published()->get();

    expect($published->pluck('id')->all())->toBe([$live->id]);
});

it('scopes to only draft posts', function () {
    $draft = makePost(['title' => 'Draft', 'status' => PostStatus::Draft]);
    makePost(['title' => 'Live', 'status' => PostStatus::Published, 'published_at' => Carbon::now()->subDay()]);

    expect(Post::query()->draft()->get()->pluck('id')->all())->toBe([$draft->id]);
});

it('counts the words in the markdown body, collapsing runs of whitespace', function (string $body, int $expected) {
    expect(new Post(['body' => $body])->wordCount())->toBe($expected);
})->with([
    'empty' => ['', 0],
    'single word' => ['hello', 1],
    'padded and irregularly spaced' => ["  one\n\n two\tthree  ", 3],
]);

it('rounds reading time up and never reports less than a minute', function (int $words, int $expected) {
    expect(new Post(['body' => trim(str_repeat('word ', $words))])->readingMinutes())->toBe($expected);
})->with([
    'empty' => [0, 1],
    'well under a minute' => [10, 1],
    'exactly a minute' => [200, 1],
    'just over a minute' => [201, 2],
]);
