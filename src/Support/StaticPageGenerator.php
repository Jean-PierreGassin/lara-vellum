<?php

namespace JeanPierreGassin\LaraVellum\Support;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\View\Factory as ViewFactory;
use JeanPierreGassin\LaraVellum\Contracts\MarkdownRenderer;
use JeanPierreGassin\LaraVellum\Data\GeneratedPage;
use JeanPierreGassin\LaraVellum\Models\Post;

readonly class StaticPageGenerator
{
    public function __construct(
        private MarkdownRenderer $renderer,
        private ViewFactory $view,
        private FilesystemFactory $filesystem,
        private Repository $config,
    ) {}

    public function generate(Post $post): GeneratedPage
    {
        $html = $this->renderPage($post);
        $hash = $this->hash($html);
        $path = $this->pathFor($hash);

        $this->pruneStaleFile($post, $hash);
        $this->disk()->put(path: $path, contents: $html);

        return new GeneratedPage(
            hash: $hash,
            path: $path,
        );
    }

    public function forget(Post $post): void
    {
        if ($post->content_hash === null) {
            return;
        }

        $this->disk()->delete($this->pathFor($post->content_hash));
    }

    public function exists(Post $post): bool
    {
        return $post->content_hash !== null
            && $this->disk()->exists($this->pathFor($post->content_hash));
    }

    public function read(Post $post): string
    {
        return (string) $this->disk()->get($this->pathFor((string) $post->content_hash));
    }

    public function pathFor(string $hash): string
    {
        $directory = trim($this->config->get(key: 'lara-vellum.static.path', default: 'vellum/pages'), '/');

        return "$directory/$hash.html";
    }

    private function renderPage(Post $post): string
    {
        $view = $this->config->get(key: 'lara-vellum.views.public_show', default: 'lara-vellum::public.show');

        return $this->view->make(view: $view, data: [
            'post' => $post,
            'content' => $this->renderer->toHtml($post->body),
        ])->render();
    }

    private function pruneStaleFile(Post $post, string $newHash): void
    {
        if ($post->content_hash === null || $post->content_hash === $newHash) {
            return;
        }

        $this->disk()->delete($this->pathFor($post->content_hash));
    }

    private function hash(string $html): string
    {
        return hash('sha256', $html);
    }

    private function disk(): Filesystem
    {
        return $this->filesystem->disk($this->config->get(key: 'lara-vellum.static.disk', default: 'local'));
    }
}
