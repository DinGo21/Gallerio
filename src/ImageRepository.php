<?php

declare(strict_types=1);

/*
 * Loads the curated images from disk and merges them with the metadata
 * defined in src/data/images.php.
 *
 * Keeping the loading logic in a single class makes it easy to swap the data
 * source later (for example, a database) without touching the views.
 */

/**
 * A single gallery image.
 *
 * array{
 *     file: string,
 *     title: string,
 *     description: string,
 *     category: string,
 *     tags: list<string>,
 *     searchable: string
 * }
 */
final class ImageRepository
{
    /** @var string */
    private string $directory;

    /** @var list<string> */
    private array $extensions;

    /** @var array<string, array{title: string, description: string, category: string, tags: list<string>}> */
    private array $metadata;

    /**
     * @param list<string>|null $extensions
     */
    public function __construct(?string $directory = null, ?array $extensions = null)
    {
        $this->directory = $directory ?? GALLERIO_IMAGE_DIR;
        $this->extensions = $extensions ?? GALLERIO_IMAGE_EXTENSIONS;
        $this->metadata = gallerio_image_data();
    }

    /**
     * Return every gallery image, ordered by file name for stable output.
     *
     * @return list<array<string, mixed>>
     */
    public function all(): array
    {
        $images = [];
        foreach ($this->filenames() as $file) {
            $images[] = $this->buildRecord($file);
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
     * Scan the image directory and return the supported file names.
     *
     * @return list<string>
     */
    private function filenames(): array
    {
        if (!is_dir($this->directory)) {
            return [];
        }

        $extensions = array_map('strtolower', $this->extensions);
        $files = [];

        foreach (scandir($this->directory) ?: [] as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            if (!is_file($this->directory . '/' . $entry)) {
                continue;
            }

            $extension = strtolower(pathinfo($entry, PATHINFO_EXTENSION));
            if (!in_array($extension, $extensions, true)) {
                continue;
            }

            $files[] = $entry;
        }

        sort($files, SORT_STRING);

        return $files;
    }

    /**
     * Build a normalized record for one image file.
     *
     * @return array<string, mixed>
     */
    private function buildRecord(string $file): array
    {
        $meta = $this->metadata[$file] ?? [];

        $title = trim((string) ($meta['title'] ?? ''));
        if ($title === '') {
            $title = $this->titleFromFilename($file);
        }

        $description = trim((string) ($meta['description'] ?? ''));

        $category = trim((string) ($meta['category'] ?? ''));
        if ($category === '') {
            $category = 'Uncategorised';
        }

        $tags = array_values(array_filter(array_map('trim', (array) ($meta['tags'] ?? []))));

        return [
            'file' => $file,
            'title' => $title,
            'description' => $description,
            'category' => $category,
            'tags' => $tags,
            'searchable' => $this->normalize(
                implode(' ', array_merge([$title, $description, $category, $file], $tags))
            ),
        ];
    }

    /**
     * Derive a readable title from a file name, e.g.
     * "wolfgang_hasselmann-desert-10463843_1920" becomes "Wolfgang Hasselmann Desert".
     */
    private function titleFromFilename(string $file): string
    {
        $name = pathinfo($file, PATHINFO_FILENAME);

        // Drop trailing size/ID groups such as "-10124031_1920".
        $name = preg_replace('/([_-]\d+)+$/', '', $name) ?? $name;
        $name = str_replace(['-', '_'], ' ', $name);
        $name = preg_replace('/\s+/', ' ', $name) ?? $name;

        return ucwords(trim($name));
    }

    /**
     * Normalize a string for case-insensitive comparisons.
     */
    private function normalize(string $value): string
    {
        return strtolower(trim($value));
    }
}
