<?php

namespace JeanPierreGassin\LaraVellum\Facades;

use Illuminate\Support\Facades\Facade;
use JeanPierreGassin\LaraVellum\LaraVellum as LaraVellumManager;

/**
 * @method static bool isEnabled()
 *
 * @see \JeanPierreGassin\LaraVellum\LaraVellum
 */
class LaraVellum extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return LaraVellumManager::class;
    }
}
