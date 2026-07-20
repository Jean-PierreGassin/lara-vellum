<?php

namespace JeanPierreGassin\LaraVellum\Http\Controllers;

use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JeanPierreGassin\LaraVellum\Contracts\MarkdownRenderer;
use JeanPierreGassin\LaraVellum\Http\Requests\SavePostRequest;
use JeanPierreGassin\LaraVellum\Models\Post;
use JeanPierreGassin\LaraVellum\Repositories\PostRepository;
use JeanPierreGassin\LaraVellum\Services\PostService;
use JeanPierreGassin\LaraVellum\Support\PostUrlGenerator;

readonly class DashboardPostController
{
    public function __construct(
        private PostService $posts,
        private PostRepository $repository,
        private MarkdownRenderer $renderer,
        private PostUrlGenerator $urls,
        private ViewFactory $view,
    ) {}

    public function index(): View
    {
        return $this->view->make(view: 'lara-vellum::dashboard.index', data: [
            'posts' => $this->repository->paginateForDashboard(),
        ]);
    }

    public function create(): View
    {
        return $this->view->make(view: 'lara-vellum::dashboard.create');
    }

    public function store(SavePostRequest $request): RedirectResponse
    {
        $post = $this->posts->create(
            payload: $request->toPayload(),
            authorId: $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route(route: 'lara-vellum.dashboard.edit', parameters: $post)
            ->with(key: 'status', value: __('lara-vellum::vellum.saved'));
    }

    public function edit(Post $post): View
    {
        $saveKey = $post->isPublished() ? 'save' : 'save_draft';

        return $this->view->make(view: 'lara-vellum::dashboard.edit', data: [
            'post' => $post,
            'publicUrl' => $this->urls->forSlug($post->slug),
            'saveLabel' => __("lara-vellum::vellum.editor.$saveKey"),
        ]);
    }

    public function update(SavePostRequest $request, Post $post): RedirectResponse
    {
        $post = $this->posts->update(post: $post, payload: $request->toPayload());
        $statusKey = $post->isPublished() ? 'updated' : 'saved';

        return redirect()
            ->route(route: 'lara-vellum.dashboard.edit', parameters: $post)
            ->with(key: 'status', value: __("lara-vellum::vellum.$statusKey"));
    }

    public function publish(Post $post): RedirectResponse
    {
        $this->posts->publish($post);

        return redirect()
            ->route(route: 'lara-vellum.dashboard.edit', parameters: $post)
            ->with(key: 'status', value: __('lara-vellum::vellum.published'));
    }

    public function unpublish(Post $post): RedirectResponse
    {
        $this->posts->unpublish($post);

        return redirect()
            ->route(route: 'lara-vellum.dashboard.edit', parameters: $post)
            ->with(key: 'status', value: __('lara-vellum::vellum.unpublished'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->posts->delete($post);

        return redirect()
            ->route(route: 'lara-vellum.dashboard.index')
            ->with(key: 'status', value: __('lara-vellum::vellum.deleted'));
    }

    public function preview(Request $request): Response
    {
        return new Response(
            content: $this->renderer->toHtml((string) $request->input('body', '')),
            headers: ['Content-Type' => 'text/html'],
        );
    }
}
