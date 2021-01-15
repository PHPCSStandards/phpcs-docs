<?php
declare(strict_types=1);

namespace App\Tests\Extractor;

use App\Extractor\PhpParser;
use App\Value\Property;
use App\Value\Url;
use PHPUnit\Framework\TestCase;

/** @covers \App\Extractor\PhpParser */
class PhpParserTest extends TestCase
{
    const PHP_FILE_PATH = 'var/tests/MySniff.php';
    private PhpParser $parser;

    protected function setUp(): void
    {
        $this->parser = new PhpParser();
    }

    /** @test */
    public function getManualParts_WithDocblockSummary_AddSummaryOnly()
    {
        $content = '<?php
        /**
         * Summary
         * Line 2
         * @since 1.0.0
         */
        class MySniff {}
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $parts = $this->parser->getManualParts(self::PHP_FILE_PATH);
        self::assertEquals(
            "Summary\nLine 2",
            $parts->getDocblock()
        );
    }

    /** @test */
    public function getManualParts_WithProperties_AddPublicOnly()
    {
        $content = '<?php
        class MySniff {
            /** @var bool */
            public $boolProperty = false;
            public string $stringProperty = "";
            public $mixedProperty = false;
            /** @var string|null */
            public string $unionProperty = null;
            private $privateProperty;
        }
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $parts = $this->parser->getManualParts(self::PHP_FILE_PATH);
        self::assertEquals(
            [
                new Property('boolProperty', 'bool'),
                new Property('stringProperty', 'string'),
                new Property('mixedProperty', 'mixed'),
                new Property('unionProperty', 'string|null'),
            ],
            $parts->getProperties()
        );
    }

    /** @test */
    public function getManualParts_WithMultipleLinks_AddLinks()
    {
        $content = '<?php
        /**
         * @link http://link1.com
         * @link http://link2.com
         */
        class MySniff {}
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $parts = $this->parser->getManualParts(self::PHP_FILE_PATH);
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ],
            $parts->getLinks()
        );
    }
}
