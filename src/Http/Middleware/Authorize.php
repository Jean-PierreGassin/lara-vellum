<?php

namespace JeanPierreGassin\LaraVellum\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class Authorize
{
    public function __construct(
        private Gate $gate,
        private Repository $config,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        $ability = $this->config->get('lara-vellum.authorization.gate');

        if (empty($ability)) {
            return $next($request);
        }

        $this->gate->authorize($ability);

        return $next($request);
    }
}
