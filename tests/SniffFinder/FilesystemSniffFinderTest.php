<?php
declare(strict_types=1);

namespace App\Tests\SniffFinder;

use App\SniffFinder\FilesystemSniffFinder;
use App\Value\Sniff;
use App\Value\Standard;
use App\Value\Urls;
use App\Value\Violation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/** @covers \App\SniffFinder\FilesystemSniffFinder */
class FilesystemSniffFinderTest extends TestCase
{
    private const PHP_SNIFF_PATH = 'var/tests/src/Standard/Sniffs/Category/MySniff.php';
    private const XML_SNIFF_PATH = 'var/tests/src/Standard/Docs/Category/MyStandard.xml';
    private const XML_VIOLATION_PATH = 'var/tests/src/Standard/Docs/Category/MyStandard/ErrorCode.xml';

    private FilesystemSniffFinder $finder;

    protected function setUp(): void
    {
        $this->finder = new FilesystemSniffFinder();
    }

    /** @test */
    public function getSniffs()
    {
        $this->writeSniffPhp();
        $this->writeSniffXml();

        self::assertEquals(
            [
                new Sniff(
                    'Standard.Category.My',
                    '',
                    [],
                    new Urls([]),
                    'Description',
                    [],
                    []
                )
            ],
            iterator_to_array($this->finder->getSniffs(new Standard(
                'var/tests/src/Standard/'
            )))
        );
    }

    /** @test */
    public function getViolations()
    {
        $this->writeViolationXml();

        self::assertEquals(
            [
                new Violation(
                    'Standard.Category.My.ErrorCode',
                    'Description',
                    [],
                    new Urls([])
                )
            ],
            iterator_to_array($this->finder->getViolations(new Standard(
                'var/tests/src/Standard/'
            )))
        );
    }

    private function writeSniffPhp(): void
    {
        $content = '<?php
        class SniffName {}
        ';
        (new Filesystem())->dumpFile(self::PHP_SNIFF_PATH, $content);
    }

    private function writeSniffXml(): void
    {
        $content = <<<XML
        <documentation title="Title">
            <standard>Description</standard>
        </documentation>
        XML;
        (new Filesystem())->dumpFile(self::XML_SNIFF_PATH, $content);
    }

    private function writeViolationXml()
    {
        $content = <<<XML
        <documentation title="Title">
            <standard>Description</standard>
        </documentation>
        XML;
        (new Filesystem())->dumpFile(self::XML_VIOLATION_PATH, $content);
    }
}
