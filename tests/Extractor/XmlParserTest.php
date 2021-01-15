<?php
declare(strict_types=1);

namespace App\Tests\Extractor;

use App\Extractor\XmlParser;
use App\Value\Diff;
use App\Value\Url;
use App\Value\XmlParts;
use PHPUnit\Framework\TestCase;

/** @covers \App\Extractor\XmlParser */
class XmlParserTest extends TestCase
{
    const XML_FILE_PATH = 'var/tests/MySniff.xml';
    private XmlParser $parser;

    protected function setUp(): void
    {
        $this->parser = new XmlParser();
    }

    /** @test */
    public function getManualParts_WithMinimumTags_CreatePartsObject()
    {
        $content = '
        <documentation title="Title">
            <rule_code>Rule.Code</rule_code>
            <standard>Description</standard>
        </documentation>
';
        file_put_contents(self::XML_FILE_PATH, $content);
        $parts = $this->parser->getManualParts(self::XML_FILE_PATH);
        self::assertEquals(
            new XmlParts(
                'Rule.Code',
                'Description',
                [],
                []
            ),
            $parts
        );
    }

    /** @test */
    public function getManualParts_WithCodeComparisons_AddTrimmedDiffs()
    {
        $content = '
        <documentation title="Title">
            <rule_code>Rule.Code</rule_code>
            <standard>Description</standard>
            <code_comparison>
                <code>
                <![CDATA[
function a() {
}
                ]]>
                </code>
                <code>
                <![CDATA[
function b() {
}
                ]]>
                </code>
            </code_comparison>
            <code_comparison>
                <code>a();</code>
                <code>b();</code>
            </code_comparison>
        </documentation>
';
        file_put_contents(self::XML_FILE_PATH, $content);
        $parts = $this->parser->getManualParts(self::XML_FILE_PATH);
        self::assertEquals(
            [
                new Diff("function a() {\n}", "function b() {\n}"),
                new Diff('a();', 'b();'),
            ],
            $parts->getDiffs()
        );
    }

    /** @test */
    public function getManualParts_WithLinks_AddLinks()
    {
        $content = '
        <documentation title="Title">
            <rule_code>Rule.Code</rule_code>
            <standard>Description</standard>
            <links>
                <link>http://link1.com</link>
                <link>http://link2.com</link>
            </links>
        </documentation>
';
        file_put_contents(self::XML_FILE_PATH, $content);
        $parts = $this->parser->getManualParts(self::XML_FILE_PATH);
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ],
            $parts->getLinks()
        );
    }
}
