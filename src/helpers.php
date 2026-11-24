<?php

declare(strict_types=1);

/*
 * Small, dependency-free helpers shared by the views and the front controller.
 */

/**
 * Escape a value for safe output inside HTML.
 */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Build the public URL of a file inside the assets/ directory.
 */
function asset(string $path): string
{
    return 'assets/' . ltrim($path, '/');
}

/**
 * Like asset(), but append the file modification time as a version query so
 * browsers pick up stylesheet and script changes after a deploy.
 */
function asset_versioned(string $path): string
{
    $url = asset($path);
    $file = GALLERIO_ROOT . '/assets/' . ltrim($path, '/');
    $modified = @filemtime($file);

    return $modified === false ? $url : $url . '?v=' . $modified;
}

/**
 * Build the public URL of a gallery image.
 */
function image_url(string $file): string
{
    return 'images/' . rawurlencode($file);
}

/**
 * Build the filesystem path of a gallery image.
 */
function image_path(string $file): string
{
    return GALLERIO_IMAGE_DIR . '/' . $file;
}

/**
 * Whether a file belongs to the curated seed data.
 *
 * Curated files are protected from deletion because database/seed.php restores
 * them on every application start.
 */
function is_curated_image(string $file): bool
{
    return array_key_exists($file, gallerio_image_data());
}
