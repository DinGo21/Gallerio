<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

/*
 * Validates an uploaded JPG, stores it inside the images directory and
 * records it in the database through ImageRepository.
 */
final class ImageUploader
{
    private ImageRepository $repository;

    private string $directory;

    /** @var callable(string): bool */
    private $isUploadedFile;

    /** @var callable(string, string): bool */
    private $moveUploadedFile;

    /**
     * The file-operation callables are injectable so tests can avoid relying on
     * real HTTP uploads; production keeps the native functions.
     */
    public function __construct(
        ImageRepository $repository,
        ?string $directory = null,
        ?callable $isUploadedFile = null,
        ?callable $moveUploadedFile = null
    ) {
        $this->repository = $repository;
        $this->directory = $directory ?? GALLERIO_IMAGE_DIR;
        $this->isUploadedFile = $isUploadedFile ?? 'is_uploaded_file';
        $this->moveUploadedFile = $moveUploadedFile ?? 'move_uploaded_file';
    }

    /**
     * Validate and store an upload.
     *
     * @param array<string, mixed> $file  One entry from $_FILES.
     * @param array<string, string> $input The submitted text fields.
     * @return array{success: bool, errors: list<string>, filename: ?string}
     */
    public function handle(array $file, array $input): array
    {
        $errors = $this->validateInput($input);

        $fileError = $this->validateFile($file);
        if ($fileError !== null) {
            $errors[] = $fileError;
        }

        if ($errors !== []) {
            return ['success' => false, 'errors' => $errors, 'filename' => null];
        }

        $title = trim($input['title'] ?? '');
        $description = trim($input['description'] ?? '');
        $category = trim($input['category'] ?? '');
        if ($category === '') {
            $category = 'Uncategorised';
        }

        $tags = $this->parseTags($input['tags'] ?? '');
        $filename = $this->uniqueFilename($title);
        $destination = $this->directory . '/' . $filename;

        if (!($this->moveUploadedFile)((string) $file['tmp_name'], $destination)) {
            return [
                'success' => false,
                'errors' => ['The image could not be saved. Check that the images directory is writable.'],
                'filename' => null,
            ];
        }

        try {
            $this->repository->create([
                'filename' => $filename,
                'title' => $title,
                'description' => $description,
                'category' => $category,
                'tags' => $tags,
            ]);
        } catch (PDOException $exception) {
            @unlink($destination);

            return [
                'success' => false,
                'errors' => ['The image could not be saved to the database.'],
                'filename' => null,
            ];
        }

        return ['success' => true, 'errors' => [], 'filename' => $filename];
    }

    /**
     * @param array<string, string> $input
     * @return list<string>
     */
    private function validateInput(array $input): array
    {
        $errors = [];

        $title = trim($input['title'] ?? '');
        if ($title === '') {
            $errors[] = 'A title is required.';
        } elseif (strlen($title) > 255) {
            $errors[] = 'The title must be 255 characters or fewer.';
        }

        if (strlen(trim($input['category'] ?? '')) > 100) {
            $errors[] = 'The category must be 100 characters or fewer.';
        }

        if (strlen(trim($input['tags'] ?? '')) > 255) {
            $errors[] = 'The tags must be 255 characters or fewer.';
        }

        return $errors;
    }

    /**
     * @param array<string, mixed> $file
     */
    private function validateFile(array $file): ?string
    {
        $error = (int) ($file['error'] ?? UPLOAD_ERR_NO_FILE);

        if ($error === UPLOAD_ERR_NO_FILE) {
            return 'Choose a JPG image to upload.';
        }

        if ($error === UPLOAD_ERR_INI_SIZE || $error === UPLOAD_ERR_FORM_SIZE) {
            return 'The image is larger than the server upload limit.';
        }

        if ($error !== UPLOAD_ERR_OK) {
            return 'The upload failed. Please try again.';
        }

        if ((int) ($file['size'] ?? 0) <= 0) {
            return 'The uploaded file is empty.';
        }

        if ((int) ($file['size'] ?? 0) > GALLERIO_MAX_UPLOAD_BYTES) {
            return sprintf(
                'The image must be smaller than %d MB.',
                (int) (GALLERIO_MAX_UPLOAD_BYTES / 1024 / 1024)
            );
        }

        $extension = strtolower(pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION));
        if (!in_array($extension, GALLERIO_UPLOAD_EXTENSIONS, true)) {
            return 'Only JPG and JPEG images can be uploaded.';
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        if (!($this->isUploadedFile)($tmp)) {
            return 'The upload failed. Please try again.';
        }

        $info = @getimagesize($tmp);
        if ($info === false || $info[2] !== IMAGETYPE_JPEG) {
            return 'The file is not a valid JPG image.';
        }

        if (class_exists('finfo')) {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mime = $finfo->file($tmp);

            if ($mime !== false && !in_array($mime, ['image/jpeg', 'image/pjpeg'], true)) {
                return 'The file is not a valid JPG image.';
            }
        }

        return null;
    }

    /**
     * @return list<string>
     */
    private function parseTags(string $tags): array
    {
        $parts = array_map('trim', explode(',', $tags));
        $parts = array_filter($parts, static fn (string $tag): bool => $tag !== '');

        return array_values(array_unique($parts));
    }

    /**
     * Build a readable, collision-free file name inside the images directory.
     */
    private function uniqueFilename(string $title): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^a-z0-9]+/i', '-', $title), '-'));
        $slug = trim((string) preg_replace('/-+/', '-', $slug), '-');

        if ($slug === '') {
            $slug = 'upload';
        }

        $slug = substr($slug, 0, 60);

        do {
            $filename = $slug . '-' . bin2hex(random_bytes(4)) . '.jpg';
        } while (file_exists($this->directory . '/' . $filename));

        return $filename;
    }
}
