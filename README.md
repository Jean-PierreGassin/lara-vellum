# Lara-Vellum

Laravel meets Vellum: the art of writing, as a Laravel package.

## Requirements

- PHP 8.3+
- Laravel 12

## Installation

```bash
composer require jeanpierregassin/lara-vellum
```

Publish the config file:

```bash
php artisan vendor:publish --tag=lara-vellum-config
```

## Usage

```php
use JeanPierreGassin\LaraVellum\Facades\LaraVellum;

LaraVellum::isEnabled();
```

## Development

```bash
composer install

composer test      # Run the Pest suite
composer analyse   # Run PHPStan (larastan, level 8)
composer lint      # Check coding standards (php-cs-fixer, PER-CS, dry run)
composer format    # Fix coding standards
```

## Contributing

This repository uses a two-branch flow:

- `main` — stable, release-ready.
- `dev` — integration branch for ongoing work.

Both branches are protected: changes land only via pull request, and the
test + coding-standards + static-analysis checks must pass before a merge.
Branch off `dev`, open a PR into `dev`, and promote `dev` into `main` for
releases.

## License

The MIT License (MIT). See [LICENSE.md](LICENSE.md).
