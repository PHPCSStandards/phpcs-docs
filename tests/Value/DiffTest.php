<?php
declare(strict_types=1);

namespace App\Tests\Value;

use App\Value\Diff;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\Value\Diff */
class DiffTest extends TestCase
{
    const BEFORE = 'a();';
    const AFTER = 'b();';

    /** @test */
    public function constructor_WithEmptyValues_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Diff('', '');
    }

    /** @test */
    public function getBefore()
    {
        self::assertEquals(
            self::BEFORE,
            $this->createValidDiff()->getBefore(),
        );
    }

    /**
     * @return Diff
     */
    private function createValidDiff(): Diff
    {
        return new Diff(self::BEFORE, self::AFTER);
    }

    /** @test */
    public function getAfter()
    {
        self::assertEquals(
            self::AFTER,
            $this->createValidDiff()->getAfter(),
        );
    }
}
