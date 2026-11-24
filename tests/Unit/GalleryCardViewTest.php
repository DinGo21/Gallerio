<?php

declare(strict_types=1);

namespace Gallerio\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class GalleryCardViewTest extends TestCase
{
    /**
     * @param array<string, mixed> $overrides
     * @return array<string, mixed>
     */
    private function image(array $overrides = []): array
    {
        return array_merge([
            'id' => 42,
            'file' => 'forest.jpg',
            'title' => 'Forest <script>alert(1)</script>',
            'description' => 'A <b>calm</b> lake.',
            'category' => 'Nature & Scenery',
            'tags' => ['forest', 'lake'],
        ], $overrides);
    }

    private function render(array $image): string
    {
        ob_start();
        require __DIR__ . '/../../src/views/gallery-card.php';

        return (string) ob_get_clean();
    }

    public function testRendersFieldsAndEscapesOutput(): void
    {
        $html = $this->render($this->image());

        self::assertStringContainsString('href="images/forest.jpg"', $html);
        self::assertStringContainsString(
            'data-title="Forest &lt;script&gt;alert(1)&lt;/script&gt;"',
            $html
        );
        self::assertStringContainsString('data-category="Nature &amp; Scenery"', $html);
        self::assertStringContainsString('data-tags="forest, lake"', $html);
        self::assertStringContainsString('&lt;b&gt;calm&lt;/b&gt;', $html);
        self::assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function testUploadedImageHasDeleteAction(): void
    {
        $html = $this->render($this->image());

        self::assertStringContainsString('action="delete.php"', $html);
        self::assertStringContainsString('name="id" value="42"', $html);
        self::assertStringContainsString('data-confirm="Delete', $html);
    }

    public function testCuratedImageHasNoDeleteAction(): void
    {
        $html = $this->render($this->image([
            'file' => 'wolfgang_hasselmann-desert-10463843_1920.jpg',
        ]));

        self::assertStringNotContainsString('delete.php', $html);
    }
}
