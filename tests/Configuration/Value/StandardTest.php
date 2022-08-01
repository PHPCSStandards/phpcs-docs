<?php
declare(strict_types=1);

namespace App\Tests\Configuration\Value;

use App\Configuration\Value\Standard;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\Configuration\Value\Standard */
class StandardTest extends TestCase
{
    private const STANDARD_PATH = 'path/to/standard';

    /** @test */
    public function construct_WithBlankPath_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Standard('');
    }

    /** @test */
    public function getPath()
    {
        self::assertEquals(
            self::STANDARD_PATH,
            (new Standard(self::STANDARD_PATH))->getPath()
        );
    }
}
