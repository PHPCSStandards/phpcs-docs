<?php
declare(strict_types=1);

namespace App\Tests\Parser\Exception;

use App\Parser\Exception\NotASniffPath;
use PHPUnit\Framework\TestCase;

/** @covers \App\Parser\Exception\NotASniffPath */
class NotASniffPathTest extends TestCase
{
    /** @test */
    public function fromPath()
    {
        self::assertStringContainsString(
            'The file path provided does not follow the convention for a sniff class.',
            NotASniffPath::fromPath('INVALID/PATH')->getMessage()
        );
    }
}
