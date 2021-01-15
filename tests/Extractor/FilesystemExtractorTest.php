<?php
declare(strict_types=1);

namespace App\Tests\Extractor;

use App\Extractor\FilesystemExtractor;
use App\Value\Diff;
use App\Value\ManualPage;
use App\Value\Property;
use App\Value\Url;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/** @covers \App\Extractor\FilesystemExtractor */
class FilesystemExtractorTest extends TestCase
{
    const STANDARD_PATH = 'var/tests/src/PHPCompatibility';
    private FilesystemExtractor $extractor;

    protected function setUp(): void
    {
        $this->extractor = new FilesystemExtractor();
    }

    /** @test */
    public function extractManualPage()
    {
        (new Filesystem)->dumpFile(
            self::STANDARD_PATH . '/Sniffs/FunctionUse/MySniff.php',
            '<?php
            /**
             * Docblock summary
             * @link http://link1.com
             */
            class MySniff {
                /** @var bool */
                public $boolProperty = false;
            }
            '
        );

        (new Filesystem)->dumpFile(
            self::STANDARD_PATH . '/Docs/FunctionUse/MySniff.xml',
            '<documentation title="Title">
                <rule_code>FunctionUse.My</rule_code>
                <standard>Description</standard>
                <code_comparison>
                    <code>a();</code>
                    <code>b();</code>
                </code_comparison>
                <links>
                    <link>http://link2.com</link>
                </links>
            </documentation>
            '
        );

        self::assertEquals(
            new ManualPage(
                'FunctionUse.My',
                'Description',
                'Docblock summary',
                [
                    new Diff('a();', 'b();')
                ],
                [
                    new Property('boolProperty', 'bool')
                ],
                [
                    new Url('http://link1.com'),
                    new Url('http://link2.com')
                ]
            ),
            $this->extractor->extractManualPage(self::STANDARD_PATH . '/Sniffs/FunctionUse/MySniff.php')
        );
    }
}
