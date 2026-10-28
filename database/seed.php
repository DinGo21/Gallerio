<?php

declare(strict_types=1);

/*
 * Imports the curated metadata from src/data/images.php into the database.
 *
 * Safe to run repeatedly: existing rows are updated by file name.
 *
 *   php database/seed.php
 */

require_once __DIR__ . '/../src/init.php';

try {
    $pdo = Database::connection();
} catch (PDOException $exception) {
    fwrite(STDERR, 'Could not connect to MariaDB: ' . $exception->getMessage() . PHP_EOL);
    exit(1);
}

$rows = [];

foreach (gallerio_image_data() as $filename => $metadata) {
    if (!is_file(image_path($filename))) {
        fwrite(STDERR, 'Skipping missing file: ' . $filename . PHP_EOL);
        continue;
    }

    $rows[] = [
        'filename' => $filename,
        'title' => $metadata['title'],
        'description' => $metadata['description'],
        'category' => $metadata['category'],
        'tags' => implode(', ', $metadata['tags']),
    ];
}

if ($rows === []) {
    fwrite(STDOUT, 'Nothing to seed.' . PHP_EOL);
    exit(0);
}

$statement = $pdo->prepare(
    'INSERT INTO images (filename, title, description, category, tags)
     VALUES (:filename, :title, :description, :category, :tags)
     ON DUPLICATE KEY UPDATE
         title = VALUES(title),
         description = VALUES(description),
         category = VALUES(category),
         tags = VALUES(tags)'
);

foreach ($rows as $row) {
    $statement->execute($row);
}

fwrite(STDOUT, sprintf('Seeded %d images.%s', count($rows), PHP_EOL));
