## Objective

Establish the content domain and static-rendering core: a `Post` model backed by a `posts` table
storing Markdown, plus a service that renders a published post to a content-hashed static HTML file
on a configured disk.

## Requirements

- Expand `config/lara-vellum.php` with: route prefix, middleware, disk name, static path,
  auth gate name, and table name — all env-overridable, all with sane zero-config defaults.
- `posts` table: `id`, nullable `author_id`, `title`, unique `slug`, nullable `excerpt`,
  `body` (Markdown, longtext), `status` (string, default `draft`), nullable `content_hash`,
  nullable `published_at`, `meta` (json, nullable), timestamps, soft deletes.
- `PostStatus` string-backed enum: `Draft`, `Published`.
- `Post` model: fillable/casts, `PostStatus` cast, `published_at` datetime cast, `meta` array
  cast, automatic slug generation on create (unique), `published`/`draft` query scopes,
  `isPublished()` helper.
- Markdown rendering via Laravel's `Str::markdown()` (CommonMark, already a Laravel dep) behind a
  `MarkdownRenderer` contract so it is swappable.
- `StaticPageGenerator`: renders a post to a full HTML page, computes a content hash, writes to
  `{static path}/{hash}.html` on the configured disk, returns the hash. Prunes the previous hash
  file when content changes. `forget()` removes a post's static file (unpublish/delete).
- Bind services in the provider; publish the migration.

## Acceptance Criteria

- Migration creates the `posts` table with all columns + indexes (unique slug, status,
  published_at).
- Creating a `Post` without a slug derives a unique slug from the title.
- `PostStatus` round-trips through the DB and casts on the model.
- `StaticPageGenerator::generate()` writes a `{hash}.html` file whose name changes when the
  post's rendered content changes, prunes the old file, and stores the hash on the post.
- `StaticPageGenerator::forget()` deletes the file.
- `composer test`, `composer analyse`, and `composer lint` all pass.

## Plan Artifact

N/A — proceeding to implementation this session at the user's explicit request (no artifact grill).

## Task Completion Checklist

### Phase 1: Planning

- [x] Requirements captured
- [x] Technical approach documented
- [x] Locked decisions recorded in OVERVIEW.md
- [x] User approval obtained (architecture answers given)

### Phase 2: Implementation

- [x] Expand config with prefix/middleware/disk/static-path/gate/table
- [x] `posts` migration
- [x] `PostStatus` enum
- [x] `Post` model (casts, slug, scopes)
- [x] `MarkdownRenderer` contract + default implementation
- [x] `StaticPageGenerator` (generate/forget/prune)
- [x] Register bindings + publish groups in the service provider

### Phase 3: Validation

- [x] Pest feature tests for model, enum, generator (18 passed, 25 assertions)
- [x] `composer test` passing
- [x] `composer analyse` passing (PHPStan level 8, no errors)
- [x] `composer lint` passing (php-cs-fixer, 0 fixable)

### Phase 4: Completion

- [x] Self-review (added forget-null + draft-scope branch coverage)
- [ ] Commit on feature branch
