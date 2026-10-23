# AGENTS.md

Gallerio is a single-page image gallery: **vanilla PHP** (no framework, no Composer)
plus **Bootstrap 5**. There is no build step, no package manager, and no test suite.

## Run and verify

```bash
php -S localhost:8000      # from the project root; open http://localhost:8000
```

PHP must be served over HTTP — there is nothing to install or compile first.

## Data flow

`index.php` (front controller) → `src/init.php` → `src/ImageRepository` →
`src/views/*` partials.

- Search is **server-side** via `GET /index.php?q=…&category=…`; `assets/js/gallery.js`
  only drives the lightbox. Don't add client-side filtering.
- `ImageRepository::all()` scans `images/`; `filter()` matches the normalized
  `searchable` string (title + description + category + tags + file name).

## Adding an image

1. Drop the file into `images/` (allowed extensions: `GALLERIO_IMAGE_EXTENSIONS`).
2. Optionally add a metadata entry in `src/data/images.php`, keyed by the **exact
   file name**. Titles/descriptions/categories/tags are optional; without an entry a
   title is derived from the file name and the category falls back to `Uncategorised`.

## Conventions and quirks

- Always escape output with `e()`; build URLs with `image_url()` / `asset()`.
- `declare(strict_types=1)` is used in `src/*.php` but **not** in `src/views/*.php`.
- All URLs are relative (`images/…`, `assets/…`, `./`) so the app works from a
  sub-directory. `GALLERIO_ROOT` is `dirname(__DIR__)`.
- **Bootstrap is vendored** at `assets/vendor/bootstrap/` on purpose. Do not switch to
  a CDN or npm — the site must work fully offline.
- The local PHP has **no `ext-gd` and no `ext-mbstring`**. Don't rely on image resizing
  (`imagecreate*`, `getimagesize` for resizing) or `mb_*` functions.
- `src/data/images.php` is a plain returned array, not a class.

## Constraints from the original spec

- Only Bootstrap may be used for styling/layout; no other external libraries.
- No PHP frameworks, no extra features beyond the initial requirements unless asked.
