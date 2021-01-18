<?php
declare(strict_types=1);

namespace App\Tests\Parser;

use App\Parser\UserDocParser;
use App\Value\Diff;
use App\Value\Url;
use App\Value\UserDoc;
use PHPUnit\Framework\TestCase;

/** @covers \App\Parser\UserDocParser */
class UserDocParserTest extends TestCase
{
    const XML_FILE_PATH = 'var/tests/MySniff.xml';
    private UserDocParser $parser;

    protected function setUp(): void
    {
        $this->parser = new UserDocParser();
    }

    /** @test */
    public function getUserDoc_WithMinimumTags_CreateUserDocObject()
    {
        $content = <<<XML
        <documentation title="Title">
            <rule_code>Rule.Code</rule_code>
            <standard>Description</standard>
        </documentation>
        XML;
        file_put_contents(self::XML_FILE_PATH, $content);
        $doc = $this->parser->getUserDoc(self::XML_FILE_PATH);
        self::assertEquals(
            new UserDoc(
                'Rule.Code',
                'Description',
                [],
                []
            ),
            $doc
        );
    }

    /** @test */
    public function getUserDoc_WithCodeComparisons_AddTrimmedDiffs()
    {
        $content = <<<XML
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
        XML;
        file_put_contents(self::XML_FILE_PATH, $content);
        $doc = $this->parser->getUserDoc(self::XML_FILE_PATH);
        self::assertEquals(
            [
                new Diff("function b() {\n}", "function a() {\n}"),
                new Diff('b();', 'a();'),
            ],
            $doc->getDiffs()
        );
    }

    /** @test */
    public function getUserDoc_WithLinks_AddLinks()
    {
        $content = <<<XML
        <documentation title="Title">
            <rule_code>Rule.Code</rule_code>
            <standard>Description</standard>
            <links>
                <link>http://link1.com</link>
                <link>http://link2.com</link>
            </links>
        </documentation>
        XML;
        file_put_contents(self::XML_FILE_PATH, $content);
        $doc = $this->parser->getUserDoc(self::XML_FILE_PATH);
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ],
            $doc->getLinks()
        );
    }
}
