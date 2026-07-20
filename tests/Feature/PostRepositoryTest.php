<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use JeanPierreGassin\LaraVellum\Enums\PostStatus;
use JeanPierreGassin\LaraVellum\Models\Post;
use JeanPierreGassin\LaraVellum\Repositories\PostRepository;

uses(RefreshDatabase::class);

function postRepository(): PostRepository
{
    return app(PostRepository::class);
}

function repositoryDraft(string $title): Post
{
    return Post::create(['title' => $title, 'body' => 'x', 'status' => PostStatus::Draft]);
}

function repositoryPublished(string $title): Post
{
    return Post::create([
        'title' => $title,
        'body' => 'x',
        'status' => PostStatus::Published,
        'published_at' => Carbon::now()->subDay(),
    ]);
}

it('reports a zero for a status nothing has been written in yet', function () {
    repositoryDraft('Only Draft');

    expect(postRepository()->countsByStatus())->toBe(['draft' => 1, 'published' => 0]);
});

it('leaves soft deleted posts out of the status counts', function () {
    repositoryDraft('Kept');
    repositoryDraft('Binned')->delete();

    expect(postRepository()->countsByStatus()['draft'])->toBe(1);
});

it('narrows the dashboard list to a single status', function () {
    repositoryDraft('Draft One');
    repositoryPublished('Live One');

    $titles = collect(postRepository()->paginateForDashboard(PostStatus::Draft)->items())
        ->pluck('title')
        ->all();

    expect($titles)->toBe(['Draft One']);
});

it('lists every status when no filter is given', function () {
    repositoryDraft('Draft One');
    repositoryPublished('Live One');

    expect(postRepository()->paginateForDashboard())->toHaveCount(2);
});
