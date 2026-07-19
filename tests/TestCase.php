<?php

namespace JeanPierreGassin\LaraVellum\Tests;

use JeanPierreGassin\LaraVellum\LaraVellumServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LaraVellumServiceProvider::class,
        ];
    }
}
