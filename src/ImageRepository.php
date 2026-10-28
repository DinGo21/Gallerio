<?php

declare(strict_types=1);

/*
 * Reads gallery images from the MariaDB `images` table.
 *
 * The database is the source of truth for the gallery. The curated metadata in
 * src/data/images.php is only used by database/seed.php to populate the table.
 */

/**
 * A single gallery image.
 *
 * array{
 *     id: int,
 *     file: string,
 *     title: string,
 *     description: string,
 *     category: string,
 *     tags: list<string>,
 *     created_at: string,
 *     searchable: string
 * }
 */
final class ImageRepository
{
    private PDO $pdo;

    public function __construct(?PDO $pdo = null)
    {
        $this->pdo = $pdo ?? Database::connection();
    }

    /**
     * Return every gallery image, newest first.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $statement = $this->pdo->query(
            'SELECT id, filename, title, description, category, tags, created_at
             FROM images
             ORDER BY created_at DESC, id DESC'
        );

        $images = [];
        foreach ($statement->fetchAll() as $row) {
            $images[] = $this->buildRecord($row);
        }

        return $images;
    }

    /**
     * Distinct categories, sorted alphabetically.
     *
     * @param list<array<string, mixed>>|null $images
     * @return list<string>
     */
    public function categories(?array $images = null): array
    {
        $images = $images ?? $this->all();

        $categories = [];
        foreach ($images as $image) {
            $categories[$image['category']] = true;
        }

        $categories = array_keys($categories);
        sort($categories, SORT_STRING);

        return $categories;
    }

    /**
     * Filter images by a free-text query and/or an exact category.
     *
     * The free-text query is matched case-insensitively against the title,
     * description, category, tags and file name.
     *
     * @param list<array<string, mixed>> $images
     * @return list<array<string, mixed>>
     */
    public function filter(array $images, string $query = '', string $category = ''): array
    {
        $query = $this->normalize($query);
        $category = $this->normalize($category);

        $filtered = array_filter(
            $images,
            static function (array $image) use ($query, $category): bool {
                if ($category !== '' && strcasecmp($image['category'], $category) !== 0) {
                    return false;
                }

                if ($query === '') {
                    return true;
                }

                return strpos($image['searchable'], $query) !== false;
            }
        );

        return array_values($filtered);
    }

    /**
     * Insert a new image record.
     *
     * @param array{filename: string, title: string, description: string, category: string, tags: list<string>} $data
     */
    public function create(array $data): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO images (filename, title, description, category, tags)
             VALUES (:filename, :title, :description, :category, :tags)'
        );

        $statement->execute([
            ':filename' => $data['filename'],
            ':title' => $data['title'],
            ':description' => $data['description'],
            ':category' => $data['category'],
            ':tags' => implode(', ', $data['tags']),
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Build a normalized record from a database row.
     *
     * @param array<string, mixed> $row
     * @return array<string, mixed>
     */
    private function buildRecord(array $row): array
    {
        $file = (string) $row['filename'];
        $title = (string) $row['title'];
        $description = (string) $row['description'];
        $category = (string) $row['category'];
        $tags = array_values(array_filter(array_map('trim', explode(',', (string) $row['tags']))));

        return [
            'id' => (int) $row['id'],
            'file' => $file,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'tags' => $tags,
            'created_at' => (string) $row['created_at'],
            'searchable' => $this->normalize(
                implode(' ', array_merge([$title, $description, $category, $file], $tags))
            ),
        ];
    }

    /**
     * Normalize a string for case-insensitive comparisons.
     */
    private function normalize(string $value): string
    {
        return strtolower(trim($value));
    }
}
