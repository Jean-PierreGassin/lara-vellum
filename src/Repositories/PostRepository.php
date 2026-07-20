<?php

namespace JeanPierreGassin\LaraVellum\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use JeanPierreGassin\LaraVellum\Data\SavePostPayload;
use JeanPierreGassin\LaraVellum\Enums\PostStatus;
use JeanPierreGassin\LaraVellum\Models\Post;

class PostRepository
{
    private const int PER_PAGE = 15;

    /**
     * @return LengthAwarePaginator<int, Post>
     */
    public function paginateForDashboard(?PostStatus $status = null): LengthAwarePaginator
    {
        return Post::query()
            ->when($status, fn(Builder $query, PostStatus $status) => $query->where('status', $status))
            ->latest('updated_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * Keyed by status value, with every case present so the dashboard can show
     * a zero rather than a missing filter.
     *
     * @return array<string, int>
     */
    public function countsByStatus(): array
    {
        $counts = Post::query()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return collect(PostStatus::cases())
            ->mapWithKeys(fn(PostStatus $status) => [$status->value => (int) $counts->get($status->value, 0)])
            ->all();
    }

    public function findPublishedBySlug(string $slug): ?Post
    {
        return Post::query()
            ->published()
            ->where('slug', $slug)
            ->first();
    }

    public function create(SavePostPayload $payload, ?int $authorId): Post
    {
        return Post::create([
            ...$payload->toAttributes(),
            'author_id' => $authorId,
            'status' => PostStatus::Draft,
        ]);
    }

    public function update(Post $post, SavePostPayload $payload): Post
    {
        $post->update($payload->toAttributes());

        return $post;
    }

    public function markPublished(Post $post, string $contentHash): Post
    {
        $post->update([
            'status' => PostStatus::Published,
            'content_hash' => $contentHash,
            'published_at' => $post->published_at ?? Carbon::now(),
        ]);

        return $post;
    }

    public function markDraft(Post $post): Post
    {
        $post->update([
            'status' => PostStatus::Draft,
            'content_hash' => null,
        ]);

        return $post;
    }

    public function delete(Post $post): void
    {
        $post->delete();
    }
}
