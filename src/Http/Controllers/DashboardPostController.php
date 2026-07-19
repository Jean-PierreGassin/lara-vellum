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

readonly class DashboardPostController
{
    public function __construct(
        private PostService $posts,
        private PostRepository $repository,
        private MarkdownRenderer $renderer,
        private ViewFactory $view,
    ) {}

    public function index(): View
    {
        return $this->view->make('lara-vellum::dashboard.index', [
            'posts' => $this->repository->paginateForDashboard(),
        ]);
    }

    public function create(): View
    {
        return $this->view->make('lara-vellum::dashboard.create');
    }

    public function store(SavePostRequest $request): RedirectResponse
    {
        $post = $this->posts->create(
            payload: $request->toPayload(),
            authorId: $request->user()?->getAuthIdentifier(),
        );

        return redirect()
            ->route('lara-vellum.dashboard.edit', $post)
            ->with('status', __('lara-vellum::vellum.saved'));
    }

    public function edit(Post $post): View
    {
        return $this->view->make('lara-vellum::dashboard.edit', [
            'post' => $post,
        ]);
    }

    public function update(SavePostRequest $request, Post $post): RedirectResponse
    {
        $this->posts->update($post, $request->toPayload());

        return redirect()
            ->route('lara-vellum.dashboard.edit', $post)
            ->with('status', __('lara-vellum::vellum.saved'));
    }

    public function publish(Post $post): RedirectResponse
    {
        $this->posts->publish($post);

        return redirect()
            ->route('lara-vellum.dashboard.edit', $post)
            ->with('status', __('lara-vellum::vellum.published'));
    }

    public function unpublish(Post $post): RedirectResponse
    {
        $this->posts->unpublish($post);

        return redirect()
            ->route('lara-vellum.dashboard.edit', $post)
            ->with('status', __('lara-vellum::vellum.unpublished'));
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->posts->delete($post);

        return redirect()
            ->route('lara-vellum.dashboard.index')
            ->with('status', __('lara-vellum::vellum.deleted'));
    }

    public function preview(Request $request): Response
    {
        return new Response(
            content: $this->renderer->toHtml((string) $request->input('body', '')),
            headers: ['Content-Type' => 'text/html'],
        );
    }
}
