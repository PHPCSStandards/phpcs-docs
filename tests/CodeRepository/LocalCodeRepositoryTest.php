<?php
declare(strict_types=1);

namespace App\Tests\CodeRepository;

use App\CodeRepository\LocalCodeRepository;
use App\Configuration\Value\Source;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\RuntimeException;

/** @covers \App\CodeRepository\LocalCodeRepository */
class LocalCodeRepositoryTest extends TestCase
{
    private const LOCAL_PATH = 'var/repos/Standard';
    private LocalCodeRepository $codeRepo;

    /** @test */
    public function getFolder_WithMissingPath_ThrowException()
    {
        $this->expectException(RuntimeException::class);
        $sourcePath = 'var/repos/MISSING';
        $this->codeRepo->getFolder(new Source($sourcePath, []));
    }

    /** @test */
    public function getFolder_WithExistingPath_InstallComposer()
    {
        $fs = new Filesystem;
        $sourcePath = self::LOCAL_PATH;
        $fs->remove($sourcePath);
        $fs->mkdir($sourcePath);
        $fs->dumpFile($sourcePath . '/composer.json', '{}');

        $this->codeRepo->getFolder(new Source($sourcePath, []));
        self::assertFileExists('var/repos/Standard/composer.lock');
    }

    protected function setUp(): void
    {
        $this->codeRepo = new LocalCodeRepository();
    }
}
