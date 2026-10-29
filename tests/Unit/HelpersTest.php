<?php

declare(strict_types=1);

namespace Gallerio\Tests\Unit;

use PHPUnit\Framework\TestCase;

final class HelpersTest extends TestCase
{
    public function testEscapesHtml(): void
    {
        self::assertSame(
            '&lt;script&gt;alert(&#039;x&#039;)&lt;/script&gt;',
            e("<script>alert('x')</script>")
        );
    }

    public function testEscapesNullAsEmptyString(): void
    {
        self::assertSame('', e(null));
    }

    public function testBuildsAssetUrl(): void
    {
        self::assertSame('assets/css/style.css', asset('/css/style.css'));
    }

    public function testBuildsEncodedImageUrl(): void
    {
        self::assertSame('images/my%20photo.jpg', image_url('my photo.jpg'));
    }

    public function testBuildsImagePath(): void
    {
        self::assertSame(GALLERIO_IMAGE_DIR . '/photo.jpg', image_path('photo.jpg'));
    }
}
