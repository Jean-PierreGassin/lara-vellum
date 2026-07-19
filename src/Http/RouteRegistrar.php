<?php

namespace JeanPierreGassin\LaraVellum\Http;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Routing\RouteRegistrar as FluentRegistrar;
use JeanPierreGassin\LaraVellum\Http\Controllers\AssetController;
use JeanPierreGassin\LaraVellum\Http\Controllers\DashboardPostController;
use JeanPierreGassin\LaraVellum\Http\Controllers\PublicPostController;
use JeanPierreGassin\LaraVellum\Http\Middleware\Authorize;

readonly class RouteRegistrar
{
    public function __construct(
        private Router $router,
        private Repository $config,
    ) {}

    public function register(): void
    {
        $this->registerAssets();
        $this->registerDashboard();
        $this->registerPublic();
    }

    private function registerAssets(): void
    {
        $this->router
            ->get(uri: $this->dashboardPath('assets/app.css'), action: [AssetController::class, 'css'])
            ->middleware($this->publicMiddleware())
            ->name('lara-vellum.assets.css');

        $this->router
            ->get(uri: $this->dashboardPath('assets/app.js'), action: [AssetController::class, 'js'])
            ->middleware($this->publicMiddleware())
            ->name('lara-vellum.assets.js');
    }

    private function registerDashboard(): void
    {
        $this->dashboardGroup()->group(function () {
            $this->router->get(uri: '/', action: [DashboardPostController::class, 'index'])->name('index');
            $this->router->get(uri: 'create', action: [DashboardPostController::class, 'create'])->name('create');
            $this->router->post(uri: '/', action: [DashboardPostController::class, 'store'])->name('store');
            $this->router->post(uri: 'preview', action: [DashboardPostController::class, 'preview'])->name('preview');
            $this->router->get(uri: '{post}/edit', action: [DashboardPostController::class, 'edit'])->name('edit');
            $this->router->put(uri: '{post}', action: [DashboardPostController::class, 'update'])->name('update');
            $this->router->post(uri: '{post}/publish', action: [DashboardPostController::class, 'publish'])->name('publish');
            $this->router->post(uri: '{post}/unpublish', action: [DashboardPostController::class, 'unpublish'])->name('unpublish');
            $this->router->delete(uri: '{post}', action: [DashboardPostController::class, 'destroy'])->name('destroy');
        });
    }

    private function registerPublic(): void
    {
        $prefix = trim((string) $this->config->get(key: 'lara-vellum.routing.public.prefix', default: ''), '/');
        $middleware = $this->publicMiddleware();

        if ($prefix === '') {
            $this->router
                ->fallback([PublicPostController::class, 'show'])
                ->middleware($middleware)
                ->name('lara-vellum.posts.show');

            return;
        }

        $this->router
            ->get(uri: "$prefix/{slug}", action: [PublicPostController::class, 'show'])
            ->middleware($middleware)
            ->where(name: 'slug', expression: '[A-Za-z0-9\-]+')
            ->name('lara-vellum.posts.show');
    }

    private function dashboardGroup(): FluentRegistrar
    {
        return $this->router
            ->middleware([...$this->dashboardMiddleware(), Authorize::class])
            ->prefix($this->dashboardPrefix())
            ->as('lara-vellum.dashboard.');
    }

    /**
     * @return array<int, string>
     */
    private function publicMiddleware(): array
    {
        return (array) $this->config->get(key: 'lara-vellum.routing.public.middleware', default: ['web']);
    }

    /**
     * @return array<int, string>
     */
    private function dashboardMiddleware(): array
    {
        return (array) $this->config->get(key: 'lara-vellum.routing.dashboard.middleware', default: ['web']);
    }

    private function dashboardPrefix(): string
    {
        return trim((string) $this->config->get(key: 'lara-vellum.routing.dashboard.prefix', default: 'vellum'), '/');
    }

    private function dashboardPath(string $path): string
    {
        $prefix = $this->dashboardPrefix();

        return $prefix === '' ? $path : "$prefix/$path";
    }
}
