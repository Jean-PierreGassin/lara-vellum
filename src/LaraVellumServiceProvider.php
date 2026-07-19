<?php

namespace JeanPierreGassin\LaraVellum;

use Illuminate\Support\ServiceProvider;

class LaraVellumServiceProvider extends ServiceProvider
{
    private const string CONFIG_PATH = __DIR__.'/../config/lara-vellum.php';

    public function register(): void
    {
        $this->mergeConfigFrom(
            path: self::CONFIG_PATH,
            key: 'lara-vellum',
        );

        $this->app->singleton(abstract: LaraVellum::class);
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes(
            paths: [
                self::CONFIG_PATH => $this->app->configPath(path: 'lara-vellum.php'),
            ],
            groups: 'lara-vellum-config',
        );
    }
}
