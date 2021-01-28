<?php
declare(strict_types=1);

namespace App\Tests\Configuration\Value;

use App\Configuration\Value\Source;
use App\Configuration\Value\Standard;
use App\Value\Folder;
use PHPUnit\Framework\TestCase;

/** @covers \App\Configuration\Value\Source */
class SourceTest extends TestCase
{
    private const SOURCE_PATH = 'path/to/source';
    private const STANDARD_PATH = 'path/to/standard';

    /** @test */
    public function getStandards()
    {
        self::assertEquals(
            [
                new Standard(self::STANDARD_PATH)
            ],
            (new Source(self::SOURCE_PATH, [
                new Standard(self::STANDARD_PATH)
            ]))->getStandards()
        );
    }

    /** @test */
    public function getLocalFolder_WithGit_PrependDownloadPath()
    {
        self::assertEquals(
            new Folder('var/repos/repo/'),
            (new Source('path/to/repo.git', []))->getLocalFolder()
        );
    }

    /** @test */
    public function getType_WithGit_ReturnGit()
    {
        self::assertEquals(
            Source::TYPE_GIT,
            (new Source('path/to/repo.git', []))->getType()
        );
    }

    /** @test */
    public function getPath()
    {
        self::assertEquals(
            self::SOURCE_PATH,
            (new Source(self::SOURCE_PATH, []))->getPath()
        );
    }
}
