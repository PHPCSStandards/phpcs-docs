<?php
declare(strict_types=1);

namespace App\Tests\Parser\Exception;

use App\Parser\Exception\NotAViolationPath;
use PHPUnit\Framework\TestCase;

/** @covers \App\Parser\Exception\NotAViolationPath */
class NotAViolationPathTest extends TestCase
{
    /** @test */
    public function fromPath()
    {
        self::assertStringContainsString(
            'The file path provided does not follow the convention for violation documentation.',
            NotAViolationPath::fromPath('INVALID/PATH')->getMessage()
        );
    }
}
