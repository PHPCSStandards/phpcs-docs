<?php
declare(strict_types=1);

namespace App\Tests\Handler;

use App\CodeRepository\CodeRepository;
use App\Configuration\ConfigurationRepository;
use App\Configuration\Value\Configuration;
use App\Configuration\Value\Source;
use App\Configuration\Value\Standard;
use App\Generator\Generator;
use App\Handler\GenerateHandler;
use App\SniffFinder\SniffFinder;
use App\Value\Folder;
use App\Value\Sniff;
use App\Value\UrlList;
use App\Value\Violation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/** @covers \App\Handler\GenerateHandler */
class GenerateHandlerTest extends TestCase
{
    /**
     * @var GenerateHandler
     */
    private GenerateHandler $handler;
    /**
     * @var CodeRepository|MockObject
     */
    private $codeRepository;
    /**
     * @var Generator|MockObject
     */
    private $generator;
    /**
     * @var SniffFinder|MockObject
     */
    private $sniffFinder;
    /**
     * @var ConfigurationRepository|MockObject
     */
    private $configRepo;

    /** @test */
    public function handle_WithoutArguments_CreatesFile()
    {
        $this->codeRepository->method('getFolder')->willReturn(new Folder('var/tests/'));
        $sniffs = $this->createSniffs(['First', 'Second']);
        $this->sniffFinder->method('getSniffs')->willReturn($sniffs);
        $this->generator->method('createSniffDoc')->withConsecutive([$sniffs[0]], [$sniffs[1]]);

        /** @var \Generator $messages */
        $messages = $this->handler->handle();

        self::assertEquals(
            [
                'Searching for sniffs in var/tests/Standard/...',
                'Created file: var/markdown/Standard/Category/First.md',
                'Created file: var/markdown/Standard/Category/Second.md'
            ],
            iterator_to_array($messages)
        );
    }

    /**
     * @param string[] $names
     */
    private function createSniffs(array $names): iterable
    {
        return array_map(function (string $name) {
            return $this->createSniff($name);
        }, $names);
    }

    private function createSniff(string $name): Sniff
    {
        return new Sniff(
            'Standard.Category.' . $name,
            '',
            [],
            new UrlList([]),
            'Description',
            [],
            [
                new Violation(
                    'Standard.Category.' . $name . '.ErrorCode',
                    'Description',
                    [],
                    new UrlList([])
                )
            ]
        );
    }

    /** @test */
    public function handle_WithSniffPath_CreatesSingleFile()
    {
        $this->codeRepository->method('getFolder')->willReturn(new Folder('var/tests/'));
        $this->sniffFinder->method('getSniff')->willReturn($this->createSniff('First'));

        /** @var \Generator $messages */
        $messages = $this->handler->handle('var/tests/Standard/Category/Sniffs/FirstSniff.php');

        self::assertEquals(
            [
                'Searching for sniffs in var/tests/Standard/...',
                'Created file: var/markdown/Standard/Category/First.md',
            ],
            iterator_to_array($messages)
        );
    }

    protected function setUp(): void
    {
        (new Filesystem())->remove('var/markdown/Standard');

        $this->codeRepository = $this->createMock(CodeRepository::class);
        $this->generator = $this->createMock(Generator::class);
        $this->sniffFinder = $this->createMock(SniffFinder::class);
        $this->configRepo = $this->createMock(ConfigurationRepository::class);

        $this->configRepo->method('getConfig')->willReturn(new Configuration(
            'markdown',
            [
                new Source('../Standard', [
                    new Standard('Standard')
                ])
            ]
        ));

        $this->handler = new GenerateHandler(
            $this->codeRepository,
            $this->generator,
            $this->sniffFinder,
            $this->configRepo
        );
    }
}
