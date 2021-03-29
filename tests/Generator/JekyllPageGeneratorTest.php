<?php
declare(strict_types=1);

namespace App\Tests\Generator;

use App\Generator\JekyllPageGenerator;
use App\Value\Sniff;
use App\Value\UrlList;
use PHPUnit\Framework\TestCase;

/** @covers \App\Generator\JekyllPage */
class JekyllPageGeneratorTest extends TestCase
{
    private JekyllPageGenerator $generator;

    /** @test */
    public function fromSniff_WithMinimalData_WriteMinimalDetails()
    {
        $doc = new Sniff(
            'Standard.Category.My',
            '',
            [],
            new UrlList([]),
            '',
            [],
            []
        );

        self::assertSame(
            <<<MD
            ---
            title: My
            ---

            # Standard.Category.My

            MD,
            $this->generator->createSniffDoc($doc)
        );
    }

    protected function setUp(): void
    {
        $this->generator = new JekyllPageGenerator();
    }
}
