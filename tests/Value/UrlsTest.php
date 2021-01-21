<?php
declare(strict_types=1);

namespace App\Tests\Value;

use App\Value\Url;
use App\Value\UrlList;
use PHPUnit\Framework\TestCase;

/** @covers \App\Value\UrlList */
class UrlsTest extends TestCase
{
    /** @test */
    public function toArray_WithDuplicateValues_Deduplicate()
    {
        self::assertEquals(
            [
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ],
            (new UrlList([
                new Url('http://link1.com'),
                new Url('http://link1.com'),
                new Url('http://link2.com'),
            ]))->toArray()
        );
    }
}
