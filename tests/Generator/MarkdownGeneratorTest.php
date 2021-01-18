<?php
declare(strict_types=1);

namespace App\Tests\Generator;

use App\Generator\MarkdownGenerator;
use App\Value\Diff;
use App\Value\Property;
use App\Value\Sniff;
use App\Value\Url;
use App\Value\Urls;
use App\Value\Violation;
use PHPUnit\Framework\TestCase;

/** @covers \App\Generator\MarkdownGenerator */
class MarkdownGeneratorTest extends TestCase
{
    private MarkdownGenerator $generator;

    /** @test */
    public function fromSniff()
    {
        $doc = new Sniff(
            'Generic.MySniff',
            'DocBlock',
            [
                new Property('a', 'string', 'DescriptionA'),
                new Property('b', 'int', 'DescriptionB')
            ],
            new Urls([
                new Url('http://link1.com'),
                new Url('http://link2.com')
            ]),
            'Description',
            [],
            [
                new Violation(
                    'Generic.MySniff.ErrorCode',
                    'Description',
                    [
                        new Diff('a();', 'b();'),
                        new Diff('a();', 'b();')
                    ],
                    new Urls([
                        new Url('http://link1.com'),
                        new Url('http://link2.com')
                    ])
                )
            ]
        );

        self::assertEquals(
            <<<MD
            # Generic.MySniff
            
            Description
            
            ## Docblock
            
            DocBlock
            
            ## Public Properties
            
            - \$a : string DescriptionA
            - \$b : int DescriptionB
            
            ## See Also
            
            - [http://link1.com](http://link1.com)
            - [http://link2.com](http://link2.com)
            
            ## Troubleshooting
            
            ```
            <details>
            <summary>Generic.MySniff.ErrorCode</summary>
            Description
            
            ## Comparisons
            
            ```diff
            -a();
            +b();
            ```
            
            ```diff
            -a();
            +b();
            ```
            
            ## See Also
            
            - [http://link1.com](http://link1.com)
            - [http://link2.com](http://link2.com)
            
            </details>
            ```
            
            MD,
            $this->generator->createSniffDoc($doc)
        );
    }

    protected function setUp(): void
    {
        $this->generator = new MarkdownGenerator();
    }
}
