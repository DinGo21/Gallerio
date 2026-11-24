FROM php:8.5-apache

# MariaDB driver. The rest of what the app needs (fileinfo, getimagesize) is
# already part of the base image; no GD is required.
RUN docker-php-ext-install pdo_mysql

# Upload limits and runtime settings.
COPY docker/php.ini /usr/local/etc/php/conf.d/gallerio.ini

# Deny access to application internals that live above the document root files.
COPY docker/apache.conf /etc/apache2/conf-enabled/gallerio.conf

# Repair the images bind mount before Apache drops privileges to www-data.
COPY docker/entrypoint.sh /usr/local/bin/gallerio-entrypoint
RUN chmod +x /usr/local/bin/gallerio-entrypoint

# Production image: no vendor/, tests/ or Composer metadata (see .dockerignore).
COPY . /var/www/html

WORKDIR /var/www/html

ENTRYPOINT ["gallerio-entrypoint"]
CMD ["apache2-foreground"]
