<?php

declare(strict_types=1);

namespace Gallerio\Tests\Integration;

use Gallerio\Tests\Support\TestDatabase;
use ImageRepository;
use ImageUploader;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

final class ImageUploaderStorageTest extends TestCase
{
    private ?PDO $pdo = null;

    private string $directory;

    private string $fixture;

    protected function setUp(): void
    {
        try {
            $this->pdo = TestDatabase::connect();
        } catch (PDOException $exception) {
            $this->markTestSkipped(
                'MariaDB test database "gallerio_test" is not available: ' . $exception->getMessage()
            );
        }

        TestDatabase::reset($this->pdo);

        $this->directory = sys_get_temp_dir() . '/gallerio-upload-' . bin2hex(random_bytes(4));
        mkdir($this->directory, 0777, true);
        $this->fixture = __DIR__ . '/../fixtures/sample.jpg';
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null) {
            TestDatabase::reset($this->pdo);
        }

        if (isset($this->directory)) {
            foreach (glob($this->directory . '/*') ?: [] as $file) {
                @unlink($file);
            }

            @rmdir($this->directory);
        }
    }

    private function uploader(): ImageUploader
    {
        return new ImageUploader(
            new ImageRepository($this->pdo),
            $this->directory,
            static fn (string $path): bool => true,
            static fn (string $from, string $to): bool => copy($from, $to)
        );
    }

    /**
     * @param array<string, string> $input
     * @return array{success: bool, errors: list<string>, filename: ?string}
     */
    private function upload(array $input): array
    {
        return $this->uploader()->handle(
            [
                'name' => 'photo.jpg',
                'type' => 'image/jpeg',
                'tmp_name' => $this->fixture,
                'error' => UPLOAD_ERR_OK,
                'size' => filesize($this->fixture),
            ],
            $input
        );
    }

    public function testStoresValidUpload(): void
    {
        $result = $this->upload([
            'title' => 'My Great Photo!',
            'description' => 'A test photo.',
            'category' => 'Tests',
            'tags' => ' one, two , one, ',
        ]);

        self::assertTrue($result['success']);
        self::assertNotNull($result['filename']);
        self::assertStringStartsWith('my-great-photo-', $result['filename']);
        self::assertStringEndsWith('.jpg', $result['filename']);
        self::assertFileExists($this->directory . '/' . $result['filename']);

        $rows = $this->pdo->query('SELECT * FROM images')->fetchAll();
        self::assertCount(1, $rows);
        self::assertSame('My Great Photo!', $rows[0]['title']);
        self::assertSame('Tests', $rows[0]['category']);
        self::assertSame('one, two', $rows[0]['tags']);
    }

    public function testUsesUncategorisedWhenCategoryIsEmpty(): void
    {
        $result = $this->upload([
            'title' => 'No Category',
            'description' => '',
            'category' => '',
            'tags' => '',
        ]);

        self::assertTrue($result['success']);

        $rows = $this->pdo->query('SELECT * FROM images')->fetchAll();
        self::assertSame('Uncategorised', $rows[0]['category']);
    }
}
