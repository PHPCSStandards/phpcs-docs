<?php
declare(strict_types=1);

namespace App\Tests\Handler;

use App\CodeRepository\CodeRepository;
use App\Generator\Generator;
use App\Handler\GenerateHandler;
use App\SniffFinder\SniffFinder;
use App\Value\Folder;
use App\Value\Sniff;
use App\Value\Urls;
use App\Value\Violation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

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

    protected function setUp(): void
    {
        $this->codeRepository = $this->createMock(CodeRepository::class);
        $this->generator = $this->createMock(Generator::class);
        $this->sniffFinder = $this->createMock(SniffFinder::class);

        $this->handler = new GenerateHandler(
            $this->codeRepository,
            $this->generator,
            $this->sniffFinder
        );
    }

    /** @test */
    public function handle()
    {
        $this->codeRepository->method('downloadCode')->willReturn(new Folder('var/tests/src/'));
        $this->sniffFinder->method('getSniffs')->willReturn($this->createSniffs());

        $this->handler->handle();

        self::assertFileExists('var/markdown/Standard/Category/My.md');
        self::assertFileExists('var/markdown/Standard/Category/My/ErrorCode.md');
    }

    private function createSniffs()
    {
        yield new Sniff(
            'Standard.Category.My',
            '',
            [],
            new Urls([]),
            'Description',
            [],
            [
                new Violation(
                    'Standard.Category.My.ErrorCode',
                    'Description',
                    [],
                    new Urls([])
                )
            ]
        );
    }
}
