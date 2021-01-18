<?php
declare(strict_types=1);

namespace App\Tests\Parser;

use App\Parser\SniffParser;
use App\Value\Property;
use App\Value\Url;
use PHPUnit\Framework\TestCase;

/** @covers \App\Parser\SniffParser */
class SniffParserTest extends TestCase
{
    const PHP_FILE_PATH = 'var/tests/Standard/Sniffs/Category/SniffName.php';
    private SniffParser $parser;

    /** @test */
    public function parse_WithValidPath_AddCode()
    {
        $content = '<?php
        /**
         * Summary
         *
         * Description
         *
         * @since 1.0.0
         */
        class SniffName {}
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH);
        self::assertEquals(
            'Standard.Category.SniffName',
            $doc->getCode()
        );
    }

    /** @test */
    public function parse_WithDocblockSummaryAndDescription_AddAllText()
    {
        $content = '<?php
        /**
         * Summary
         *
         * Description
         *
         * @since 1.0.0
         */
        class SniffName {}
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH);
        self::assertEquals(
            "Summary\n\nDescription",
            $doc->getDocblock()
        );
    }

    /** @test */
    public function parse_WithDocblockSummary_AddSummary()
    {
        $content = '<?php
        /**
         * Summary
         */
        class SniffName {}
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH);
        self::assertEquals(
            'Summary',
            $doc->getDocblock()
        );
    }

    /** @test */
    public function parse_WithProperties_AddPublicOnly()
    {
        $content = '<?php
        class SniffName {
            /** @var bool */
            public $boolProperty = false;
            public string $stringProperty = "";
            public $mixedProperty = false;
            /** @var string|null Description */
            public string $unionProperty = null;
            private $privateProperty;
        }
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH);
        self::assertEquals(
            [
                new Property('boolProperty', 'bool', ''),
                new Property('stringProperty', 'string', ''),
                new Property('mixedProperty', 'mixed', ''),
                new Property('unionProperty', 'string|null', 'Description'),
            ],
            $doc->getProperties()
        );
    }

    /** @test */
    public function parse_WithMultipleLinks_AddLinks()
    {
        $content = '<?php
        /**
         * @link http://link1.com
         * @link http://link2.com
         */
        class SniffName {}
        ';

        file_put_contents(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH);
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ],
            $doc->getLinks()
        );
    }

    protected function setUp(): void
    {
        $this->parser = new SniffParser();
    }
}
