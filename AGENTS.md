# AGENTS.md

Gallerio is a single-page image gallery: **vanilla PHP** (no framework) plus
**Bootstrap 5**, backed by **MariaDB**. There is no build step. Composer is used
**only for dev tooling** (PHPUnit); the deployed app has no runtime Composer
dependencies.

## Run and verify

```bash
# Uploads are larger than PHP's 2 MB default.
php -d upload_max_filesize=10M -d post_max_size=12M -S localhost:8000
```

Open http://localhost:8000. PHP must be served over HTTP.

## Docker deployment

The app and MariaDB run as two Compose services; Apache serves on
http://127.0.0.1:8080. Docker files: `Dockerfile`, `docker-compose.yml`,
`docker/php.ini`, `docker/apache.conf`.

```bash
cp .env.example .env             # DB_ROOT_PASSWORD is the only required change
docker compose up -d --build
docker compose exec app php database/seed.php
```

- `docker/entrypoint.sh` makes `images/` writable at startup, so a fresh clone
  needs no manual `chmod` (Docker would otherwise create it as `root:root 0755`).
- `images/` is a bind mount; curated photos and uploads persist on the host.
  The curated photos are tracked in git, while new uploads stay untracked
  because `.gitignore` ignores `images/`.
- DB data lives in the `gallerio_gallerio_db` named volume. `docker compose down`
  keeps it; `docker compose down -v` deletes it.
- `src/config.php` reads `DB_*` from the environment, falling back to the local
  development defaults. Compose sets `DB_HOST=db`.
- `database/schema.sql` is mounted into `/docker-entrypoint-initdb.d/` and runs
  only on first initialisation.
- `docker/apache.conf` denies `src/`, `database/`, `tests/`, `vendor/` and the
  dev metadata files; they must never be publicly reachable.
- `.dockerignore` keeps `vendor/`, `tests/` and Composer files out of the image.

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

## Testing

PHPUnit is a **dev-only** dependency. Integration tests use a separate
`gallerio_test` database and skip automatically when it is unreachable.

```bash
sudo systemctl start mariadb
sudo mariadb < database/schema.test.sql   # once; creates gallerio_test
composer install
composer test                             # all suites
composer test:unit                        # no database required
composer test:integration                 # needs gallerio_test
vendor/bin/phpunit --filter=Upload        # focused run
```

- `tests/Unit/` — helpers, search/filter logic, view escaping, upload validation.
- `tests/Integration/` — real MariaDB CRUD via `gallerio_test`.
- `ImageUploader` accepts injectable file-operation callables so tests avoid real
  HTTP uploads. `ImageRepository` connects lazily, so filter/category unit tests
  need no database.
- `vendor/`, `.phpunit.cache/` and `tests/tmp/` are gitignored.

## Adding a curated image

1. Drop the file into `images/`.
2. Add a metadata entry in `src/data/images.php`, keyed by the **exact file name**.
3. Track the file explicitly: `git add -f images/<name>.jpg` (the folder is
   gitignored so uploads stay untracked).
4. Run `php database/seed.php` (or insert the row manually).

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
- PHPUnit/Composer are dev-only test tooling and are not part of the deployed app.
