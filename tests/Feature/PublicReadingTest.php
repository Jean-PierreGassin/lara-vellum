<?php

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Events\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use JeanPierreGassin\LaraVellum\Enums\PostStatus;
use JeanPierreGassin\LaraVellum\Http\RouteRegistrar;
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

    get('/posts/reading-matters')
        ->assertOk()
        ->assertSee('Reading Matters')
        ->assertSee('Hello reader');
});

it('generates the static file on first read and serves it thereafter', function () {
    $post = livePost();

    get("/posts/$post->slug")->assertOk();

    expect($post->refresh()->content_hash)->not->toBeNull();
    Storage::disk('local')->assertExists("vellum/pages/{$post->content_hash}.html");
});

it('returns 404 for a draft post', function () {
    $post = Post::create([
        'title' => 'Secret Draft',
        'body' => 'wip',
        'status' => PostStatus::Draft,
    ]);

    get("/posts/$post->slug")->assertNotFound();
});

it('returns 404 for a post scheduled in the future', function () {
    $post = livePost(['published_at' => Carbon::now()->addWeek()]);

    get("/posts/$post->slug")->assertNotFound();
});

it('returns 404 for an unknown slug', function () {
    get('/posts/does-not-exist')->assertNotFound();
});

/**
 * @return array<int, Route>
 */
function registerRoutesWithPrefix(string $prefix): array
{
    $router = new Router(new Dispatcher());

    (new RouteRegistrar(
        router: $router,
        config: new ConfigRepository(['lara-vellum' => ['routing' => ['public' => ['prefix' => $prefix]]]]),
    ))->register();

    return $router->getRoutes()->getRoutes();
}

it('mounts posts under the configured prefix, falling back to the default when it is blank', function (string $prefix, string $expected) {
    $uris = array_map(
        fn(Route $route) => $route->uri(),
        registerRoutesWithPrefix($prefix),
    );

    expect($uris)->toContain($expected);
})->with([
    'configured' => ['writing', 'writing/{slug}'],
    'wrapped in slashes' => ['/writing/', 'writing/{slug}'],
    'empty' => ['', 'posts/{slug}'],
    'slash only' => ['/', 'posts/{slug}'],
    'whitespace only' => ['   ', 'posts/{slug}'],
]);

it('never registers a fallback route that would capture host application urls', function () {
    $fallbacks = array_filter(
        registerRoutesWithPrefix(''),
        fn(Route $route) => $route->isFallback,
    );

    expect($fallbacks)->toBeEmpty();
});

it('serves the compiled stylesheet and script', function () {
    get(route('lara-vellum.assets.css'))
        ->assertOk()
        ->assertHeader('content-type', 'text/css; charset=UTF-8');

    get(route('lara-vellum.assets.js'))
        ->assertOk()
        ->assertHeader('content-type', 'text/javascript; charset=UTF-8');
});
