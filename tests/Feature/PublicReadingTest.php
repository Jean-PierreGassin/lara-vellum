<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use JeanPierreGassin\LaraVellum\Enums\PostStatus;
use JeanPierreGassin\LaraVellum\Models\Post;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

/**
 * @param  array<string, mixed>  $attributes
 */
function livePost(array $attributes = []): Post
{
    return Post::create(array_merge([
        'title' => 'A Live Post',
        'body' => '# Hello reader',
        'status' => PostStatus::Published,
        'published_at' => Carbon::now()->subDay(),
    ], $attributes));
}

it('renders a published post at its slug', function () {
    livePost(['title' => 'Reading Matters']);

    get('/reading-matters')
        ->assertOk()
        ->assertSee('Reading Matters')
        ->assertSee('Hello reader');
});

it('generates the static file on first read and serves it thereafter', function () {
    $post = livePost();

    get("/$post->slug")->assertOk();

    expect($post->refresh()->content_hash)->not->toBeNull();
    Storage::disk('local')->assertExists("vellum/pages/{$post->content_hash}.html");
});

it('returns 404 for a draft post', function () {
    $post = Post::create([
        'title' => 'Secret Draft',
        'body' => 'wip',
        'status' => PostStatus::Draft,
    ]);

    get("/$post->slug")->assertNotFound();
});

it('returns 404 for a post scheduled in the future', function () {
    $post = livePost(['published_at' => Carbon::now()->addWeek()]);

    get("/$post->slug")->assertNotFound();
});

it('returns 404 for an unknown slug', function () {
    get('/does-not-exist')->assertNotFound();
});

it('serves the compiled stylesheet and script', function () {
    get(route('lara-vellum.assets.css'))
        ->assertOk()
        ->assertHeader('content-type', 'text/css; charset=UTF-8');

    get(route('lara-vellum.assets.js'))
        ->assertOk()
        ->assertHeader('content-type', 'text/javascript; charset=UTF-8');
});
