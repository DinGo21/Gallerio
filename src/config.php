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
