<?php

declare(strict_types=1);

/*
 * Gallerio configuration.
 *
 * Application-wide settings live here so that paths and options can be
 * changed in a single place as the project grows.
 */

// Display name and marketing copy.
define('GALLERIO_NAME', 'Gallerio');
define('GALLERIO_TAGLINE', 'A small gallery of curated photographs.');
define(
    'GALLERIO_DESCRIPTION',
    'Gallerio is a curated image gallery. Browse the collection, search by keyword or category, and open any photograph in the lightbox.'
);

// Absolute path to the project root (the directory that holds index.php).
define('GALLERIO_ROOT', dirname(__DIR__));

// Directory that stores the curated source images.
define('GALLERIO_IMAGE_DIR', GALLERIO_ROOT . '/images');

// File extensions that are treated as gallery images.
define('GALLERIO_IMAGE_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// Database connection (development credentials).
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3306);
define('DB_NAME', 'gallerio');
define('DB_USER', 'gallerio');
define('DB_PASS', 'gallerio');

// Uploaded files must be JPG/JPEG and no larger than this.
define('GALLERIO_UPLOAD_EXTENSIONS', ['jpg', 'jpeg']);
define('GALLERIO_MAX_UPLOAD_BYTES', 10 * 1024 * 1024);
