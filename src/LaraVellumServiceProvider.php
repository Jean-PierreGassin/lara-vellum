<?php

namespace JeanPierreGassin\LaraVellum;

use Illuminate\Support\ServiceProvider;
use JeanPierreGassin\LaraVellum\Contracts\MarkdownRenderer;
use JeanPierreGassin\LaraVellum\Support\CommonMarkRenderer;
use JeanPierreGassin\LaraVellum\Support\StaticPageGenerator;

class LaraVellumServiceProvider extends ServiceProvider
{
    private const string CONFIG_PATH = __DIR__.'/../config/lara-vellum.php';

    private const string VIEWS_PATH = __DIR__.'/../resources/views';

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

        $this->loadMigrationsFrom(paths: self::MIGRATIONS_PATH);

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
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
    }
}
