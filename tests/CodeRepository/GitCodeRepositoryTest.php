<?php
declare(strict_types=1);

namespace App\Tests\CodeRepository;

use App\CodeRepository\GitCodeRepository;
use App\Configuration\Value\Source;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Process\Exception\RuntimeException;

/** @covers \App\CodeRepository\GitCodeRepository */
class GitCodeRepositoryTest extends TestCase
{
    const LOCAL_GIT_PATH = 'var/tests/repo/Standard.git';
    private GitCodeRepository $codeRepo;

    /** @test */
    public function getFolder_WithMissingPath_ThrowException()
    {
        $this->expectException(RuntimeException::class);
        $sourcePath = 'var/repos/MISSING.git';
        $this->codeRepo->getFolder(new Source($sourcePath, []));
    }

    /** @test */
    public function getFolder_WithGitPath_InstallComposer()
    {
        $this->createGitRepo();
        (new Filesystem())->remove('var/repos/Standard'); // force fresh clone

        $this->codeRepo->getFolder(new Source(self::LOCAL_GIT_PATH, []));
        self::assertFileExists('var/repos/Standard/composer.lock');
    }

    private function createGitRepo(): void
    {
        $fs = new Filesystem;
        $gitPath = self::LOCAL_GIT_PATH;
        $fs->mkdir($gitPath);
        $fs->dumpFile($gitPath . '/composer.json', '{}');
        `cd {$gitPath} && git init && git add composer.json && git commit -m "Init"`;
    }

    /** @test */
    public function getFolder_WithExistingGitClone_InstallComposer()
    {
        $this->createGitRepo();

        $this->codeRepo->getFolder(new Source(self::LOCAL_GIT_PATH, []));
        self::assertFileExists('var/repos/Standard/composer.lock');
    }

    protected function setUp(): void
    {
        $this->codeRepo = new GitCodeRepository();
    }
}
