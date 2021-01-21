<?php
declare(strict_types=1);

namespace App\Tests\Value;

use App\Value\Url;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\Value\Url */
class UrlTest extends TestCase
{
    const URL = 'http://link.com';

    /** @test */
    public function constructor_WithInvalidUrlString_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Url('INVALID.com');
    }

    /** @test */
    public function getUrl()
    {
        self::assertEquals(
            self::URL,
            (string)(new Url(self::URL))
        );
    }
}
