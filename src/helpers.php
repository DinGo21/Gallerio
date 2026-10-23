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
