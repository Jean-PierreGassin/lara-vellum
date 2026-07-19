<?php

namespace JeanPierreGassin\LaraVellum;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\ServiceProvider;
use JeanPierreGassin\LaraVellum\Contracts\MarkdownRenderer;
use JeanPierreGassin\LaraVellum\Http\RouteRegistrar;
use JeanPierreGassin\LaraVellum\Support\CommonMarkRenderer;
use JeanPierreGassin\LaraVellum\Support\StaticPageGenerator;

class LaraVellumServiceProvider extends ServiceProvider
{
    private const string CONFIG_PATH = __DIR__.'/../config/lara-vellum.php';

    private const string VIEWS_PATH = __DIR__.'/../resources/views';

    private const string LANG_PATH = __DIR__.'/../resources/lang';

    private const string MIGRATIONS_PATH = __DIR__.'/../database/migrations';

    private const string VIEW_NAMESPACE = 'lara-vellum';

    public function register(): void
    {
        $this->mergeConfigFrom(
            path: self::CONFIG_PATH,
            key: 'lara-vellum',
        );

        $this->app->bind(
            abstract: MarkdownRenderer::class,
            concrete: CommonMarkRenderer::class,
        );

        $this->app->singleton(abstract: StaticPageGenerator::class);
        $this->app->singleton(abstract: LaraVellum::class);
    }

    public function boot(): void
    {
        $this->loadViewsFrom(
            path: self::VIEWS_PATH,
            namespace: self::VIEW_NAMESPACE,
        );

        $this->loadTranslationsFrom(
            path: self::LANG_PATH,
            namespace: self::VIEW_NAMESPACE,
        );

        $this->loadMigrationsFrom(paths: self::MIGRATIONS_PATH);

        $this->registerDefaultGate();
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    private function registerRoutes(): void
    {
        if (!$this->app->make('config')->get('lara-vellum.enabled', true)) {
            return;
        }

        $this->app->make(RouteRegistrar::class)->register();
    }

    private function registerDefaultGate(): void
    {
        $ability = $this->app->make('config')->get('lara-vellum.authorization.gate');

        if (empty($ability)) {
            return;
        }

        $gate = $this->app->make(Gate::class);

        if ($gate->has($ability)) {
            return;
        }

        $gate->define($ability, fn(?Authenticatable $user): bool => $user !== null);
    }

    private function registerPublishing(): void
    {
        $this->publishes(
            paths: [
                self::CONFIG_PATH => $this->app->configPath(path: 'lara-vellum.php'),
            ],
            groups: 'lara-vellum-config',
        );

        $this->publishes(
            paths: [
                self::VIEWS_PATH => $this->app->resourcePath(path: 'views/vendor/lara-vellum'),
            ],
            groups: 'lara-vellum-views',
        );

        $this->publishes(
            paths: [
                self::MIGRATIONS_PATH => $this->app->databasePath(path: 'migrations'),
            ],
            groups: 'lara-vellum-migrations',
        );

        $this->publishes(
            paths: [
                self::LANG_PATH => $this->app->langPath(path: 'vendor/lara-vellum'),
            ],
            groups: 'lara-vellum-lang',
        );
    }
}
