<?php

declare(strict_types=1);

namespace Gallerio\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class GalleryCardViewTest extends TestCase
{
    public function testRendersFieldsAndEscapesOutput(): void
    {
        $image = [
            'file' => 'forest.jpg',
            'title' => 'Forest <script>alert(1)</script>',
            'description' => 'A <b>calm</b> lake.',
            'category' => 'Nature & Scenery',
            'tags' => ['forest', 'lake'],
        ];

        ob_start();
        require __DIR__ . '/../../src/views/gallery-card.php';
        $html = (string) ob_get_clean();

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
}
