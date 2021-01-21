<?php
declare(strict_types=1);

namespace App\Tests\SniffFinder;

use App\SniffFinder\FilesystemSniffFinder;
use App\Value\Folder;
use App\Value\Sniff;
use App\Value\UrlList;
use App\Value\Violation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Traversable;

/** @covers \App\SniffFinder\FilesystemSniffFinder */
class FilesystemSniffFinderTest extends TestCase
{
    private const PHP_SNIFF_PATH = 'var/tests/src/Standard/Sniffs/Category/MySniff.php';
    private const XML_SNIFF_PATH = 'var/tests/src/Standard/Docs/Category/MyStandard.xml';
    private const XML_VIOLATION_PATH = 'var/tests/src/Standard/Docs/Category/MyStandard/ErrorCode.xml';

    private FilesystemSniffFinder $finder;

    /** @test */
    public function getSniffs()
    {
        $this->writeSniffPhp();
        $this->writeSniffXml();
        $this->writeViolationXml();

        /** @var Traversable $sniffs */
        $sniffs = $this->finder->getSniffs(
            new Folder(
                'var/tests/src/Standard/'
            )
        );
        self::assertEquals(
            [
                new Sniff(
                    'Standard.Category.My',
                    '',
                    [],
                    new UrlList([]),
                    'Description',
                    [],
                    [
                        new Violation(
                            'Standard.Category.My.ErrorCode',
                            'Description',
                            [],
                            new UrlList([])
                        )
                    ]
                )
            ],
            iterator_to_array($sniffs)
        );
    }

    private function writeSniffPhp(): void
    {
        $content = '<?php
        namespace Standard\Sniffs\Category;
        class MySniff {}
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

    /** @test */
    public function getSniff()
    {
        $this->writeSniffPhp();
        $this->writeSniffXml();
        $this->writeViolationXml();

        self::assertEquals(
            new Sniff(
                'Standard.Category.My',
                '',
                [],
                new UrlList([]),
                'Description',
                [],
                [
                    new Violation(
                        'Standard.Category.My.ErrorCode',
                        'Description',
                        [],
                        new UrlList([])
                    )
                ]
            ),
            $this->finder->getSniff(
                new Folder(
                    'var/tests/src/Standard/'
                ),
                self::PHP_SNIFF_PATH
            )
        );
    }

    protected function setUp(): void
    {
        $this->finder = new FilesystemSniffFinder();
    }
}
