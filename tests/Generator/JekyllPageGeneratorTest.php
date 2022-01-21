<?php
declare(strict_types=1);

namespace App\Tests\Generator;

use App\Generator\Formatter\MarkdownFormatter;
use App\Generator\JekyllPageGenerator;
use App\Value\Diff;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\UrlList;
use App\Value\Violation;
use PHPUnit\Framework\TestCase;

/** @covers \App\Generator\JekyllPageGenerator */
class JekyllPageGeneratorTest extends TestCase
{
    private JekyllPageGenerator $generator;

    /** @test */
    public function createSniffDoc_WithMinimalData_WriteMinimalDetails()
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

    /** @test */
    public function createViolationDoc_WriteMinimalDetails()
    {
        self::assertEquals(
            <<<'MD'
            Description
            
            ## Comparisons
            
            ```diff
            -a();
            +b();
            ```
            
            ## See Also
            
            - [http://link1.com](http://link1.com)
            MD,
            $this->generator->createViolationDoc(
                new Violation(
                    'Standard.Category.My.ErrorCode',
                    'Description',
                    [
                        new Diff('a();', 'b();'),
                    ],
                    new UrlList([
                        new Url('http://link1.com')
                    ])
                )
            )
        );
    }

    protected function setUp(): void
    {
        $this->generator = new JekyllPageGenerator(new MarkdownFormatter());
    }
}
