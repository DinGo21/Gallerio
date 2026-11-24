#!/bin/sh
set -e

# images/ is gitignored, so on a fresh clone Docker creates the bind-mount
# source as root:root 0755 and Apache (www-data, uid 33) cannot write uploads.
# Repair the directory before the stock entrypoint starts Apache.
mkdir -p /var/www/html/images
chmod 0777 /var/www/html/images

# Seed the curated metadata on every start. seed.php upserts by file name, so
# running it repeatedly is safe and keeps rows in sync with src/data/images.php.
# Retry while MariaDB finishes starting, then let Apache run regardless.
if [ "${GALLERIO_SKIP_SEED:-0}" != "1" ]; then
    attempt=0
    until php /var/www/html/database/seed.php; do
        attempt=$((attempt + 1))
        if [ "$attempt" -ge 30 ]; then
            echo "gallerio: database unavailable, skipping seed" >&2
            break
        fi
        echo "gallerio: waiting for database (${attempt}/30)..."
        sleep 2
    done
fi

exec docker-php-entrypoint "$@"
