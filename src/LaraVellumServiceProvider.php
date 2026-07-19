<?php

namespace JeanPierreGassin\LaraVellum;

use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\ServiceProvider;
use JeanPierreGassin\LaraVellum\Contracts\MarkdownRenderer;
use JeanPierreGassin\LaraVellum\Enums\Asset;
use JeanPierreGassin\LaraVellum\Http\RouteRegistrar;
use JeanPierreGassin\LaraVellum\Support\AssetUrlGenerator;
use JeanPierreGassin\LaraVellum\Support\CommonMarkRenderer;
use JeanPierreGassin\LaraVellum\Support\StaticPageGenerator;

class LaraVellumServiceProvider extends ServiceProvider
{
    private const string CONFIG_FILE = 'config/lara-vellum.php';
    private const string VIEWS_DIR = 'resources/views';
    private const string LANG_DIR = 'resources/lang';
    private const string MIGRATIONS_DIR = 'database/migrations';
    private const string VIEW_NAMESPACE = 'lara-vellum';
    private const array ASSET_VIEWS = [
        'lara-vellum::dashboard.layout',
        'lara-vellum::public.layout',
    ];

    public function register(): void
    {
        $this->mergeConfigFrom(
            path: $this->packagePath(self::CONFIG_FILE),
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
            path: $this->packagePath(self::VIEWS_DIR),
            namespace: self::VIEW_NAMESPACE,
        );

        $this->loadTranslationsFrom(
            path: $this->packagePath(self::LANG_DIR),
            namespace: self::VIEW_NAMESPACE,
        );

        $this->loadMigrationsFrom(paths: $this->packagePath(self::MIGRATIONS_DIR));

        $this->shareAssetUrls();
        $this->registerDefaultGate();
        $this->registerRoutes();

        if ($this->app->runningInConsole()) {
            $this->registerPublishing();
        }
    }

    private function registerRoutes(): void
    {
        if (!$this->app->make('config')->get(key: 'lara-vellum.enabled', default: true)) {
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
                $this->packagePath(self::CONFIG_FILE) => $this->app->configPath(path: 'lara-vellum.php'),
            ],
            groups: 'lara-vellum-config',
        );

        $this->publishes(
            paths: [
                $this->packagePath(self::VIEWS_DIR) => $this->app->resourcePath(path: 'views/vendor/lara-vellum'),
            ],
            groups: 'lara-vellum-views',
        );

        $this->publishes(
            paths: [
                $this->packagePath(self::MIGRATIONS_DIR) => $this->app->databasePath(path: 'migrations'),
            ],
            groups: 'lara-vellum-migrations',
        );

        $this->publishes(
            paths: [
                $this->packagePath(self::LANG_DIR) => $this->app->langPath(path: 'vendor/lara-vellum'),
            ],
            groups: 'lara-vellum-lang',
        );
    }

    private function shareAssetUrls(): void
    {
        $this->app->make(ViewFactory::class)->composer(
            views: self::ASSET_VIEWS,
            callback: function (View $view): void {
                $assets = $this->app->make(AssetUrlGenerator::class);

                $view->with(key: 'stylesheetUrl', value: $assets->url(Asset::Css));
                $view->with(key: 'scriptUrl', value: $assets->url(Asset::Js));
            },
        );
    }

    private function packagePath(string $relative): string
    {
        return dirname(__DIR__).'/'.$relative;
    }
}
