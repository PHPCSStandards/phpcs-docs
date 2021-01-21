<?php
declare(strict_types=1);

namespace App\Tests\Value;

use App\Value\Folder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\Value\Folder */
class FolderTest extends TestCase
{
    const PATH = 'path/to/folder/';

    /** @test */
    public function constructor_WithMissingBackslash_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Folder('path/to/folder');
    }

    /** @test */
    public function getPath()
    {
        self::assertEquals(
            self::PATH,
            (string)$this->createValidFolder()
        );
    }

    /**
     * @return Folder
     */
    private function createValidFolder(): Folder
    {
        return new Folder(self::PATH);
    }
}
