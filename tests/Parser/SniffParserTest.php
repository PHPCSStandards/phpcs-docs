<?php
declare(strict_types=1);

namespace App\Tests\Parser;

use App\Parser\Exception\NotASniffPath;
use App\Parser\SniffParser;
use App\Value\Diff;
use App\Value\Property;
use App\Value\Url;
use PHPUnit\Framework\TestCase;
use Roave\BetterReflection\BetterReflection;
use Roave\BetterReflection\SourceLocator\Ast\Locator;
use Roave\BetterReflection\SourceLocator\Type\StringSourceLocator;
use Symfony\Component\Filesystem\Filesystem;

/** @covers \App\Parser\SniffParser */
class SniffParserTest extends TestCase
{
    private const PHP_FILE_PATH = 'var/tests/src/Standard/Sniffs/Category/MySniff.php';
    private const XML_FILE_PATH = 'var/tests/src/Standard/Docs/Category/MyStandard.xml';
    private const PHP_FILE_PATH_MIXED_SLASHES = 'var\tests\src\Standard/Sniffs/Category\MySniff.php';

    private SniffParser $parser;
    private Locator $astLocator;

    /** @test */
    public function parse_WithValidPath_AddCode()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         * Summary
         *
         * Description
         *
         * @since 1.0.0
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            'Standard.Category.My',
            $doc->getCode()
        );
    }

    /** @test */
    public function parse_WithEmptyDocBlock_AddEmptyDescription()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            '',
            $doc->getDocblock()
        );
    }

    /** @test */
    public function parse_WithDocblockSummaryAndDescription_AddAllText()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         * Summary
         *
         * Description
         *
         * @since 1.0.0
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            "Summary\n\nDescription",
            $doc->getDocblock()
        );
    }

    /** @test */
    public function parse_WithDocblockSummary_AddSummary()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         * Summary
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            'Summary',
            $doc->getDocblock()
        );
    }

    /** @test */
    public function parse_WithProperties_AddPublicOnly()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        class MySniff {
            /** @var bool */
            public $boolProperty = false;
            /** */
            public string $stringProperty = "";
            public $mixedProperty = false;
            /** @var string|null Description */
            public string $unionProperty = null;
            private $privateProperty;
        }
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
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
    public function parse_WithMultipleUrls_AddUrls()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         * @link http://link1.com
         * @link http://link2.com
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ],
            $doc->getUrls()->toArray()
        );
    }

    /** @test */
    public function parse_WithXmlDocs_AddProperties()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         * Summary
         *
         * Description
         *
         * @since 1.0.0
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            'Standard.Category.My',
            $doc->getCode()
        );
    }

    /** @test */
    public function parse_WithCodeComparison_AddDiff()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);

        $content = <<<XML
        <documentation title="Title">
            <standard>Description</standard>
            <code_comparison>
                <code>b();</code>
                <code>a();</code>
            </code_comparison>
        </documentation>
        XML;
        (new Filesystem())->dumpFile(self::XML_FILE_PATH, $content);

        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            [
                new Diff('a();', 'b();')
            ],
            $doc->getDiffs()
        );
    }

    /** @test */
    public function parse_WithXmlUrls_MergeUrls()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         * @link http://link1.com
         * @link http://link3.com
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);

        $content = <<<XML
        <documentation title="Title">
            <standard>Description</standard>
            <link>http://link1.com</link>
            <link>http://link2.com</link>
        </documentation>
        XML;
        (new Filesystem())->dumpFile(self::XML_FILE_PATH, $content);

        $doc = $this->parser->parse(self::PHP_FILE_PATH, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link3.com'),
                new Url('http://link2.com')
            ],
            $doc->getUrls()->toArray()
        );
    }

    /** @test */
    public function parse_WithWindowsSlashesInPhpPath()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         * Summary
         *
         * Description
         *
         * @since 1.0.0
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);
        $doc = $this->parser->parse(str_replace('/', '\\', self::PHP_FILE_PATH), new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            'Standard.Category.My',
            $doc->getCode()
        );
    }

    /** @test */
    public function parse_WithMixedSlashesInPhpPath()
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        /**
         * Summary
         *
         * Description
         *
         * @since 1.0.0
         */
        class MySniff {}
        ';

        (new Filesystem())->dumpFile(self::PHP_FILE_PATH, $content);

        $doc = $this->parser->parse(self::PHP_FILE_PATH_MIXED_SLASHES, new StringSourceLocator($content, $this->astLocator));
        self::assertEquals(
            'Standard.Category.My',
            $doc->getCode()
        );
    }

    /** @test */
    public function parse_WithInvalidPhpPath_ThrowException()
    {
        $content = '<?php';
        $invalidPath = 'var/tests/src/INVALID_PATH/MySniff.php';
        (new Filesystem())->dumpFile($invalidPath, $content);

        $this->expectException(NotASniffPath::class);
        $this->parser->parse($invalidPath, new StringSourceLocator($content, $this->astLocator));
    }

    protected function setUp(): void
    {
        $this->parser = new SniffParser();
        $this->astLocator = (new BetterReflection())->astLocator();
    }
}
