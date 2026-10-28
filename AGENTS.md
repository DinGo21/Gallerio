# AGENTS.md

Gallerio is a single-page image gallery: **vanilla PHP** (no framework, no Composer)
plus **Bootstrap 5**, backed by **MariaDB**. There is no build step and no test suite.

## Run and verify

```bash
# Uploads are larger than PHP's 2 MB default.
php -d upload_max_filesize=10M -d post_max_size=12M -S localhost:8000
```

Open http://localhost:8000. PHP must be served over HTTP.

## Database setup

MariaDB 11.8; database, user and password are all `gallerio` (dev-only credentials
in `src/config.php`). On a fresh Fedora machine the PHP MySQL driver and the MariaDB
service are **not enabled by default**:

```bash
sudo dnf install -y php-mysqlnd
sudo systemctl enable --now mariadb
sudo mariadb < database/schema.sql
php database/seed.php        # idempotent; imports the 8 curated photos
```

`src/data/images.php` is **seed data only** — it is not read at runtime. The `images`
table is the source of truth. Re-run `php database/seed.php` after editing it.

## Data flow

`index.php` / `upload.php` → `src/init.php` → `ImageRepository` → MariaDB →
`src/views/*` partials.

- Search is **server-side** via `GET /index.php?q=…&category=…`; `assets/js/gallery.js`
  only drives the lightbox. Don't add client-side filtering.
- `ImageRepository::all()` reads `images` newest-first; `filter()` matches the
  normalized `searchable` string (title + description + category + tags + file name).
- Use `Database::connection()` for new PDO access; it returns a shared connection.

## Uploads

- `upload.php` + `src/ImageUploader.php`. JPG/JPEG only, validated with
  `getimagesize()` and `finfo`; files are written to `images/` with a unique slugged
  name, then a row is inserted via `ImageRepository::create()`.
- `images/` must stay writable by the PHP process.

## Adding a curated image

1. Drop the file into `images/`.
2. Add a metadata entry in `src/data/images.php`, keyed by the **exact file name**.
3. Run `php database/seed.php` (or insert the row manually).

## Conventions and quirks

- Always escape output with `e()`; build URLs with `image_url()` / `asset()`.
- `declare(strict_types=1)` is used in `src/*.php` but **not** in `src/views/*.php`.
- All URLs are relative (`images/…`, `assets/…`, `./`) so the app works from a
  sub-directory. `GALLERIO_ROOT` is `dirname(__DIR__)`.
- **Bootstrap is vendored** at `assets/vendor/bootstrap/` on purpose. Do not switch to
  a CDN or npm — the site must work fully offline.
- The local PHP has **no `ext-gd` and no `ext-mbstring`**. Don't rely on image resizing
  or `mb_*` functions. There are no thumbnails; uploads are served full-size.
- `src/data/images.php` is a plain returned array, not a class.

## Constraints from the original spec

- Only Bootstrap may be used for styling/layout; no other external libraries.
- No PHP frameworks, no extra features beyond the initial requirements unless asked.
