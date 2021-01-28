<?php
declare(strict_types=1);

namespace App\Tests\Configuration\Value;

use App\Configuration\Value\Configuration;
use App\Configuration\Value\Source;
use PHPUnit\Framework\TestCase;

/** @covers \App\Configuration\Value\Configuration */
class ConfigurationTest extends TestCase
{
    private const FORMAT = 'markdown';
    /**
     * @var Source[]
     */
    private static array $SOURCES;

    /** @test */
    public function getSources()
    {
        self::assertEquals(
            self::$SOURCES,
            $this->createValidVO()->getSources(),
        );
    }

    /** @test */
    public function getFormat()
    {
        self::assertEquals(
            self::FORMAT,
            $this->createValidVO()->getFormat(),
        );
    }

    private function createValidVO(): Configuration
    {
        return new Configuration(
            self::FORMAT,
            self::$SOURCES
        );
    }

    public static function setUpBeforeClass(): void
    {
        self::$SOURCES = [
            new Source('path/to/source', [])
        ];
    }
}
