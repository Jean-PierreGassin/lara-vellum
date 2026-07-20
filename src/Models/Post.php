<?php

namespace JeanPierreGassin\LaraVellum\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use JeanPierreGassin\LaraVellum\Enums\PostStatus;

/**
 * @property int $id
 * @property int|null $author_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $body
 * @property PostStatus $status
 * @property string|null $content_hash
 * @property Carbon|null $published_at
 * @property array<string, mixed>|null $meta
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 */
class Post extends Model
{
    use SoftDeletes;

    private const int WORDS_PER_MINUTE = 200;

    protected $fillable = [
        'author_id',
        'title',
        'slug',
        'excerpt',
        'body',
        'status',
        'content_hash',
        'published_at',
        'meta',
    ];

    public function getTable(): string
    {
        return config('lara-vellum.database.posts_table', 'vellum_posts');
    }

    public function isPublished(): bool
    {
        return $this->status->isPublished()
            && $this->published_at !== null
            && $this->published_at->isPast();
    }

    /**
     * Counted off the Markdown source rather than the rendered HTML, so the
     * figure is the same in the editor as it is on the published page.
     */
    public function wordCount(): int
    {
        $words = preg_split('/\s+/u', trim($this->body), flags: PREG_SPLIT_NO_EMPTY);

        return $words === false ? 0 : count($words);
    }

    public function readingMinutes(): int
    {
        return max(1, (int) ceil($this->wordCount() / self::WORDS_PER_MINUTE));
    }

    /**
     * @param  Builder<Post>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query
            ->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', Carbon::now());
    }

    /**
     * @param  Builder<Post>  $query
     */
    public function scopeDraft(Builder $query): void
    {
        $query->where('status', PostStatus::Draft);
    }

    protected static function booted(): void
    {
        static::creating(function (Post $post): void {
            if (empty($post->slug)) {
                $post->slug = $post->uniqueSlugFrom($post->title);
            }
        });
    }

    protected function uniqueSlugFrom(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $suffix = 2;

        while ($this->slugExists($slug)) {
            $slug = "$baseSlug-$suffix";
            $suffix++;
        }

        return $slug;
    }

    protected function slugExists(string $slug): bool
    {
        return static::withTrashed()
            ->where('slug', $slug)
            ->whereKeyNot($this->getKey())
            ->exists();
    }

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'meta' => 'array',
        ];
    }
}
