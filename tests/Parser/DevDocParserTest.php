<?php
declare(strict_types=1);

namespace App\Tests\Parser;

use App\Parser\DevDocParser;
use App\Value\Property;
use App\Value\Url;
use PHPUnit\Framework\TestCase;

/** @covers \App\Parser\DevDocParser */
class DevDocParserTest extends TestCase
{
    const PHP_FILE_PATH = 'var/tests/MySniff.php';
    private DevDocParser $parser;

    protected function setUp(): void
    {
        $this->parser = new DevDocParser();
    }

    /** @test */
    public function getDevDoc_WithDocblockSummary_AddSummaryOnly()
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
        $doc = $this->parser->getDevDoc(self::PHP_FILE_PATH);
        self::assertEquals(
            "Summary\nLine 2",
            $doc->getDocblock()
        );
    }

    /** @test */
    public function getDevDoc_WithProperties_AddPublicOnly()
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
        $doc = $this->parser->getDevDoc(self::PHP_FILE_PATH);
        self::assertEquals(
            [
                new Property('boolProperty', 'bool'),
                new Property('stringProperty', 'string'),
                new Property('mixedProperty', 'mixed'),
                new Property('unionProperty', 'string|null'),
            ],
            $doc->getProperties()
        );
    }

    /** @test */
    public function getDevDoc_WithMultipleLinks_AddLinks()
    {
        $content = '<?php
        /**
         * @link http://link1.com
         * @link http://link2.com
         */
        class MySniff {}
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->getDevDoc(self::PHP_FILE_PATH);
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ],
            $doc->getLinks()
        );
    }
}
