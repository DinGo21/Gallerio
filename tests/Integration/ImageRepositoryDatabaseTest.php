<?php

declare(strict_types=1);

namespace Gallerio\Tests\Integration;

use Gallerio\Tests\Support\TestDatabase;
use ImageRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

final class ImageRepositoryDatabaseTest extends TestCase
{
    private ?PDO $pdo = null;

    private ImageRepository $repository;

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
        $this->repository = new ImageRepository($this->pdo);
    }

    protected function tearDown(): void
    {
        if ($this->pdo !== null) {
            TestDatabase::reset($this->pdo);
        }
    }

    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function record(array $overrides = []): array
    {
        return array_merge([
            'filename' => 'photo.jpg',
            'title' => 'Photo',
            'description' => 'A photo.',
            'category' => 'Nature',
            'tags' => ['nature'],
        ], $overrides);
    }

    public function testCreatePersistsRecord(): void
    {
        $id = $this->repository->create($this->record([
            'filename' => 'test-photo.jpg',
            'title' => 'Test Photo',
            'tags' => ['one', 'two'],
        ]));

        self::assertGreaterThan(0, $id);

        $rows = $this->pdo->query('SELECT * FROM images')->fetchAll();
        self::assertCount(1, $rows);
        self::assertSame('test-photo.jpg', $rows[0]['filename']);
        self::assertSame('Test Photo', $rows[0]['title']);
        self::assertSame('one, two', $rows[0]['tags']);
    }

    public function testAllReturnsNewestFirst(): void
    {
        $this->repository->create($this->record(['filename' => 'first.jpg']));
        $this->repository->create($this->record(['filename' => 'second.jpg']));

        $images = $this->repository->all();

        self::assertSame('second.jpg', $images[0]['file']);
        self::assertSame('first.jpg', $images[1]['file']);
    }

    public function testTagsAreSplitIntoList(): void
    {
        $this->repository->create($this->record(['tags' => ['one', 'two']]));

        $images = $this->repository->all();

        self::assertSame(['one', 'two'], $images[0]['tags']);
    }

    public function testCategoriesAreDistinctAndSorted(): void
    {
        $this->repository->create($this->record(['filename' => 'a.jpg', 'category' => 'Nature']));
        $this->repository->create($this->record(['filename' => 'b.jpg', 'category' => 'Animals']));
        $this->repository->create($this->record(['filename' => 'c.jpg', 'category' => 'Nature']));

        self::assertSame(['Animals', 'Nature'], $this->repository->categories());
    }

    public function testFilterWorksWithStoredRecords(): void
    {
        $this->repository->create($this->record([
            'filename' => 'forest.jpg',
            'title' => 'Forest',
            'description' => 'Trees and a lake.',
            'category' => 'Nature',
            'tags' => ['autumn'],
        ]));
        $this->repository->create($this->record([
            'filename' => 'wolf.jpg',
            'title' => 'Wolf',
            'description' => 'A wild animal.',
            'category' => 'Animals',
            'tags' => ['forest'],
        ]));

        $images = $this->repository->all();

        self::assertCount(2, $this->repository->filter($images, 'forest'));
        self::assertCount(1, $this->repository->filter($images, 'forest', 'Animals'));
    }
}
