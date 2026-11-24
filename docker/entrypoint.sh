#!/bin/sh
set -e

# images/ is gitignored, so on a fresh clone Docker creates the bind-mount
# source as root:root 0755 and Apache (www-data, uid 33) cannot write uploads.
# Repair the directory before the stock entrypoint starts Apache.
mkdir -p /var/www/html/images
chmod 0777 /var/www/html/images

exec docker-php-entrypoint "$@"
