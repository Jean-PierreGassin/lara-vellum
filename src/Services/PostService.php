<?php

namespace JeanPierreGassin\LaraVellum\Services;

use JeanPierreGassin\LaraVellum\Data\SavePostPayload;
use JeanPierreGassin\LaraVellum\Models\Post;
use JeanPierreGassin\LaraVellum\Repositories\PostRepository;
use JeanPierreGassin\LaraVellum\Support\StaticPageGenerator;

readonly class PostService
{
    public function __construct(
        private PostRepository $posts,
        private StaticPageGenerator $generator,
    ) {}

    public function create(SavePostPayload $payload, ?int $authorId): Post
    {
        return $this->posts->create(payload: $payload, authorId: $authorId);
    }

    public function update(Post $post, SavePostPayload $payload): Post
    {
        $post = $this->posts->update(post: $post, payload: $payload);

        if ($post->isPublished()) {
            return $this->regenerate($post);
        }

        return $post;
    }

    public function publish(Post $post): Post
    {
        return $this->regenerate($post);
    }

    public function unpublish(Post $post): Post
    {
        $this->generator->forget($post);

        return $this->posts->markDraft($post);
    }

    public function delete(Post $post): void
    {
        $this->generator->forget($post);

        $this->posts->delete($post);
    }

    public function readStaticPage(Post $post): string
    {
        if (!$this->generator->exists($post)) {
            $post = $this->regenerate($post);
        }

        return $this->generator->read($post);
    }

    private function regenerate(Post $post): Post
    {
        $generated = $this->generator->generate($post);

        return $this->posts->markPublished(post: $post, contentHash: $generated->hash);
    }
}
