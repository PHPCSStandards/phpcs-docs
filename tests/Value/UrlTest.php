<?php
declare(strict_types=1);

namespace App\Tests\Value;

use App\Value\Url;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\Value\Url */
class UrlTest extends TestCase
{
    /** @test */
    public function constructor_WithInvalidUrlString_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Url('INVALID.com');
    }

    /** @test */
    public function getName_WithTextAfterUrl_ReturnTextName()
    {
        $url = new Url('https://link.com Name with spaces');
        self::assertEquals(
            'Name with spaces',
            $url->getName()
        );
    }

    /** @test */
    public function getUrl_WithUrlOnly_ReturnsUrl()
    {
        $url = new Url('https://link.com');
        self::assertEquals(
            'https://link.com',
            $url->getUrl()
        );
    }

    /** @test */
    public function getName_WithUrlOnly_ReturnsUrl()
    {
        $url = new Url('https://link.com');
        self::assertEquals(
            'https://link.com',
            $url->getName()
        );
    }
}
