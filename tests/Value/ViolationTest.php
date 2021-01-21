<?php
declare(strict_types=1);

namespace App\Tests\Value;

use App\Value\Diff;
use App\Value\Url;
use App\Value\UrlList;
use App\Value\Violation;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\Value\Violation */
class ViolationTest extends TestCase
{
    const DESCRIPTION = 'description';
    const CODE = 'Standard.Category.My.ErrorCode';
    /**
     * @var Url[]
     */
    private array $URLS;
    /**
     * @var Diff[]
     */
    private array $DIFFS;

    /** @test */
    public function constructor_WithBlankName_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        new Violation('', self::DESCRIPTION, [], new UrlList([]));
    }

    /** @test */
    public function getCode()
    {
        self::assertEquals(
            self::CODE,
            $this->createViolation()->getCode()
        );
    }

    private function createViolation(): Violation
    {
        return new Violation(
            self::CODE,
            self::DESCRIPTION,
            $this->DIFFS,
            new UrlList($this->URLS)
        );
    }

    /** @test */
    public function getDiffs()
    {
        self::assertEquals(
            $this->DIFFS,
            $this->createViolation()->getDiffs()
        );
    }

    /** @test */
    public function getDescription()
    {
        self::assertEquals(
            self::DESCRIPTION,
            $this->createViolation()->getDescription()
        );
    }

    /** @test */
    public function getUrls()
    {
        self::assertEquals(
            $this->URLS,
            $this->createViolation()->getUrls()->toArray()
        );
    }

    protected function setUp(): void
    {
        $this->URLS = [
            new Url('https://link.com')
        ];
        $this->DIFFS = [
            new Diff('a();', 'b();')
        ];
    }
}
