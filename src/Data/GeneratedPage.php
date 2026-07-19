<?php

namespace JeanPierreGassin\LaraVellum\Data;

final readonly class GeneratedPage
{
    public function __construct(
        public string $hash,
        public string $path,
    ) {}
}
