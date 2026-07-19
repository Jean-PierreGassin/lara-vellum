<?php

namespace JeanPierreGassin\LaraVellum\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use JeanPierreGassin\LaraVellum\Repositories\PostRepository;
use JeanPierreGassin\LaraVellum\Services\PostService;

readonly class PublicPostController
{
    public function __construct(
        private PostRepository $posts,
        private PostService $service,
    ) {}

    public function show(Request $request, ?string $slug = null): Response
    {
        $slug ??= trim($request->path(), '/');

        $post = $this->posts->findPublishedBySlug($slug);

        if ($post === null) {
            abort(404);
        }

        return new Response(
            content: $this->service->readStaticPage($post),
            headers: ['Content-Type' => 'text/html'],
        );
    }
}
