# Vellum Writing Platform — Roadmap

Zero-config, extendible Laravel writing platform. Install, hit `/vellum`, draft and publish
beautifully simple posts. Auto-integrates with the host app's auth. Provides a public API for
modular extension (scheduling, theme editor, etc.). Tailwind (latest).

## Locked decisions

- **Frontend:** Blade + vanilla JS (no Livewire/Inertia/Alpine). Tailwind, latest.
- **Content:** Markdown authored + stored in the DB (`posts.body`).
- **Static rendering:** On publish, Markdown is rendered to a full HTML page written to a
  **content-hashed generated file** on a configured disk (e.g. `vellum/pages/{hash}.html`).
  The hash is persisted on the post (`content_hash`); the HTML is **not** stored in a DB column.
  Public reads stream the static file (regenerated if missing). Stale hash files are pruned.
- **Auth:** Configurable authorization gate/callback; defaults to the host app's `auth` guard +
  an overridable `Gate::define('viewVellum', ...)`.
- **Zero-config:** Ship a precompiled Tailwind stylesheet so no JS/CSS build step is required.

## Vertical slices

1. **Content domain + static generation** — config, `posts` migration, `Post` model,
   `PostStatus` enum, Markdown renderer, hashed static-file generator + pruning. (this session)
2. **Public reading experience** — routes, controller streaming static HTML, beautiful Tailwind
   reading theme, draft 404s.
3. **`/vellum` dashboard** — auth gate integration, CRUD + publish/unpublish, Blade dashboard,
   vanilla-JS Markdown editor with live preview.
4. **Extension API** — domain events, an extension registry (nav items / dashboard panels),
   documented public manager + facade surface.
5. **Polish / DX** — asset publishing, precompiled Tailwind build, README, upgrade notes.

## Conventions (inherited from the package)

- Named arguments everywhere; `readonly` where applicable.
- PHPStan larastan level 8 clean; PER-CS via php-cs-fixer.
- Pest via Orchestra Testbench. `composer test`, `composer analyse`, `composer lint`.
- Commit prefixes: `Change / Fix / Chore / Refactor` with bullet bodies.
