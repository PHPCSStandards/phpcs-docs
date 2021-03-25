<?php
declare(strict_types=1);

namespace App\Tests\Value;

use App\Value\Diff;
use App\Value\Property;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\UrlList;
use App\Value\Violation;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

/** @covers \App\Value\Sniff */
class SniffTest extends TestCase
{
    const CODE = 'Standard.Category.Code';
    const STANDARD = 'Standard';
    const CATEGORY = 'Category';
    const SNIFFNAME = 'Code';
    const DOCBLOCK = 'Docblock';
    const DESCRIPTION = 'Description';
    /**
     * @var Property[]
     */
    private array $PROPERTIES;
    /**
     * @var Url[]
     */
    private array $URLS;
    /**
     * @var Diff[]
     */
    private array $DIFFS;
    /**
     * @var Violation[]
     */
    private array $VIOLATIONS;

    /** @test */
    public function constructor_WithBlankCode_ThrowException()
    {
        $this->expectException(InvalidArgumentException::class);
        $params = $this->getValidParams();
        $params[0] = '';
        new Sniff(...$params);
    }

    private function getValidParams(): array
    {
        return [
            self::CODE,
            self::DOCBLOCK,
            $this->PROPERTIES,
            new UrlList($this->URLS),
            self::DESCRIPTION,
            $this->DIFFS,
            $this->VIOLATIONS
        ];
    }

    /** @test */
    public function getProperties()
    {
        $properties = [
            new Property('name', 'int', 'description')
        ];
        self::assertEquals(
            $properties,
            $this->createSniff()->getProperties()
        );
    }

    private function createSniff(): Sniff
    {
        return new Sniff(...$this->getValidParams());
    }

    /** @test */
    public function getDocblock()
    {
        self::assertEquals(
            self::DOCBLOCK,
            $this->createSniff()->getDocblock()
        );
    }

    /** @test */
    public function getDescription()
    {
        self::assertEquals(
            self::DESCRIPTION,
            $this->createSniff()->getDescription()
        );
    }

    /** @test */
    public function getViolations()
    {
        self::assertEquals(
            $this->VIOLATIONS,
            $this->createSniff()->getViolations()
        );
    }

    /** @test */
    public function getDiffs()
    {
        self::assertEquals(
            $this->DIFFS,
            $this->createSniff()->getDiffs()
        );
    }

    /** @test */
    public function getUrls()
    {
        self::assertEquals(
            $this->URLS,
            $this->createSniff()->getUrls()->toArray()
        );
    }

    /** @test */
    public function getCode()
    {
        self::assertEquals(
            self::CODE,
            $this->createSniff()->getCode()
        );
    }

    /** @test */
    public function getStandardName()
    {
        self::assertSame(
            self::STANDARD,
            $this->createSniff()->getStandardName()
        );
    }

    /** @test */
    public function getCategoryName()
    {
        self::assertSame(
            self::CATEGORY,
            $this->createSniff()->getCategoryName()
        );
    }

    /** @test */
    public function getSniffName()
    {
        self::assertSame(
            self::SNIFFNAME,
            $this->createSniff()->getSniffName()
        );
    }

    protected function setUp(): void
    {
        $this->PROPERTIES = [
            new Property('name', 'int', 'description')
        ];
        $this->URLS = [
            new Url('https://link.com')
        ];
        $this->DIFFS = [
            new Diff('a();', 'b();')
        ];
        $this->VIOLATIONS = [
            new Violation('code', '', [], new UrlList([]))
        ];
    }
}
