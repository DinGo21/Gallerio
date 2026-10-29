<?php

declare(strict_types=1);

namespace Gallerio\Tests\Unit;

use ImageRepository;
use ImageUploader;
use PDOException;
use PHPUnit\Framework\TestCase;

final class ImageUploaderValidationTest extends TestCase
{
    private string $directory;

    private string $fixture;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/gallerio-upload-' . bin2hex(random_bytes(4));
        mkdir($this->directory, 0777, true);
        $this->fixture = __DIR__ . '/../fixtures/sample.jpg';
    }

    protected function tearDown(): void
    {
        foreach (glob($this->directory . '/*') ?: [] as $file) {
            @unlink($file);
        }

        @rmdir($this->directory);
    }

    private function uploader(?ImageRepository $repository = null): ImageUploader
    {
        return new ImageUploader(
            $repository ?? new ImageRepository(),
            $this->directory,
            static fn (string $path): bool => true,
            static fn (string $from, string $to): bool => copy($from, $to)
        );
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function file(array $overrides = []): array
    {
        return array_merge([
            'name' => 'photo.jpg',
            'type' => 'image/jpeg',
            'tmp_name' => $this->fixture,
            'error' => UPLOAD_ERR_OK,
            'size' => filesize($this->fixture),
        ], $overrides);
    }

    /**
     * @param array<string, string> $overrides
     * @return array<string, string>
     */
    private function input(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Test Photo',
            'description' => 'A test photo.',
            'category' => 'Tests',
            'tags' => 'one, two',
        ], $overrides);
    }

    public function testRejectsMissingFile(): void
    {
        $result = $this->uploader()->handle(
            $this->file(['error' => UPLOAD_ERR_NO_FILE, 'tmp_name' => '', 'size' => 0]),
            $this->input()
        );

        self::assertFalse($result['success']);
        self::assertContains('Choose a JPG image to upload.', $result['errors']);
    }

    public function testRejectsNonJpgExtension(): void
    {
        $result = $this->uploader()->handle(
            $this->file(['name' => 'photo.png']),
            $this->input()
        );

        self::assertFalse($result['success']);
        self::assertContains('Only JPG and JPEG images can be uploaded.', $result['errors']);
    }

    public function testRejectsFileThatIsNotAJpeg(): void
    {
        $result = $this->uploader()->handle(
            $this->file(['tmp_name' => __DIR__ . '/../fixtures/not-an-image.jpg']),
            $this->input()
        );

        self::assertFalse($result['success']);
        self::assertContains('The file is not a valid JPG image.', $result['errors']);
    }

    public function testRejectsOversizedFile(): void
    {
        $result = $this->uploader()->handle(
            $this->file(['size' => GALLERIO_MAX_UPLOAD_BYTES + 1]),
            $this->input()
        );

        self::assertFalse($result['success']);
        self::assertContains('The image must be smaller than 10 MB.', $result['errors']);
    }

    public function testRejectsMissingTitle(): void
    {
        $result = $this->uploader()->handle(
            $this->file(),
            $this->input(['title' => ''])
        );

        self::assertFalse($result['success']);
        self::assertContains('A title is required.', $result['errors']);
    }

    public function testRejectsTitleLongerThan255Characters(): void
    {
        $result = $this->uploader()->handle(
            $this->file(),
            $this->input(['title' => str_repeat('a', 256)])
        );

        self::assertFalse($result['success']);
        self::assertContains('The title must be 255 characters or fewer.', $result['errors']);
    }

    public function testRemovesStoredFileWhenDatabaseInsertFails(): void
    {
        $repository = $this->createMock(ImageRepository::class);
        $repository->expects(self::once())
            ->method('create')
            ->willThrowException(new PDOException('database unavailable'));

        $result = $this->uploader($repository)->handle($this->file(), $this->input());

        self::assertFalse($result['success']);
        self::assertContains('The image could not be saved to the database.', $result['errors']);
        self::assertSame([], glob($this->directory . '/*') ?: []);
    }
}
