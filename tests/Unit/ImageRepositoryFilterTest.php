<?php

declare(strict_types=1);

namespace Gallerio\Tests\Unit;

use ImageRepository;
use PHPUnit\Framework\TestCase;

final class ImageRepositoryFilterTest extends TestCase
{
    /**
     * @return list<array<string, mixed>>
     */
    private function images(): array
    {
        return [
            [
                'id' => 1,
                'file' => 'forest.jpg',
                'title' => 'Forest Reflection',
                'description' => 'A calm lake in autumn.',
                'category' => 'Nature',
                'tags' => ['forest', 'lake', 'autumn'],
                'created_at' => '2026-01-01 00:00:00',
                'searchable' => 'forest reflection a calm lake in autumn. nature forest.jpg forest lake autumn',
            ],
            [
                'id' => 2,
                'file' => 'wolf.jpg',
                'title' => 'The Wolf',
                'description' => 'A wolf in the forest.',
                'category' => 'Animals',
                'tags' => ['wolf', 'wildlife'],
                'created_at' => '2026-01-02 00:00:00',
                'searchable' => 'the wolf a wolf in the forest. animals wolf.jpg wolf wildlife',
            ],
            [
                'id' => 3,
                'file' => 'budapest.jpg',
                'title' => 'Budapest at Dusk',
                'description' => 'The Danube at night.',
                'category' => 'Travel',
                'tags' => ['budapest', 'danube'],
                'created_at' => '2026-01-03 00:00:00',
                'searchable' => 'budapest at dusk the danube at night. travel budapest.jpg budapest danube',
            ],
        ];
    }

    public function testEmptyQueryReturnsEverything(): void
    {
        $result = (new ImageRepository())->filter($this->images());

        self::assertCount(3, $result);
    }

    public function testMatchesTitle(): void
    {
        $result = (new ImageRepository())->filter($this->images(), 'Budapest');

        self::assertCount(1, $result);
        self::assertSame('Budapest at Dusk', $result[0]['title']);
    }

    public function testMatchesDescription(): void
    {
        $result = (new ImageRepository())->filter($this->images(), 'danube');

        self::assertCount(1, $result);
        self::assertSame('Budapest at Dusk', $result[0]['title']);
    }

    public function testMatchesTags(): void
    {
        $result = (new ImageRepository())->filter($this->images(), 'autumn');

        self::assertCount(1, $result);
        self::assertSame('Forest Reflection', $result[0]['title']);
    }

    public function testMatchesCategory(): void
    {
        $result = (new ImageRepository())->filter($this->images(), 'animals');

        self::assertCount(1, $result);
        self::assertSame('The Wolf', $result[0]['title']);
    }

    public function testMatchesFilename(): void
    {
        $result = (new ImageRepository())->filter($this->images(), 'budapest.jpg');

        self::assertCount(1, $result);
        self::assertSame('Budapest at Dusk', $result[0]['title']);
    }

    public function testIsCaseInsensitive(): void
    {
        $lower = (new ImageRepository())->filter($this->images(), 'forest');
        $upper = (new ImageRepository())->filter($this->images(), 'FOREST');

        self::assertSame(
            array_column($lower, 'id'),
            array_column($upper, 'id')
        );
        self::assertCount(2, $lower);
    }

    public function testFiltersByExactCategory(): void
    {
        $result = (new ImageRepository())->filter($this->images(), '', 'Nature');

        self::assertCount(1, $result);
        self::assertSame('Forest Reflection', $result[0]['title']);
    }

    public function testCategoryMatchIsCaseInsensitive(): void
    {
        $result = (new ImageRepository())->filter($this->images(), '', 'nature');

        self::assertCount(1, $result);
    }

    public function testCombinesQueryAndCategory(): void
    {
        $result = (new ImageRepository())->filter($this->images(), 'forest', 'Animals');

        self::assertCount(1, $result);
        self::assertSame('The Wolf', $result[0]['title']);
    }

    public function testReturnsEmptyWhenNothingMatches(): void
    {
        self::assertSame([], (new ImageRepository())->filter($this->images(), 'zzz'));
        self::assertSame([], (new ImageRepository())->filter($this->images(), '', 'Unknown'));
    }

    public function testReindexesResults(): void
    {
        $result = (new ImageRepository())->filter($this->images(), '', 'Animals');

        self::assertArrayHasKey(0, $result);
        self::assertArrayNotHasKey(1, $result);
    }

    public function testCategoriesAreDistinctAndSorted(): void
    {
        $categories = (new ImageRepository())->categories($this->images());

        self::assertSame(['Animals', 'Nature', 'Travel'], $categories);
    }
}
