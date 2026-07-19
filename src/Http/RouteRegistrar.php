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
            ->get($this->dashboardPath('assets/app.css'), [AssetController::class, 'css'])
            ->middleware($this->publicMiddleware())
            ->name('lara-vellum.assets.css');

        $this->router
            ->get($this->dashboardPath('assets/app.js'), [AssetController::class, 'js'])
            ->middleware($this->publicMiddleware())
            ->name('lara-vellum.assets.js');
    }

    private function registerDashboard(): void
    {
        $this->dashboardGroup()->group(function () {
            $this->router->get('/', [DashboardPostController::class, 'index'])->name('index');
            $this->router->get('create', [DashboardPostController::class, 'create'])->name('create');
            $this->router->post('/', [DashboardPostController::class, 'store'])->name('store');
            $this->router->post('preview', [DashboardPostController::class, 'preview'])->name('preview');
            $this->router->get('{post}/edit', [DashboardPostController::class, 'edit'])->name('edit');
            $this->router->put('{post}', [DashboardPostController::class, 'update'])->name('update');
            $this->router->post('{post}/publish', [DashboardPostController::class, 'publish'])->name('publish');
            $this->router->post('{post}/unpublish', [DashboardPostController::class, 'unpublish'])->name('unpublish');
            $this->router->delete('{post}', [DashboardPostController::class, 'destroy'])->name('destroy');
        });
    }

    private function registerPublic(): void
    {
        $prefix = trim((string) $this->config->get('lara-vellum.routing.public.prefix', ''), '/');
        $middleware = $this->publicMiddleware();

        if ($prefix === '') {
            $this->router
                ->fallback([PublicPostController::class, 'show'])
                ->middleware($middleware)
                ->name('lara-vellum.posts.show');

            return;
        }

        $this->router
            ->get("$prefix/{slug}", [PublicPostController::class, 'show'])
            ->middleware($middleware)
            ->where('slug', '[A-Za-z0-9\-]+')
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
        return (array) $this->config->get('lara-vellum.routing.public.middleware', ['web']);
    }

    /**
     * @return array<int, string>
     */
    private function dashboardMiddleware(): array
    {
        return (array) $this->config->get('lara-vellum.routing.dashboard.middleware', ['web']);
    }

    private function dashboardPrefix(): string
    {
        return trim((string) $this->config->get('lara-vellum.routing.dashboard.prefix', 'vellum'), '/');
    }

    private function dashboardPath(string $path): string
    {
        $prefix = $this->dashboardPrefix();

        return $prefix === '' ? $path : "$prefix/$path";
    }
}
