<?php
declare(strict_types=1);

namespace App\Tests\Parser;

use App\Parser\ViolationParser;
use App\Value\Diff;
use App\Value\Url;
use App\Value\Violation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/** @covers \App\Parser\ViolationParser */
class ViolationParserTest extends TestCase
{
    const XML_FILE_PATH = 'var/tests/Standard/Docs/Category/SniffName/ErrorCode.xml';
    private ViolationParser $parser;

    /** @test */
    public function parse_WithMinimumTags_CreateUserDocObject()
    {
        $content = <<<XML
        <documentation title="Title">
            <standard>Description</standard>
        </documentation>
        XML;
        (new Filesystem())->dumpFile(self::XML_FILE_PATH, $content);
        self::assertEquals(
            new Violation(
                'Standard.Category.SniffName.ErrorCode',
                'Description',
                [],
                []
            ),
            $this->parser->parse(self::XML_FILE_PATH)
        );
    }

    /** @test */
    public function parse_WithCodeComparisons_AddTrimmedDiffs()
    {
        $content = <<<XML
        <documentation title="Title">
            <standard>Description</standard>
            <code_comparison>
                <code>
                <![CDATA[
        function b() {
        }
                ]]>
                </code>
                <code>
                <![CDATA[
        function a() {
        }
                ]]>
                </code>
            </code_comparison>
            <code_comparison>
                <code>b();</code>
                <code>a();</code>
            </code_comparison>
        </documentation>
        XML;
        file_put_contents(self::XML_FILE_PATH, $content);
        self::assertEquals(
            [
                new Diff("function a() {\n}", "function b() {\n}"),
                new Diff('a();', 'b();'),
            ],
            $this->parser->parse(self::XML_FILE_PATH)->getDiffs()
        );
    }

    /** @test */
    public function parse_WithLinks_AddLinks()
    {
        $content = <<<XML
        <documentation title="Title">
            <standard>Description</standard>
            <links>
                <link>http://link1.com</link>
                <link>http://link2.com</link>
            </links>
        </documentation>
        XML;
        file_put_contents(self::XML_FILE_PATH, $content);
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ],
            $this->parser->parse(self::XML_FILE_PATH)->getLinks()
        );
    }

    protected function setUp(): void
    {
        $this->parser = new ViolationParser();
    }
}
