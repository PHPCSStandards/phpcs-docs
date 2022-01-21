<?php
declare(strict_types=1);

namespace App\Tests\Generator;

use App\Generator\Formatter\MarkdownFormatter;
use App\Generator\MarkdownGenerator;
use App\Value\Diff;
use App\Value\Property;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\UrlList;
use App\Value\Violation;
use PHPUnit\Framework\TestCase;

/** @covers \App\Generator\MarkdownGenerator */
class MarkdownGeneratorTest extends TestCase
{
    private MarkdownGenerator $generator;

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

        self::assertEquals(
            <<<MD
            # Standard.Category.My
            
            MD,
            $this->generator->createSniffDoc($doc)
        );
    }

    /** @test */
    public function createSniffDoc_WithCompleteData_WriteAllDetails()
    {
        $doc = new Sniff(
            'Standard.Category.My',
            'DocBlock',
            [
                new Property('a', 'string', 'DescriptionA'),
            ],
            new UrlList([
                new Url('http://link1.com')
            ]),
            'Description',
            [],
            [
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
            ]
        );

        self::assertEquals(
            <<<'MD'
            # Standard.Category.My
            
            Description
            
            ## Docblock
            
            DocBlock
            
            ## Public Properties
            
            - `$a` : string DescriptionA
            
            ## See Also
            
            - [http://link1.com](http://link1.com)
            
            ## Troubleshooting
            
            ```
            <details>
            <summary>Standard.Category.My.ErrorCode</summary>
            Description
            
            ## Comparisons
            
            ```diff
            -a();
            +b();
            ```
            
            ## See Also
            
            - [http://link1.com](http://link1.com)
            </details>
            ```
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
        $this->generator = new MarkdownGenerator(new MarkdownFormatter());
    }
}
