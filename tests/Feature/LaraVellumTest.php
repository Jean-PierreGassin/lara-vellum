<?php

use JeanPierreGassin\LaraVellum\Facades\LaraVellum;
use JeanPierreGassin\LaraVellum\LaraVellum as LaraVellumManager;

it('resolves the manager from the container', function () {
    expect(app(LaraVellumManager::class))->toBeInstanceOf(LaraVellumManager::class);
});

it('is enabled by default', function () {
    expect(LaraVellum::isEnabled())->toBeTrue();
});

it('reflects the enabled config flag', function () {
    config()->set(
        key: 'lara-vellum.enabled',
        value: false,
    );

    expect(LaraVellum::isEnabled())->toBeFalse();
});
