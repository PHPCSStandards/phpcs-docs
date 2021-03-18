<?php
declare(strict_types=1);

namespace App\Tests\Utils;

use App\Util\Functions;
use PHPUnit\Framework\TestCase;

/** @covers \App\Util\Functions */
class FunctionsTest extends TestCase
{

    /**
     * Verify slash normalization.
     *
     * @dataProvider dataNormalizeSlashes
     *
     * @param string $input    Path to normalize.
     * @param string $expected Expected normalized path.
     *
     * @return void
     */
    public function testNormalizeSlashes($input, $expected)
    {
        self::assertSame($expected, Functions::normalizeSlashes($input));
    }

    /**
     * Data provider.
     *
     * @return array
     */
    public function dataNormalizeSlashes()
    {
        return [
            'unix_slashes' => [
                'path/to/folder/filename.php',
                'path/to/folder/filename.php',
            ],
            'windows_slashes' => [
                'path\to\folder\filename.php',
                'path/to/folder/filename.php',
            ],
            'mixed_slashes' => [
                'path\to/folder\filename.php',
                'path/to/folder/filename.php',
            ],
        ];
    }
}
