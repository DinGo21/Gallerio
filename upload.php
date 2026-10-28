<?php

declare(strict_types=1);

/*
 * Upload page controller.
 *
 * Handles the multipart form submission, validates/stores the JPG and redirects
 * back to the gallery on success.
 */

require_once __DIR__ . '/src/init.php';

$pageTitle = 'Upload an image';
$pageDescription = 'Add a JPG photograph to the Gallerio collection.';

$errors = [];
$databaseError = null;
$categories = [];
$repository = null;
$values = [
    'title' => '',
    'description' => '',
    'category' => '',
    'tags' => '',
];

try {
    $repository = new ImageRepository();
    $categories = $repository->categories();
} catch (PDOException $exception) {
    $databaseError = 'The image database is unavailable. Start MariaDB and try again.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $databaseError === null) {
    $values = [
        'title' => trim((string) ($_POST['title'] ?? '')),
        'description' => trim((string) ($_POST['description'] ?? '')),
        'category' => trim((string) ($_POST['category'] ?? '')),
        'tags' => trim((string) ($_POST['tags'] ?? '')),
    ];

    $uploader = new ImageUploader($repository);
    $result = $uploader->handle($_FILES['image'] ?? [], $values);

    if ($result['success']) {
        header('Location: index.php?uploaded=1');
        exit;
    }

    $errors = $result['errors'];
}

require __DIR__ . '/src/views/header.php';
require __DIR__ . '/src/views/upload-form.php';
require __DIR__ . '/src/views/footer.php';
