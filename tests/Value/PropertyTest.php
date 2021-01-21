<?php
declare(strict_types=1);

namespace App\Tests\Value;

use App\Value\Property;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\Value\Property */
class PropertyTest extends TestCase
{
    const PATH = 'path/to/folder/';
    const NAME = 'name';
    const TYPE = 'int|null';
    const DESCRIPTION = 'Description';

    /** @test */
    public function constructor_WithBlankNAME_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Property('', self::TYPE, self::DESCRIPTION);
    }

    /** @test */
    public function getName_()
    {
        self::assertEquals(
            self::NAME,
            $this->createValidProperty()->getName()
        );
    }

    /** @test */
    public function getType()
    {
        self::assertEquals(
            self::TYPE,
            $this->createValidProperty()->getType()
        );
    }

    /** @test */
    public function getDescription()
    {
        self::assertEquals(
            self::DESCRIPTION,
            $this->createValidProperty()->getDescription()
        );
    }

    private function createValidProperty(): Property
    {
        return new Property(self::NAME, self::TYPE, self::DESCRIPTION);
    }
}
