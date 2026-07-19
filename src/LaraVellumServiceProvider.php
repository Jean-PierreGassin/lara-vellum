<?php

namespace JeanPierreGassin\LaraVellum;

use Illuminate\Support\ServiceProvider;

class LaraVellumServiceProvider extends ServiceProvider
{
    private const string CONFIG_PATH = __DIR__.'/../config/lara-vellum.php';

    public function register(): void
    {
        $this->mergeConfigFrom(self::CONFIG_PATH, 'lara-vellum');

        $this->app->singleton(LaraVellum::class);
    }

    public function boot(): void
    {
        if (!$this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            self::CONFIG_PATH => $this->app->configPath('lara-vellum.php'),
        ], 'lara-vellum-config');
    }
}
