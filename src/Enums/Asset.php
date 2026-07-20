<?php

namespace JeanPierreGassin\LaraVellum\Enums;

enum Asset: string
{
    case Css = 'vellum.css';
    case Js = 'vellum.js';

    private const string DIST_DIR = 'resources/dist';

    public function routeName(): string
    {
        return match ($this) {
            self::Css => 'lara-vellum.assets.css',
            self::Js => 'lara-vellum.assets.js',
        };
    }

    public function contentType(): string
    {
        return match ($this) {
            self::Css => 'text/css',
            self::Js => 'text/javascript',
        };
    }

    public function path(): string
    {
        return dirname(__DIR__, 2).'/'.self::DIST_DIR.'/'.$this->value;
    }
}
