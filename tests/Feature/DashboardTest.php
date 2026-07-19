<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use JeanPierreGassin\LaraVellum\Enums\PostStatus;
use JeanPierreGassin\LaraVellum\Models\Post;
use JeanPierreGassin\LaraVellum\Support\StaticPageGenerator;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake('local');
});

it('forbids guests from reaching the dashboard', function () {
    get('/vellum')->assertForbidden();
});

it('lets an authenticated user list posts', function () {
    Post::create(['title' => 'On Writing', 'body' => 'x', 'status' => PostStatus::Draft]);

    actingAs(vellumUser());

    get('/vellum')
        ->assertOk()
        ->assertSee('On Writing');
});

it('stores a new draft and redirects to its editor', function () {
    actingAs(vellumUser());

    post('/vellum', [
        'title' => 'Fresh Draft',
        'body' => '# Draft body',
    ])->assertRedirect();

    $post = Post::query()->firstOrFail();

    expect($post->title)->toBe('Fresh Draft')
        ->and($post->status)->toBe(PostStatus::Draft)
        ->and($post->author_id)->toBe(1);
});

it('publishes a draft and writes its static page', function () {
    $post = Post::create(['title' => 'To Publish', 'body' => '# Ready', 'status' => PostStatus::Draft]);

    actingAs(vellumUser());
    post("/vellum/$post->id/publish")->assertRedirect();

    $post->refresh();

    expect($post->status)->toBe(PostStatus::Published)
        ->and($post->published_at)->not->toBeNull()
        ->and($post->content_hash)->not->toBeNull();
    Storage::disk('local')->assertExists("vellum/pages/{$post->content_hash}.html");
});

it('unpublishes a post and removes its static page', function () {
    $post = Post::create([
        'title' => 'Live One',
        'body' => '# Live',
        'status' => PostStatus::Published,
        'published_at' => Carbon::now()->subDay(),
    ]);
    $generated = app(StaticPageGenerator::class)->generate($post);
    $post->update(['content_hash' => $generated->hash]);

    actingAs(vellumUser());
    post("/vellum/$post->id/unpublish")->assertRedirect();

    expect($post->refresh()->status)->toBe(PostStatus::Draft);
    Storage::disk('local')->assertMissing($generated->path);
});

it('regenerates the static page when editing a published post', function () {
    $post = Post::create([
        'title' => 'Editable',
        'body' => '# First',
        'status' => PostStatus::Published,
        'published_at' => Carbon::now()->subDay(),
    ]);
    $original = app(StaticPageGenerator::class)->generate($post);
    $post->update(['content_hash' => $original->hash]);

    actingAs(vellumUser());
    put("/vellum/$post->id", ['title' => 'Editable', 'body' => '# Second'])->assertRedirect();

    expect($post->refresh()->content_hash)->not->toBe($original->hash);
    Storage::disk('local')->assertMissing($original->path);
});

it('deletes a post', function () {
    $post = Post::create(['title' => 'Trash Me', 'body' => 'x', 'status' => PostStatus::Draft]);

    actingAs(vellumUser());
    delete("/vellum/$post->id")->assertRedirect('/vellum');

    expect(Post::query()->find($post->id))->toBeNull();
});

it('renders a markdown preview matching the publish renderer', function () {
    actingAs(vellumUser());

    post('/vellum/preview', ['body' => '# Preview heading'])
        ->assertOk()
        ->assertSee('<h1>Preview heading</h1>', false);
});

it('validates that a title and body are required', function () {
    actingAs(vellumUser());

    post('/vellum', ['title' => '', 'body' => ''])
        ->assertSessionHasErrors(['title', 'body']);
});
