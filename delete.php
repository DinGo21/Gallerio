<?php

declare(strict_types=1);

/*
 * Deletes an uploaded image.
 *
 * Curated photographs are tracked in src/data/images.php and restored by the
 * seeding step, so they are protected from deletion. Only POST requests are
 * accepted, and the file name comes from the database rather than the request.
 */

require_once __DIR__ . '/src/init.php';

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo 'Method Not Allowed';
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    header('Location: index.php?delete_error=invalid');
    exit;
}

try {
    $repository = new ImageRepository();
    $image = $repository->find($id);

    if ($image === null) {
        header('Location: index.php?delete_error=missing');
        exit;
    }

    if (is_curated_image($image['file'])) {
        header('Location: index.php?delete_error=curated');
        exit;
    }

    $repository->delete($id);

    $path = image_path($image['file']);
    if (is_file($path)) {
        @unlink($path);
    }
} catch (PDOException $exception) {
    header('Location: index.php?delete_error=database');
    exit;
}

header('Location: index.php?deleted=1');
exit;
